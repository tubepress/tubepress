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

/**
 */
class tubepress_options_ui_impl_FieldProvider implements tubepress_spi_options_ui_FieldProviderInterface
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

    public function __construct(array $categories, array $fields, array $map)
    {
        $this->_categories = $categories;
        $this->_fields     = $fields;
        $this->_map        = $map;
        $this->_properties = new tubepress_internal_collection_Map();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'field-provider-core';
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories()
    {
        return $this->_categories;
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
        return $this->_map;
    }

    /**
     * {@inheritdoc}
     */
    public function isAbleToBeFilteredFromGui()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return false;
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

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDisplayName()
    {
        return null;
    }
}
