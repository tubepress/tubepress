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
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_options_OptionDescriptorReference',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_api_message_MessageService',
    'org_tubepress_spi_options_ui_Widget'
));

/**
 * Base class for HTML widgets.
 */
abstract class org_tubepress_impl_options_ui_widgets_AbstractWidget implements org_tubepress_spi_options_ui_Widget
{
    const TEMPLATE_VAR_NAME  = 'org_tubepress_impl_options_ui_widgets_AbstractWidget__name';
    const TEMPLATE_VAR_VALUE = 'org_tubepress_impl_options_ui_widgets_AbstractWidget__value';

    /** Applicable providers. */
    private $_providerArray = array();

    /** Message service. */
    private $_messageService;

    /** Option descriptor. */
    private $_optionDescriptor;

    public function __construct($name)
    {
        $ioc                     = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_messageService   = $ioc->get(org_tubepress_api_message_MessageService::_);
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

    public function getTitle()
    {
        return $this->_messageService->_($this->_optionDescriptor->getLabel());
    }

    public function getDescription()
    {
        return $this->_messageService->_($this->_optionDescriptor->getDescription());
    }

    public function isProOnly()
    {
        return $this->_optionDescriptor->isProOnly();
    }

    public function getInputHtml()
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

    protected abstract function getTemplatePath();

    protected function populateTemplate($template, $currentValue)
    {
         //override point
    }

    protected function getOptionDescriptor()
    {
        return $this->_optionDescriptor;
    }

    protected function getMessageService()
    {
        return $this->_messageService;
    }
}