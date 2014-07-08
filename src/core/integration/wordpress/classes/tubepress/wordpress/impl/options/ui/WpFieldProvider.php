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

class tubepress_wordpress_impl_options_ui_WpFieldProvider implements tubepress_app_options_ui_api_FieldProviderInterface
{
    /**
     * @var tubepress_lib_translation_api_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_app_options_ui_api_FieldInterface[]
     */
    private $_fields;

    public function __construct(tubepress_lib_translation_api_TranslatorInterface $translator,
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
        return $this->_translator->_('WordPress');     //>(translatable)<
    }

    /**
     * @return string The page-unique identifier for this item.
     */
    public function getId()
    {
        return 'wordpress-field-provider';
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
        return array(
            'advanced-category' => array(
                tubepress_app_shortcode_api_Constants::OPTION_KEYWORD
            )
        );
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
        return false;
    }
}