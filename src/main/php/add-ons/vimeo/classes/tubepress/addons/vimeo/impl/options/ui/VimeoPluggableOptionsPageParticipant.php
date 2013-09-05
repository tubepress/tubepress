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
 * Plugs Vimeo into the options page.
 */
class tubepress_addons_vimeo_impl_options_ui_VimeoPluggableOptionsPageParticipant extends tubepress_impl_options_ui_OptionsPageItem implements tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface
{
    private static $_PARTICIPANT_ID = 'vimeo-participant';

    /**
     * @var tubepress_spi_options_ui_OptionsPageFieldInterface[]
     */
    private $_cachedFields;

    public function __construct()
    {
        parent::__construct(self::$_PARTICIPANT_ID, 'Vimeo');   //>(translatable)<
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

            $keyField    = new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY);
            $secretField = new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET);

            $keyField->setSize(40);
            $secretField->setSize(40);

            $this->_cachedFields = array(

                //Gallery source fields
                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE)),

                new tubepress_impl_options_ui_fields_GallerySourceRadioField(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
                    new tubepress_impl_options_ui_fields_TextField(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE)),

                //Player fields
                new tubepress_impl_options_ui_fields_SpectrumColorField(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR),

                //Feed fields
                $keyField,
                $secretField,
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

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_GALLERYSOURCE => array(

                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_PLAYER => array(

                tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_FEED => array(

                tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY,
                tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET,
            ),
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
