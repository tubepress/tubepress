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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_CategoryName',
    'org_tubepress_api_const_options_Type',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_api_message_MessageService',
    'org_tubepress_api_template_Template',
    'org_tubepress_impl_options_ui_AbstractDelegatingFormHandler',
    'org_tubepress_impl_options_ui_DefaultTabsHandler',
    'org_tubepress_impl_options_ui_fields_FilterMultiSelectField',
));

/**
 * Displays a generic options form for TubePress
 *
 */
abstract class org_tubepress_impl_options_ui_AbstractFormHandler extends org_tubepress_impl_options_ui_AbstractDelegatingFormHandler
{
    const TEMPLATE_VAR_TITLE     = 'optionsPageTitle';
    const TEMPLATE_VAR_INTRO     = 'optionsPageIntro';
    const TEMPLATE_VAR_TABS      = 'optionsPageTabs';
    const TEMPLATE_VAR_SAVE_TEXT = 'optionsPageSaveText';
    const TEMPLATE_VAR_SAVE_ID   = 'optionsPageSaveId';
    const TEMPLATE_VAR_FILTER    = 'optionsPageFilter';

    /**
     * Displays all the TubePress options in HTML
     *
     * @return string The HTML for the options page.
     */
    public function getHtml()
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $messageService = $ioc->get(org_tubepress_api_message_MessageService::_);
        $templateBldr   = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse            = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $tabs           = $ioc->get(org_tubepress_impl_options_ui_DefaultTabsHandler::__);
        $filter         = $ioc->get(org_tubepress_impl_options_ui_fields_FilterMultiSelectField::__);
        $basePath       = $fse->getTubePressBaseInstallationPath();
        $template       = $templateBldr->getNewTemplateInstance($basePath . '/' . $this->getRelativeTemplatePath());
        
        $template->setVariable(self::TEMPLATE_VAR_TITLE, $messageService->_('TubePress Options'));                                                                                                                                                                                                                                                                                                                                 //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_INTRO, $messageService->_('Set default options for the plugin. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more info. An asterisk (*) next to an option indicates it\'s only available with <a href="http://tubepress.org/features">TubePress Pro</a>.')); //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_SAVE_TEXT, $messageService->_('Save'));                                                                                                                                                                                                                                                                                                                                          //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_SAVE_ID, 'tubepress_save');
        $template->setVariable(self::TEMPLATE_VAR_TABS, $tabs->getHtml());
        $template->setVariable(self::TEMPLATE_VAR_FILTER, $filter);

        return $template->toString();
    }

    protected function getDelegateFormHandlers()
    {
        $ioc    = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tabs   = $ioc->get(org_tubepress_impl_options_ui_DefaultTabsHandler::__);
        $filter = $ioc->get(org_tubepress_impl_options_ui_fields_FilterMultiSelectField::__);

        return array($tabs, $filter);
    }

    protected abstract function getRelativeTemplatePath();
}
