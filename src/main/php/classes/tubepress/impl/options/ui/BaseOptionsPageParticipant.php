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
class tubepress_impl_options_ui_BaseOptionsPageParticipant extends tubepress_impl_options_ui_OptionsPageItem implements tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface
{
    /**
     * @var tubepress_spi_options_ui_OptionsPageItemInterface[]
     */
    private $_categories = array();

    /**
     * @var tubepress_spi_options_ui_OptionsPageFieldInterface[]
     */
    private $_fields = array();

    /**
     * @var array
     */
    private $_map = array();

    /**
     * @var boolean
     */
    private $_isAbleToBeFilteredFromGui;

    /**
     * @var boolean
     */
    private $_fieldsContainedInSeparateBoxes;

    public function __construct($id, tubepress_api_translation_TranslatorInterface $translator,
                                $untranslatedDisplayName, array $categories, array $fields, array $map,
                                $ableToBeFilteredFromGui = true, $fieldsContainedInSeparateBoxes = true)
    {
        parent::__construct($id, $translator, $untranslatedDisplayName);

        $this->_categories                     = $categories;
        $this->_fields                         = $fields;
        $this->_map                            = $map;
        $this->_isAbleToBeFilteredFromGui      = (boolean) $ableToBeFilteredFromGui;
        $this->_fieldsContainedInSeparateBoxes = (boolean) $fieldsContainedInSeparateBoxes;
    }

    /**
     * @return tubepress_spi_options_ui_OptionsPageItemInterface[] The categories that this participant supplies.
     */
    public function getCategories()
    {
        return $this->_categories;
    }

    /**
     * @return tubepress_spi_options_ui_OptionsPageFieldInterface[] The fields that this options page participant provides.
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
        return $this->_map;
    }

    /**
     * @return boolean True if this participant should show up in the "Only show options to..." dropdown. False otherwise.
     */
    public function isAbleToBeFilteredFromGui()
    {
        return $this->_isAbleToBeFilteredFromGui;
    }

    /**
     * @return boolean True if this participant should separate its field into separate boxes. False otherwise.
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return $this->_fieldsContainedInSeparateBoxes;
    }
}