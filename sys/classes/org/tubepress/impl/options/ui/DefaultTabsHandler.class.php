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
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_options_ui_FormHandler',
    'org_tubepress_api_template_Template',
    'org_tubepress_api_template_TemplateBuilder',
    'org_tubepress_impl_options_ui_AbstractDelegatingFormHandler',
    'org_tubepress_impl_options_ui_tabs_GallerySourceTab',
    'org_tubepress_impl_options_ui_tabs_ThumbsTab',
	'org_tubepress_impl_options_ui_tabs_CacheTab',
	'org_tubepress_impl_options_ui_tabs_AdvancedTab',
	'org_tubepress_impl_options_ui_tabs_EmbeddedTab',
	'org_tubepress_impl_options_ui_tabs_FeedTab',
    'org_tubepress_impl_options_ui_tabs_ThemeTab',
	'org_tubepress_impl_options_ui_tabs_MetaTab',
));

/**
 * Generates the "meat" of the options form (in the form of tabs).
 */
class org_tubepress_impl_options_ui_DefaultTabsHandler extends org_tubepress_impl_options_ui_AbstractDelegatingFormHandler implements org_tubepress_api_options_ui_FormHandler
{
    const __ = 'org_tubepress_impl_options_ui_DefaultTabsHandler';

    const TEMPLATE_VAR_TABS = 'org_tubepress_impl_options_ui_DefaultTabsHandler__tabs';

    /**
     * Generates the HTML for the "meat" of the options form.
     *
     * @return string The HTML for the options form.
     */
    public function getHtml()
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $templateBldr   = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse            = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $basePath       = $fse->getTubePressBaseInstallationPath();
        $template       = $templateBldr->getNewTemplateInstance("$basePath/sys/ui/templates/options_page/tabs.tpl.php");
        $tabs           = $this->getDelegateFormHandlers();

        $template->setVariable(self::TEMPLATE_VAR_TABS, $tabs);

        return $template->toString();
    }

    protected function getDelegateFormHandlers()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        return array(

            $ioc->get(org_tubepress_impl_options_ui_tabs_GallerySourceTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_ThumbsTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_EmbeddedTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_MetaTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_ThemeTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_FeedTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_CacheTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_AdvancedTab::_), 
        );
    }
}
