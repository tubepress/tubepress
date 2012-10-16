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
class tubepress_plugins_youtube_impl_listeners_YouTubeOptionsPageBuilder
{
    public function onBoot(ehough_tickertape_api_Event $bootEvent)
    {
        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();
        $fieldBuilder               = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFieldBuilder();

        $gallerySources = array(

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FEATURED_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TRENDING_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_SHARED_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_RELATED_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_RESPONSES_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );

        foreach ($gallerySources as $gallerySource) {

            $field = $fieldBuilder->build($gallerySource[1],
                $gallerySource[2], 'gallery-source');

            $field = new tubepress_impl_options_ui_fields_GallerySourceField($gallerySource[0], $field);

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_options_ui_PluggableOptionsPageField::CLASS_NAME,
                $field
            );
        }

        $embeddedBooleans = array(

            tubepress_plugins_youtube_api_const_options_names_Embedded::AUTOHIDE,
            tubepress_plugins_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS,
            tubepress_plugins_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD,
            tubepress_plugins_youtube_api_const_options_names_Embedded::FULLSCREEN,
            tubepress_plugins_youtube_api_const_options_names_Embedded::MODEST_BRANDING,
            tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS,
            tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_CONTROLS,
            tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_RELATED,
        );

        foreach ($embeddedBooleans as $embeddedBoolean) {

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_options_ui_PluggableOptionsPageField::CLASS_NAME,
                new tubepress_impl_options_ui_fields_BooleanField($embeddedBoolean, 'embedded')
            );
        }

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_options_ui_PluggableOptionsPageField::CLASS_NAME,
            new tubepress_impl_options_ui_fields_DropdownField(tubepress_plugins_youtube_api_const_options_names_Embedded::THEME, 'embedded')
        );

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_options_ui_PluggableOptionsPageField::CLASS_NAME,
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_plugins_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY, 'feed')
        );

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_options_ui_PluggableOptionsPageField::CLASS_NAME,
            new tubepress_impl_options_ui_fields_TextField(tubepress_plugins_youtube_api_const_options_names_Feed::DEV_KEY, 'feed')
        );

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_options_ui_PluggableOptionsPageField::CLASS_NAME,
            new tubepress_impl_options_ui_fields_DropdownField(tubepress_plugins_youtube_api_const_options_names_Feed::FILTER, 'feed')
        );
    }
}