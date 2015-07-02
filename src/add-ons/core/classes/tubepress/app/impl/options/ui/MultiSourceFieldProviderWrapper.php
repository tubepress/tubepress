<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 */
class tubepress_app_impl_options_ui_MultiSourceFieldProviderWrapper implements tubepress_app_api_options_ui_FieldProviderInterface
{
    /**
     * @var tubepress_app_api_options_ui_FieldProviderInterface
     */
    private $_delegate;

    /**
     * @var string
     */
    private $_id;

    /**
     * @var tubepress_app_api_options_ui_FieldInterface[]
     */
    private $_fields;

    public function __construct(tubepress_app_api_options_ui_FieldProviderInterface $delegate,
                                array $fields)
    {
        $this->_delegate = $delegate;
        $this->_id       = $delegate->getId() . '-wrapped-' . mt_rand();
        $this->_fields   = $fields;
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
     * @return tubepress_platform_api_collection_MapInterface
     */
    public function getProperties()
    {
        return $this->_delegate->getProperties();
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
        $this->_delegate->setProperty($name, $value);
    }

    /**
     * @return string|null The untranslated display name of this element. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return $this->_delegate->getUntranslatedDisplayName();
    }

    /**
     * @return tubepress_app_api_options_ui_ElementInterface[] The categories that this field provider supplies.
     *
     * @api
     * @since 4.0.0
     */
    public function getCategories()
    {
        return $this->_delegate->getCategories();
    }

    /**
     * @return tubepress_app_api_options_ui_FieldInterface[] The fields that this field provider provides.
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
        return $this->_delegate->getCategoryIdsToFieldIdsMap();
    }

    /**
     * @return boolean True if this field provider should show up in the "Only show options to..." dropdown. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isAbleToBeFilteredFromGui()
    {
        return $this->_delegate->isAbleToBeFilteredFromGui();
    }

    /**
     * @return boolean True if this field provider should separate its field into separate boxes. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return $this->_delegate->fieldsShouldBeInSeparateBoxes();
    }
}
