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

/**
 * Builds the options page for TubePress, if necessary.
 */
class tubepress_plugins_vimeo_impl_listeners_VimeoOptionsPageBuilder
{
    public function onBoot(ehough_tickertape_api_Event $bootEvent)
    {
        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();
        $fieldBuilder               = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFieldBuilder();

        $gallerySources = array(

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE
        );

        foreach ($gallerySources as $name => $value) {

            $field = $fieldBuilder->build($value,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME, 'gallery-source');

            $field = new tubepress_impl_options_ui_fields_GallerySourceField($name, $field);

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_options_ui_PluggableOptionsPageField::CLASS_NAME,
                $field
            );
        }
    }
}