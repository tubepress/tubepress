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
 * Hooks JW Player into TubePress.
 */
class tubepress_addons_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant extends tubepress_impl_options_ui_OptionsPageItem implements tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface
{
    private static $_PARTICIPANT_ID = 'jwplayer-participant';

    private $_cachedFields;

    public function __construct()
    {
        parent::__construct(self::$_PARTICIPANT_ID, 'JW Player');     //>(translatable)<
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

                new tubepress_impl_options_ui_fields_SpectrumColorField(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK),
                new tubepress_impl_options_ui_fields_SpectrumColorField(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT),
                new tubepress_impl_options_ui_fields_SpectrumColorField(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT),
                new tubepress_impl_options_ui_fields_SpectrumColorField(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN),

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

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_PLAYER => array(

                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN)
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
