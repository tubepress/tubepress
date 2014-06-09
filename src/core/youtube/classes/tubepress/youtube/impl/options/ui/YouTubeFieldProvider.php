<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
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
class tubepress_youtube_impl_options_ui_YouTubeFieldProvider implements tubepress_core_options_ui_api_FieldProviderInterface
{
    /**
     * @var tubepress_core_options_ui_api_FieldInterface[]
     */
    private $_fields;

    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_core_translation_api_TranslatorInterface $translator,
                                array $fields)
    {
        $this->_translator = $translator;
        $this->_fields     = $fields;
    }

    /**
     * @return string The name of the item that is displayed to the user.
     *
     * @api
     * @since 4.0.0
     */
    public function getTranslatedDisplayName()
    {
        return $this->_translator->_('YouTube');    //>(translatable)<
    }

    /**
     * @return string The page-unique identifier for this item.
     *
     * @api
     * @since 4.0.0
     */
    public function getId()
    {
        return 'youtube-field-provider';
    }

    /**
     * @return tubepress_core_options_ui_api_ElementInterface[] The categories that this participant supplies.
     *
     * @api
     * @since 4.0.0
     */
    public function getCategories()
    {
        return array();
    }

    /**
     * @return tubepress_core_options_ui_api_FieldInterface[] The fields that this options page participant provides.
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

            tubepress_core_media_provider_api_Constants::OPTIONS_UI_CATEGORY_GALLERY_SOURCE => array(

                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED,
            ),

            tubepress_core_embedded_api_Constants::OPTIONS_UI_CATEGORY_EMBEDDED => array(

                tubepress_youtube_api_Constants::OPTION_AUTOHIDE,
                tubepress_youtube_api_Constants::OPTION_CLOSED_CAPTIONS,
                tubepress_youtube_api_Constants::OPTION_DISABLE_KEYBOARD,
                tubepress_youtube_api_Constants::OPTION_FULLSCREEN,
                tubepress_youtube_api_Constants::OPTION_MODEST_BRANDING,
                tubepress_youtube_api_Constants::OPTION_SHOW_ANNOTATIONS,
                tubepress_youtube_api_Constants::OPTION_SHOW_RELATED,
                tubepress_youtube_api_Constants::OPTION_THEME,
                tubepress_youtube_api_Constants::OPTION_SHOW_CONTROLS,
            ),

            tubepress_core_media_provider_api_Constants::OPTIONS_UI_CATEGORY_FEED => array(

                tubepress_youtube_api_Constants::OPTION_FILTER,
                tubepress_youtube_api_Constants::OPTION_DEV_KEY,
                tubepress_youtube_api_Constants::OPTION_EMBEDDABLE_ONLY,
            )
        );
    }

    /**
     * @return boolean True if this participant should show up in the "Only show options to..." dropdown. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isAbleToBeFilteredFromGui()
    {
        return true;
    }

    /**
     * @return boolean True if this participant should separate its field into separate boxes. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return true;
    }
}