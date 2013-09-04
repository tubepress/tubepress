<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Plugs YouTube into the options page.
 */
class tubepress_addons_youtube_impl_options_ui_YouTubeOptionsPageParticipant extends tubepress_impl_options_ui_OptionsPageItem implements tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface
{
    private static $_PARTICIPANT_ID = 'youtube-participant';

    private $_cachedFields;

    public function __construct()
    {
        parent::__construct(self::$_PARTICIPANT_ID, 'YouTube');     //>(translatable)<
    }

    /**
     * @return tubepress_spi_options_ui_OptionsPageItemInterface[] The categories that this participant supplies.
     */
    public function getCategories()
    {
        return array();
    }

    /**
     * @return tubepress_spi_options_ui_OptionsPageFieldInterface[] The fields that this options page participant provides.
     */
    public function getFields()
    {
        if (!isset($this->_cachedFields)) {

            $this->_cachedFields = array(

                //Gallery source fields
                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
                    new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_FEATURED_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING,
                    new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TRENDING_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                    new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
                    new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
                    new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED,
                    new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_SHARED_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
                    new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
                    new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
                    new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_RELATED_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_RESPONSES_VALUE)),


                //Player fields
                new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE),
                new tubepress_impl_options_ui_fields_BooleanField(tubepress_addons_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS),
                new tubepress_impl_options_ui_fields_BooleanField(tubepress_addons_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD),
                new tubepress_impl_options_ui_fields_BooleanField(tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN),
                new tubepress_impl_options_ui_fields_BooleanField(tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING),
                new tubepress_impl_options_ui_fields_BooleanField(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS),
                new tubepress_impl_options_ui_fields_BooleanField(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED),
                new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_Embedded::THEME),
                new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_CONTROLS),

                //Feed fields
                new tubepress_impl_options_ui_fields_DropdownField(tubepress_addons_youtube_api_const_options_names_Feed::FILTER),
                new tubepress_impl_options_ui_fields_TextField(tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY),
                new tubepress_impl_options_ui_fields_BooleanField(tubepress_addons_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY),
            );
        }

        return $this->_cachedFields;
    }

    /**
     * @return array An associative array, which may be empty, where the keys are category IDs and the values
     *               are arrays of field IDs that belong in the category.
     */
    public function getCategoryIdsToFieldIdsMap()
    {
        return array(

            tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant::CATEGORY_ID_GALLERYSOURCE => array(

                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES,
            ),

            tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant::CATEGORY_ID_PLAYER => array(

                tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE,
                tubepress_addons_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS,
                tubepress_addons_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD,
                tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN,
                tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING,
                tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS,
                tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED,
                tubepress_addons_youtube_api_const_options_names_Embedded::THEME,
                tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_CONTROLS,
            ),

            tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant::CATEGORY_ID_FEED => array(

                tubepress_addons_youtube_api_const_options_names_Feed::FILTER,
                tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY,
                tubepress_addons_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY,
            )
        );
    }

    /**
     * @return string JavaScript to run *below* the elements on the options page. Make sure to enclose the script with
     *                <script type="text/javascrip> and close it with </script>!
     */
    public function getInlineJs()
    {
        return '';
    }
}
