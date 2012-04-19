<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_spi_options_ui_Field',
    'org_tubepress_impl_options_ui_fields_AbstractField',
));

/**
 * Displays a multi-select drop-down input.
 */
abstract class org_tubepress_impl_options_ui_fields_AbstractMultiSelectField extends org_tubepress_impl_options_ui_fields_AbstractField
{
    const TEMPLATE_VAR_DESCRIPTORS = 'org_tubepress_impl_options_ui_fields_AbstractMultiSelectField__descriptors';

    const TEMPLATE_VAR_CURRENTVALUES = 'org_tubepress_impl_options_ui_fields_AbstractMultiSelectField__currentValues';

    /** Array of option descriptors. */
    private $_optionDescriptors;

    /** Name. */
    private $_name;

    public function __construct($optionDescriptors, $name, $description = '')
    {
        parent::__construct();

        if (! is_array($optionDescriptors)) {

            throw new Exception('Option descriptors must be an array');
        }

        foreach ($optionDescriptors as $optionDescriptor) {

            if (! $optionDescriptor instanceof org_tubepress_api_options_OptionDescriptor) {

                throw new Exception('Non option descriptor detected');
            }

            if (! $optionDescriptor->isBoolean()) {

                throw new Exception('Non-boolean option descriptor detected');
            }
        }

        if (! is_string($name)) {

            throw new Exception('Label must be a string');
        }

        $this->_optionDescriptors = $optionDescriptors;
        $this->_name              = $name;
    }

    /**
     * Handles form submission.
     *
     * @return An array of failure messages if there's a problem, otherwise null.
     */
    function onSubmit()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $sm  = $ioc->get(org_tubepress_api_options_StorageManager::_);

        $hrps = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);

        if (! $hrps->hasParam($this->_name)) {

            /* not submitted. */
            foreach ($this->_optionDescriptors as $optionDescriptor) {

                $sm->set($optionDescriptor->getName(), false);
            }

            return null;
        }

        $vals = $hrps->getParamValue($this->_name);

        if (! is_array($vals)) {

            /* this should never happen. */
            return null;
        }

        $errors = array();

        foreach ($this->_optionDescriptors as $optionDescriptor) {

            $result = $sm->set($optionDescriptor->getName(), in_array($optionDescriptor->getName(), $vals));

            if ($result !== true) {

                $errors[] = $result;
            }
        }

        if (count($errors) === 0) {

            return null;
        }

        return $errors;
    }

    function getHtml()
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $templateBldr  = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse           = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $basePath      = $fse->getTubePressBaseInstallationPath();
        $template      = $templateBldr->getNewTemplateInstance($basePath . '/sys/ui/templates/options_page/fields/multiselect.tpl.php');
        $sm            = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $currentValues = array();

        foreach ($this->_optionDescriptors as $optionDescriptor) {

            if ($sm->get($optionDescriptor->getName())) {

                $currentValues[] = $optionDescriptor->getName();
            }
        }
        $template->setVariable(self::TEMPLATE_VAR_NAME, $this->_name);
        $template->setVariable(self::TEMPLATE_VAR_DESCRIPTORS, $this->_optionDescriptors);
        $template->setVariable(self::TEMPLATE_VAR_CURRENTVALUES, $currentValues);

        return $template->toString();
    }

    function isProOnly()
    {
        return false;
    }

    function getArrayOfApplicableProviderNames()
    {
        return array(

            org_tubepress_api_provider_Provider::YOUTUBE,
            org_tubepress_api_provider_Provider::VIMEO,
        );
    }
}