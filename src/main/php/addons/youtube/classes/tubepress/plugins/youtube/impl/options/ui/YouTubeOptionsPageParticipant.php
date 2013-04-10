<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Plugs YouTube into the options page.
 */
class tubepress_plugins_youtube_impl_options_ui_YouTubeOptionsPageParticipant implements tubepress_spi_options_ui_PluggableOptionsPageParticipant
{
    /**
     * @param string $tabName The name of the tab being built.
     *
     * @return array An array of fields that should be shown on the given tab. May be empty, never null.
     */
    public final function getFieldsForTab($tabName)
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        switch ($tabName) {

            case tubepress_impl_options_ui_tabs_EmbeddedTab::TAB_NAME:

                return $this->getFieldsForEmbeddedTab($fieldBuilder);

            case tubepress_impl_options_ui_tabs_FeedTab::TAB_NAME:

                return $this->getFieldsForFeedTab($fieldBuilder);

            case tubepress_impl_options_ui_tabs_GallerySourceTab::TAB_NAME:

                return $this->getFieldsForGallerySourceTab($fieldBuilder);

            default:

                return array();
        }
    }

    /**
     * @return string The name that will be displayed in the options page filter (top right).
     */
    public final function getFriendlyName()
    {
        return 'YouTube';
    }

    /**
     * @return string All lowercase alphanumerics.
     */
    public final function getName()
    {
        return 'youtube';
    }

    private function getFieldsForEmbeddedTab(tubepress_spi_options_ui_FieldBuilder $fieldBuilder)
    {
        $toReturn = array(

            $fieldBuilder->build(

                tubepress_plugins_youtube_api_const_options_names_Embedded::AUTOHIDE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME
            )
        );

        $embeddedBooleans = array(

            tubepress_plugins_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS,
            tubepress_plugins_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD,
            tubepress_plugins_youtube_api_const_options_names_Embedded::FULLSCREEN,
            tubepress_plugins_youtube_api_const_options_names_Embedded::MODEST_BRANDING,
            tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS,
            tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_RELATED,
        );

        $toReturn[] = $fieldBuilder->build(

            tubepress_plugins_youtube_api_const_options_names_Embedded::THEME,
            tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME
        );

        foreach ($embeddedBooleans as $embeddedBoolean) {

            $toReturn[] = $fieldBuilder->build($embeddedBoolean, tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME);
        }

        $toReturn[] = $fieldBuilder->build(

            tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_CONTROLS,
            tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME
        );

        return $toReturn;
    }

    private function getFieldsForFeedTab(tubepress_spi_options_ui_FieldBuilder $fieldBuilder)
    {
        return array(

            $fieldBuilder->build(

                tubepress_plugins_youtube_api_const_options_names_Feed::FILTER,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME
            ),

            $fieldBuilder->build(

                tubepress_plugins_youtube_api_const_options_names_Feed::DEV_KEY,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME
            ),

            $fieldBuilder->build(

                tubepress_plugins_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME
            ),
        );
    }

    private function getFieldsForGallerySourceTab(tubepress_spi_options_ui_FieldBuilder $fieldBuilder)
    {
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

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE,
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

        $toReturn = array();

        foreach ($gallerySources as $gallerySource) {

            $field = $fieldBuilder->build($gallerySource[1], $gallerySource[2]);

            $toReturn[] = new tubepress_impl_options_ui_fields_GallerySourceField($gallerySource[0], $field);
        }

        return $toReturn;
    }
}
