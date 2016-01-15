<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.2.0
 */
class tubepress_api_options_ui_BaseFieldProvider implements tubepress_spi_options_ui_FieldProviderInterface
{
    /**
     * @var tubepress_api_options_ui_ElementInterface[]
     */
    private $_categories;

    /**
     * @var tubepress_api_options_ui_FieldInterface[]
     */
    private $_fields;

    /**
     * @var array
     */
    private $_map;

    /**
     * @var tubepress_api_collection_MapInterface
     */
    private $_properties;

    /**
     * @var string
     */
    private $_untranslatedDisplayName;

    /**
     * @var string
     */
    private $_id;

    /**
     * @var boolean
     */
    private $_ableToBeFilteredFromGui;

    /**
     * @var boolean
     */
    private $_fieldsInSeparateBoxes;

    public function __construct($id,
                                $untranslatedDisplayName,
                                $ableToBeFilteredFromGui,
                                $fieldsInSeparateBoxes,
                                array $categories,
                                array $fields,
                                array $map)
    {
        $this->_id                      = $id;
        $this->_categories              = $categories;
        $this->_fields                  = $fields;
        $this->_map                     = $map;
        $this->_properties              = new tubepress_internal_collection_Map();
        $this->_untranslatedDisplayName = $untranslatedDisplayName;
        $this->_ableToBeFilteredFromGui = $ableToBeFilteredFromGui;
        $this->_fieldsInSeparateBoxes   = $fieldsInSeparateBoxes;
    }

    /**
     * @return string The page-unique identifier for this item.
     *
     * @api
     * @since 4.0.0
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return tubepress_api_options_ui_ElementInterface[] The categories that this field provider supplies.
     *
     * @api
     * @since 4.0.0
     */
    public function getCategories()
    {
        return $this->_categories;
    }

    /**
     * @return tubepress_api_options_ui_FieldInterface[] The fields that this field provider provides.
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
        return $this->_map;
    }

    /**
     * @return boolean True if this field provider should show up in the "Only show options to..." dropdown. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isAbleToBeFilteredFromGui()
    {
        return $this->_ableToBeFilteredFromGui;
    }

    /**
     * @return boolean True if this field provider should separate its field into separate boxes. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return $this->_fieldsInSeparateBoxes;
    }

    /**
     * @return tubepress_api_collection_MapInterface
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * @param string $name  The property name.
     * @param mixed  $value The property value.
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

    /**
     * @return string|null The untranslated display name of this element. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return $this->_untranslatedDisplayName;
    }
}
