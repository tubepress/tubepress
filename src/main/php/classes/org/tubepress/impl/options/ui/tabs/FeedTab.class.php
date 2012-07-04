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
));

/**
 * Displays the feed tab.
 */
class org_tubepress_impl_options_ui_tabs_FeedTab extends org_tubepress_impl_options_ui_tabs_AbstractTab
{
    const _ = 'org_tubepress_impl_options_ui_tabs_FeedTab';

    protected function doGetTitle()
    {
        return 'Feed';  //>(translatable)<
    }

    protected function getDelegateFormHandlers()
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fieldBuilder = $ioc->get(org_tubepress_spi_options_ui_FieldBuilder::_);

        return array(

            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::ORDER_BY,         org_tubepress_impl_options_ui_fields_DropdownField::_),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::PER_PAGE_SORT,    org_tubepress_impl_options_ui_fields_DropdownField::_),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP, org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::DEV_KEY,          org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::VIMEO_KEY,        org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::VIMEO_SECRET,     org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST,  org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER, org_tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::FILTER,           org_tubepress_impl_options_ui_fields_DropDownField::_),
            $fieldBuilder->build(org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY,  org_tubepress_impl_options_ui_fields_BooleanField::__),
        );
    }
}