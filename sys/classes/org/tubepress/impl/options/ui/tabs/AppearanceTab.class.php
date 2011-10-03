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
    'org_tubepress_impl_options_ui_tabs_AbstractTab',
    'org_tubepress_impl_options_ui_widgets_DropdownInput',
));

/**
 * Displays the appearance tab.
 */
class org_tubepress_impl_options_ui_tabs_AppearanceTab extends org_tubepress_impl_options_ui_tabs_AbstractTab
{
    const _ = 'org_tubepress_impl_options_ui_tabs_AppearanceTab';

    protected function doGetTitle()
    {
        return 'Appearance';
    }

    protected function getDelegateFormHandlers()
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $widgetBuilder = $ioc->get(org_tubepress_spi_options_ui_WidgetBuilder::_);

        return array(

            $widgetBuilder->build(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME, org_tubepress_impl_options_ui_widgets_DropdownInput::_),
            $widgetBuilder->build(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE, org_tubepress_impl_options_ui_widgets_TextInput::_),
            $widgetBuilder->build(org_tubepress_api_const_options_names_Display::FLUID_THUMBS, org_tubepress_impl_options_ui_widgets_BooleanInput::_),
            $widgetBuilder->build(org_tubepress_api_const_options_names_Display::THUMB_HEIGHT, org_tubepress_impl_options_ui_widgets_TextInput::_),
            $widgetBuilder->build(org_tubepress_api_const_options_names_Display::THUMB_WIDTH, org_tubepress_impl_options_ui_widgets_TextInput::_),
            $widgetBuilder->build(org_tubepress_api_const_options_names_Display::THEME, org_tubepress_impl_options_ui_widgets_DropdownInput::_),
        );
    }
}