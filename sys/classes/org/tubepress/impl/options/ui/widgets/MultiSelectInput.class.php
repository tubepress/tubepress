<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_spi_options_ui_Widget',
));

/**
 * Displays a multi-select drop-down input.
 */
class org_tubepress_impl_options_ui_widgets_MultiSelectInput implements org_tubepress_spi_options_ui_Widget
{
    const TEMPLATE_VAR_NAME = 'org_tubepress_impl_options_ui_widgets_MultiSelectInput__name';

    const TEMPLATE_VAR_DESCRIPTORS = 'org_tubepress_impl_options_ui_widgets_MultiSelectInput__descriptors';

    const TEMPLATE_VAR_CURRENTVALUES = 'org_tubepress_impl_options_ui_widgets_MultiSelectInput__currentValues';

    /** Array of option descriptors. */
    private $_optionDescriptors;

    /** Label. */
    private $_label;

    /** Description. */
    private $_description;

    public function __construct($optionDescriptors, $label, $description = '')
    {
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

        if (! is_string($label)) {

            throw new Exception('Label must be a string');
        }

        $this->_optionDescriptors = $optionDescriptors;
        $this->_label             = $label;
        $this->_description       = $description;
    }

    function getTitle()
    {
        return $this->_label;
    }

    function getDescription()
    {
        return $this->_description;
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

    function onSubmit($postVars)
    {
        if (! array_key_exists($this->_label, $postVars)) {

            /* not submitted. */
            return;
        }

        $vals = $postVars[$this->_label];

        if (! is_array($vals)) {

            /* this should never happen. */
            return;
        }
        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $sm   = $ioc->get(org_tubepress_api_options_StorageManager::_);

        foreach ($this->_optionDescriptors as $optionDescriptor) {

            $sm->set($optionDescriptor->getName(), in_array($optionDescriptor->getName(), $vals));
        }
    }

    function getHtml()
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $templateBldr  = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse           = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $basePath      = $fse->getTubePressBaseInstallationPath();
        $template      = $templateBldr->getNewTemplateInstance($basePath . '/sys/ui/templates/options_page/widgets/multiselect.tpl.php');
        $sm            = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $currentValues = array();

        foreach ($this->_optionDescriptors as $optionDescriptor) {

            if ($sm->get($optionDescriptor->getName())) {

                $currentValues[] = $optionDescriptor->getName();
            }
        }

        $template->setVariable(self::TEMPLATE_VAR_NAME, $this->_label);
        $template->setVariable(self::TEMPLATE_VAR_DESCRIPTORS, $this->_optionDescriptors);
        $template->setVariable(self::TEMPLATE_VAR_CURRENTVALUES, $currentValues);

        return $template->toString();
    }
}