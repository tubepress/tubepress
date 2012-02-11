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
    'org_tubepress_impl_options_ui_tabs_AbstractTab',
    'org_tubepress_impl_options_ui_fields_DropdownField',
    'org_tubepress_impl_options_ui_fields_ColorField',
));

/**
 * Displays the embedded tab.
 */
class org_tubepress_impl_options_ui_tabs_EmbeddedTab extends org_tubepress_impl_options_ui_tabs_AbstractTab
{
    const _ = 'org_tubepress_impl_options_ui_tabs_EmbeddedTab';

    protected function doGetTitle()
    {
        return 'Player';  //>(translatable)<
    }

    protected function getDelegateFormHandlers()
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fieldBuilder = $ioc->get(org_tubepress_spi_options_ui_FieldBuilder::_);

        return array(

            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION,  org_tubepress_impl_options_ui_fields_DropdownField::_),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL,      org_tubepress_impl_options_ui_fields_DropdownField::_),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT,  org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH,   org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::LAZYPLAY,         org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR,     org_tubepress_impl_options_ui_fields_ColorField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT, org_tubepress_impl_options_ui_fields_ColorField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::SHOW_INFO,        org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::FULLSCREEN,       org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY,     org_tubepress_impl_options_ui_fields_BooleanField::__),
        	$fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::AUTONEXT,         org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::AUTOPLAY,         org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::LOOP,             org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::SHOW_RELATED,     org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::AUTOHIDE,         org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::MODEST_BRANDING,  org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Embedded::ENABLE_JS_API,    org_tubepress_impl_options_ui_fields_BooleanField::__),
        );
    }
}