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
class tubepress_vimeo_impl_options_ui_VimeoFieldProvider implements tubepress_core_options_ui_api_FieldProviderInterface
{
    /**
     * @var tubepress_core_options_ui_api_FieldInterface[]
     */
    private $_fields;

    /**
     * @var tubepress_core_options_ui_api_ElementInterface[]
     */
    private $_categories;

    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_core_translation_api_TranslatorInterface $translator,
                                array $fields,
                                array $categories)
    {
        $this->_translator = $translator;
        $this->_fields     = $fields;
        $this->_categories = $categories;
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
     * @return tubepress_core_options_ui_api_ElementInterface[] The categories that this participant supplies.
     */
    public function getCategories()
    {
        return $this->_categories;
    }

    /**
     * @return tubepress_core_options_ui_api_FieldInterface[] The fields that this options page participant provides.
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

            tubepress_core_options_ui_api_Constants::CATEGORY_NAME_GALLERYSOURCE => array(

                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CREDITED,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
            ),

            tubepress_core_options_ui_api_Constants::CATEGORY_NAME_PLAYER => array(

                tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR,
            ),

            tubepress_core_options_ui_api_Constants::CATEGORY_NAME_FEED => array(

                tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET,
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