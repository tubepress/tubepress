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
class tubepress_vimeo_impl_options_ui_VimeoFieldProvider implements tubepress_core_api_options_ui_FieldProviderInterface
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
     */
    public function getTranslatedDisplayName()
    {
        return $this->_translator->_('Vimeo');   //>(translatable)<
    }

    /**
     * @return string The page-unique identifier for this item.
     */
    public function getId()
    {
        return 'vimeo-field-provider';
    }

    /**
     * @return tubepress_core_api_options_ui_ElementInterface[] The categories that this participant supplies.
     */
    public function getCategories()
    {
        return array();
    }

    /**
     * @return tubepress_core_api_options_ui_FieldInterface[] The fields that this options page participant provides.
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @return array An associative array, which may be empty, where the keys are category IDs and the values
     *               are arrays of field IDs that belong in the category.
     */
    public function getCategoryIdsToFieldIdsMap()
    {
        return array(

            'gallerysource-category' => array(

                tubepress_vimeo_api_const_options_Values::VIMEO_ALBUM,
                tubepress_vimeo_api_const_options_Values::VIMEO_CHANNEL,
                tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH,
                tubepress_vimeo_api_const_options_Values::VIMEO_UPLOADEDBY,
                tubepress_vimeo_api_const_options_Values::VIMEO_APPEARS_IN,
                tubepress_vimeo_api_const_options_Values::VIMEO_CREDITED,
                tubepress_vimeo_api_const_options_Values::VIMEO_LIKES,
                tubepress_vimeo_api_const_options_Values::VIMEO_GROUP,
            ),

            'player-category' => array(

                tubepress_vimeo_api_const_options_Names::PLAYER_COLOR,
            ),

            'feed-category' => array(

                tubepress_vimeo_api_const_options_Names::VIMEO_KEY,
                tubepress_vimeo_api_const_options_Names::VIMEO_SECRET,
            ),
        );
    }

    /**
     * @return boolean True if this participant should show up in the "Only show options to..." dropdown. False otherwise.
     */
    public function isAbleToBeFilteredFromGui()
    {
        return true;
    }

    /**
     * @return boolean True if this participant should separate its field into separate boxes. False otherwise.
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return true;
    }
}