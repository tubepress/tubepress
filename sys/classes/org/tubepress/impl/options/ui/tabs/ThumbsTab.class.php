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
    'org_tubepress_impl_options_ui_fields_BooleanField',
    'org_tubepress_impl_options_ui_fields_DropdownField',
    'org_tubepress_impl_options_ui_fields_ThemeField',
));

/**
 * Displays the thumbnails tab.
 */
class org_tubepress_impl_options_ui_tabs_ThumbsTab extends org_tubepress_impl_options_ui_tabs_AbstractTab
{
    const _ = 'org_tubepress_impl_options_ui_tabs_ThumbsTab';

    protected function doGetTitle()
    {
        return 'Thumbnails';  //>(translatable)<
    }

    protected function getDelegateFormHandlers()
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fieldBuilder = $ioc->get(org_tubepress_spi_options_ui_FieldBuilder::_);

        return array(

            $fieldBuilder->build(org_tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT,     org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Thumbs::THUMB_WIDTH,      org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION,  org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Thumbs::FLUID_THUMBS,     org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE,   org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW,   org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Thumbs::HQ_THUMBS,        org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS,    org_tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE, org_tubepress_impl_options_ui_fields_TextField::__),
        );
    }
}