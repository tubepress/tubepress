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
class tubepress_jwplayer_impl_options_ui_JwPlayerFieldProvider implements tubepress_app_options_ui_api_FieldProviderInterface
{
    private static $_MAP = array(

        'player-category' => array(

            tubepress_jwplayer_api_Constants::OPTION_COLOR_BACK,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_FRONT,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_LIGHT,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_SCREEN)
    );

    /**
     * @var tubepress_app_options_ui_api_FieldInterface[]
     */
    private $_fields;

    /**
     * @var tubepress_lib_translation_api_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_lib_translation_api_TranslatorInterface  $translator,
                                array $fields)
    {
        $this->_translator = $translator;
        $this->_fields     = $fields;
    }

    /**
     * @return tubepress_app_options_ui_api_ElementInterface[] The categories that this field provider supplies.
     */
    public function getCategories()
    {
        return array();
    }

    /**
     * @return tubepress_app_options_ui_api_FieldInterface[] The fields that this field provider provides.
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
        return self::$_MAP;
    }

    /**
     * @return boolean True if this field provider should show up in the "Only show options to..." dropdown. False otherwise.
     */
    public function isAbleToBeFilteredFromGui()
    {
        return false;
    }

    /**
     * @return boolean True if this field provider should separate its field into separate boxes. False otherwise.
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return true;
    }

    /**
     * @return string The name of the item that is displayed to the user.
     */
    public function getTranslatedDisplayName()
    {
        return $this->_translator->_('JW Player');          //>(translatable)<)
    }

    /**
     * @return string The page-unique identifier for this item.
     */
    public function getId()
    {
        return 'jwplayer-field-provider';
    }
}