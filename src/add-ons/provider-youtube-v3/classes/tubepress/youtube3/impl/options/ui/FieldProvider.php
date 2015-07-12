<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_youtube3_impl_options_ui_FieldProvider implements tubepress_app_api_options_ui_FieldProviderInterface
{
    /**
     * @var tubepress_app_api_options_ui_FieldInterface[]
     */
    private $_fields;

    /**
     * @var tubepress_platform_api_collection_MapInterface
     */
    private $_properties;

    public function __construct(array $fields)
    {
        $this->_fields     = $fields;
        $this->_properties = new tubepress_platform_impl_collection_Map();
    }

    /**
     * @return string The name of the item that is displayed to the user.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'YouTube';    //>(translatable)<
    }

    /**
     * @return string The page-unique identifier for this item.
     *
     * @api
     * @since 4.0.0
     */
    public function getId()
    {
        return 'field-provider-youtube';
    }

    /**
     * @return tubepress_app_api_options_ui_ElementInterface[] The categories that this field provider supplies.
     *
     * @api
     * @since 4.0.0
     */
    public function getCategories()
    {
        return array();
    }

    /**
     * @return tubepress_app_api_options_ui_FieldInterface[] The fields that this field provider provides.
     *
     * @api
     * @since 4.0.0
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @return array An associative array, which may be empty, where the keys are category IDs and the values
     *               are arrays of field IDs that belong in the category.
     *
     * @api
     * @since 4.0.0
     */
    public function getCategoryIdsToFieldIdsMap()
    {
        return array(

            tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE => array(

                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_LIST,
            ),

            tubepress_app_api_options_ui_CategoryNames::EMBEDDED => array(

                tubepress_youtube3_api_Constants::OPTION_AUTOHIDE,
                tubepress_youtube3_api_Constants::OPTION_CLOSED_CAPTIONS,
                tubepress_youtube3_api_Constants::OPTION_DISABLE_KEYBOARD,
                tubepress_youtube3_api_Constants::OPTION_FULLSCREEN,
                tubepress_youtube3_api_Constants::OPTION_MODEST_BRANDING,
                tubepress_youtube3_api_Constants::OPTION_SHOW_ANNOTATIONS,
                tubepress_youtube3_api_Constants::OPTION_SHOW_RELATED,
                tubepress_youtube3_api_Constants::OPTION_THEME,
                tubepress_youtube3_api_Constants::OPTION_SHOW_CONTROLS,
            ),

            tubepress_app_api_options_ui_CategoryNames::FEED => array(

                tubepress_youtube3_api_Constants::OPTION_FILTER,
                tubepress_youtube3_api_Constants::OPTION_API_KEY,
                tubepress_youtube3_api_Constants::OPTION_EMBEDDABLE_ONLY,
            )
        );
    }

    /**
     * @return boolean True if this field provider should show up in the "Only show options to..." dropdown. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isAbleToBeFilteredFromGui()
    {
        return true;
    }

    /**
     * @return boolean True if this field provider should separate its field into separate boxes. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return true;
    }

    /**
     * @return tubepress_platform_api_collection_MapInterface
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * @param string $name The property name.
     * @param mixed $value The property value.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function setProperty($name, $value)
    {
        $this->_properties->put($name, $value);
    }
}