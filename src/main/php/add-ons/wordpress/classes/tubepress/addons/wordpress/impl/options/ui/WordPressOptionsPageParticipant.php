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
 *
 */
class tubepress_addons_wordpress_impl_options_ui_WordPressOptionsPageParticipant implements tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface
{
    const PARTICIPANT_ID = 'wordpress';

    private $_cachedFields;

    /**
     * @return string The name of the item that is displayed to the user.
     */
    public function getTranslatedDisplayName()
    {
        return 'WordPress';  //this will never be shown, so don't translate
    }

    /**
     * @return string The page-unique identifier for this item.
     */
    public function getId()
    {
        return self::PARTICIPANT_ID;
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

                new tubepress_addons_wordpress_impl_options_ui_fields_WpNonceField(),
                new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Advanced::KEYWORD),
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

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_ADVANCED => array(

                tubepress_api_const_options_names_Advanced::KEYWORD
            )
        );
    }
}