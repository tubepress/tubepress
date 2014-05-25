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
class tubepress_youtube_impl_options_ui_YouTubeFieldProvider implements tubepress_core_api_options_ui_FieldProviderInterface
{
    /**
     * @var tubepress_core_api_options_ui_FieldInterface[]
     */
    private $_fields;

    /**
     * @var tubepress_core_api_translation_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_core_api_translation_TranslatorInterface $translator,
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
     * @return tubepress_core_api_options_ui_ElementInterface[] The categories that this participant supplies.
     *
     * @api
     * @since 4.0.0
     */
    public function getCategories()
    {
        return array();
    }

    /**
     * @return tubepress_core_api_options_ui_FieldInterface[] The fields that this options page participant provides.
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

            'gallerysource-category' => array(

                tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH,
                tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
                tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
                tubepress_youtube_api_const_options_Values::YOUTUBE_FAVORITES,
                tubepress_youtube_api_const_options_Values::YOUTUBE_MOST_POPULAR,
                tubepress_youtube_api_const_options_Values::YOUTUBE_RELATED,
            ),

            'player-category' => array(

                tubepress_youtube_api_const_options_Names::AUTOHIDE,
                tubepress_youtube_api_const_options_Names::CLOSED_CAPTIONS,
                tubepress_youtube_api_const_options_Names::DISABLE_KEYBOARD,
                tubepress_youtube_api_const_options_Names::FULLSCREEN,
                tubepress_youtube_api_const_options_Names::MODEST_BRANDING,
                tubepress_youtube_api_const_options_Names::SHOW_ANNOTATIONS,
                tubepress_youtube_api_const_options_Names::SHOW_RELATED,
                tubepress_youtube_api_const_options_Names::THEME,
                tubepress_youtube_api_const_options_Names::SHOW_CONTROLS,
            ),

            'feed-category' => array(

                tubepress_youtube_api_const_options_Names::FILTER,
                tubepress_youtube_api_const_options_Names::DEV_KEY,
                tubepress_youtube_api_const_options_Names::EMBEDDABLE_ONLY,
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