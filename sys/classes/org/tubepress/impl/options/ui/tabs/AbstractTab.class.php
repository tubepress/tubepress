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
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_api_message_MessageService',
    'org_tubepress_api_template_TemplateBuilder',
    'org_tubepress_impl_options_ui_AbstractDelegatingFormHandler',
    'org_tubepress_spi_options_ui_Tab',
	'org_tubepress_spi_options_ui_FieldBuilder'
));

/**
 * Displays a tab on the options page.
 */
abstract class org_tubepress_impl_options_ui_tabs_AbstractTab extends org_tubepress_impl_options_ui_AbstractDelegatingFormHandler implements org_tubepress_spi_options_ui_Tab
{
    const TEMPLATE_VAR_WIDGETARRAY = 'org_tubepress_impl_options_ui_tabs_AbstractTab__widgetArray';

    public function getTitle()
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $messageService = $ioc->get(org_tubepress_api_message_MessageService::_);

        return $messageService->_($this->doGetTitle());
    }

    public function getHtml()
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $messageService = $ioc->get(org_tubepress_api_message_MessageService::_);
        $templateBldr   = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse            = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $basePath       = $fse->getTubePressBaseInstallationPath();
        $template       = $templateBldr->getNewTemplateInstance($basePath . $this->getTemplatePath());

        $template->setVariable(self::TEMPLATE_VAR_WIDGETARRAY, $this->getDelegateFormHandlers());

        $this->addToTemplate($template);
        
        return $template->toString();
    }

    protected abstract function doGetTitle();
    
    protected function addToTemplate(org_tubepress_api_template_Template $template)
    {
        
    }
    
    protected function getTemplatePath()
    {
        return '/sys/ui/templates/options_page/tab.tpl.php';
    }

}