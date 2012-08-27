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
    'org_tubepress_api_options_OptionDescriptorReference',
    'org_tubepress_impl_options_ui_fields_AbstractMultiSelectField',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_api_const_options_names_Meta'
));

/**
 * Displays a multi-select drop-down input for video meta.
 */
class org_tubepress_impl_options_ui_fields_MetaMultiSelectField extends org_tubepress_impl_options_ui_fields_AbstractMultiSelectField
{
    public function __construct()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);

        parent::__construct(array(

            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::AUTHOR),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::RATING),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::CATEGORY),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::UPLOADED),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::DESCRIPTION),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::ID),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::KEYWORDS),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::LIKES),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::RATINGS),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::LENGTH),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::TITLE),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::URL),
            $odr->findOneByName(org_tubepress_api_const_options_names_Meta::VIEWS),

        ), 'metadropdown');
    }

    protected function getRawTitle()
    {
        return 'Show each video\'s...';   //>(translatable)<
    }

    protected function getRawDescription()
    {
        return '';
    }

}