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
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_names_GallerySource',
    'org_tubepress_api_const_options_values_GallerySourceValue',
    'org_tubepress_impl_options_ui_tabs_AbstractTab',
    'org_tubepress_impl_options_ui_fields_DropdownField',
    'org_tubepress_impl_options_ui_fields_TextField',
));

/**
 * Displays the video source tab.
 */
class org_tubepress_impl_options_ui_tabs_GallerySourceTab extends org_tubepress_impl_options_ui_tabs_AbstractTab
{
    const _ = 'org_tubepress_impl_options_ui_tabs_GallerySourceTab';

    const TEMPLATE_VAR_CURRENT_MODE = 'org_tubepress_impl_options_ui_tabs_GallerySourceTab__mode';

    protected function doGetTitle()
    {
        return 'Which videos?';  //>(translatable)<
    }

    protected function getDelegateFormHandlers()
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fieldBuilder = $ioc->get(org_tubepress_spi_options_ui_FieldBuilder::_);

        return array(

            org_tubepress_api_const_options_names_Output::GALLERY_SOURCE =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_FEATURED, org_tubepress_impl_options_ui_fields_DropdownField::_),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_USER =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_VIEWED =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_VIEWED_VALUE, org_tubepress_impl_options_ui_fields_DropdownField::_),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE, org_tubepress_impl_options_ui_fields_DropdownField::_),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE, org_tubepress_impl_options_ui_fields_DropdownField::_),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE, org_tubepress_impl_options_ui_fields_DropdownField::_),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE, org_tubepress_impl_options_ui_fields_DropdownField::_),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE, org_tubepress_impl_options_ui_fields_DropdownField::_),

            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_ALBUM =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_GROUP =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_LIKES =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_CREDITED =>
                $fieldBuilder->build(org_tubepress_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE, org_tubepress_impl_options_ui_fields_TextField::__),
        );
    }

    protected function addToTemplate(org_tubepress_api_template_Template $template)
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $currentMode = $execContext->get(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        $template->setVariable(self::TEMPLATE_VAR_CURRENT_MODE, $currentMode);
    }

    protected function getTemplatePath()
    {
        return '/sys/ui/templates/options_page/gallery_source_tab.tpl.php';
    }
}