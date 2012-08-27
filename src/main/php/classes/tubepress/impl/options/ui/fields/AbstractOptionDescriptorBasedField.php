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
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_options_OptionDescriptorReference',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_api_message_MessageService',
    'org_tubepress_impl_options_ui_fields_AbstractField'
));

/**
 * Base class for HTML fields.
 */
abstract class org_tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField extends org_tubepress_impl_options_ui_fields_AbstractField
{
    const TEMPLATE_VAR_VALUE = 'org_tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField__value';

    /** Applicable providers. */
    private $_providerArray = array();

    /** Option descriptor. */
    private $_optionDescriptor;

    public function __construct($name)
    {
        parent::__construct();

        $ioc                     = org_tubepress_impl_ioc_IocContainer::getInstance();
        $odr                     = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
        $this->_optionDescriptor = $odr->findOneByName($name);

        if ($this->_optionDescriptor === null) {

            throw new Exception('Could not find option with name "%s"');
        }

        if ($this->_optionDescriptor->isApplicableToVimeo()) {

            array_push($this->_providerArray, org_tubepress_api_provider_Provider::VIMEO);
        }

        if ($this->_optionDescriptor->isApplicableToYouTube()) {

            array_push($this->_providerArray, org_tubepress_api_provider_Provider::YOUTUBE);
        }
    }

    public function getArrayOfApplicableProviderNames()
    {
        return $this->_providerArray;
    }

    public function getRawTitle()
    {
        return $this->_optionDescriptor->getLabel();
    }

    public function getRawDescription()
    {
        return $this->_optionDescriptor->getDescription();
    }

    public function isProOnly()
    {
        return $this->_optionDescriptor->isProOnly();
    }

    public function getHtml()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $templateBldr = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse          = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $basePath     = $fse->getTubePressBaseInstallationPath();
        $template     = $templateBldr->getNewTemplateInstance($basePath . '/' . $this->getTemplatePath());
        $sm           = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $currentValue = $sm->get($this->_optionDescriptor->getName());

        $template->setVariable(self::TEMPLATE_VAR_NAME, $this->_optionDescriptor->getName());
        $template->setVariable(self::TEMPLATE_VAR_VALUE, $currentValue);

        $this->populateTemplate($template, $currentValue);

        return $template->toString();
    }

    /**
     * Handles form submission.
     *
     * @return An array of failure messages if there's a problem, otherwise null.
     */
    public function onSubmit()
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $storageManager = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $hrps           = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);

        if ($this->_optionDescriptor->isBoolean()) {

            return $this->_onSubmitBoolean($storageManager, $hrps);
        }

        return $this->_onSubmitSimple($storageManager, $hrps);
    }

    private function _onSubmitSimple(org_tubepress_api_options_StorageManager $storageManager, org_tubepress_api_http_HttpRequestParameterService $hrps)
    {
        $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $validator = $ioc->get(org_tubepress_api_options_OptionValidator::_);
        $name      = $this->_optionDescriptor->getName();

        if (! $hrps->hasParam($name)) {

            /* not submitted. */
            return null;
        }

        $value = $hrps->getParamValue($name);

        /* run it through validation. */
        if (! $validator->isValid($name, $value)) {

            return array($validator->getProblemMessage($name, $value));
        }

        return $this->_setToStorage($storageManager, $name, $value);
    }

    private function _onSubmitBoolean(org_tubepress_api_options_StorageManager $storageManager, org_tubepress_api_http_HttpRequestParameterService $hrps)
    {
        $name = $this->_optionDescriptor->getName();

        /* if the user checked the box, the option name will appear in the POST vars */
        return $this->_setToStorage($storageManager, $name, $hrps->hasParam($name));
    }

    protected abstract function getTemplatePath();

    protected function populateTemplate($template, $currentValue)
    {
         //override point
    }

    protected function getOptionDescriptor()
    {
        return $this->_optionDescriptor;
    }

    private function _setToStorage(org_tubepress_api_options_StorageManager $storageManager, $name, $value)
    {
        $result = $storageManager->set($name, $value);

        if ($result === true) {

            return null;
        }

        return array($result);
    }
}