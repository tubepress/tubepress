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
    'org_tubepress_impl_options_ui_fields_AbstractMultiSelectField',
    'org_tubepress_api_const_options_names_WordPress',
));

/**
 * Displays a multi-select drop-down input for video meta.
 */
class org_tubepress_impl_options_ui_fields_FilterMultiSelectField extends org_tubepress_impl_options_ui_fields_AbstractMultiSelectField
{
    const __ = 'org_tubepress_impl_options_ui_fields_FilterMultiSelectField';
    
    public function __construct()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);

        parent::__construct(array(

            $odr->findOneByName(org_tubepress_api_const_options_names_WordPress::SHOW_VIMEO_OPTIONS),
            $odr->findOneByName(org_tubepress_api_const_options_names_WordPress::SHOW_YOUTUBE_OPTIONS),


        ), 'filterdropdown');
    }

    protected function getRawTitle()
    {
        return 'Only show options applicable to...';    //>(translatable)<
    }

    protected function getRawDescription()
    {
        return '';
    }
}