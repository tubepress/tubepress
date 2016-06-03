<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_youtube3_impl_options_ui_FieldProvider implements tubepress_spi_options_ui_FieldProviderInterface
{
    /**
     * @var tubepress_api_options_ui_FieldInterface[]
     */
    private $_fields;

    /**
     * @var tubepress_api_collection_MapInterface
     */
    private $_properties;

    public function __construct(array $fields)
    {
        $this->_fields     = $fields;
        $this->_properties = new tubepress_internal_collection_Map();
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDisplayName()
    {
        return 'YouTube';    //>(translatable)<
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'field-provider-youtube';
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryIdsToFieldIdsMap()
    {
        return array(

            tubepress_api_options_ui_CategoryNames::GALLERY_SOURCE => array(

                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_LIST,
            ),

            tubepress_api_options_ui_CategoryNames::EMBEDDED => array(

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

            tubepress_api_options_ui_CategoryNames::FEED => array(

                tubepress_youtube3_api_Constants::OPTION_FILTER,
                tubepress_youtube3_api_Constants::OPTION_API_KEY,
                tubepress_youtube3_api_Constants::OPTION_EMBEDDABLE_ONLY,
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isAbleToBeFilteredFromGui()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty($name, $value)
    {
        $this->_properties->put($name, $value);
    }
}
