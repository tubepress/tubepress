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
class tubepress_options_ui_impl_MultiSourceFieldProviderWrapper implements tubepress_spi_options_ui_FieldProviderInterface
{
    /**
     * @var tubepress_spi_options_ui_FieldProviderInterface
     */
    private $_delegate;

    /**
     * @var string
     */
    private $_id;

    /**
     * @var tubepress_api_options_ui_FieldInterface[]
     */
    private $_fields;

    public function __construct(tubepress_spi_options_ui_FieldProviderInterface $delegate,
                                array $fields)
    {
        $this->_delegate = $delegate;
        $this->_id       = $delegate->getId() . '-wrapped-' . mt_rand();
        $this->_fields   = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->_delegate->getProperties();
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty($name, $value)
    {
        $this->_delegate->setProperty($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDisplayName()
    {
        return $this->_delegate->getUntranslatedDisplayName();
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories()
    {
        return $this->_delegate->getCategories();
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
        return $this->_delegate->getCategoryIdsToFieldIdsMap();
    }

    /**
     * {@inheritdoc}
     */
    public function isAbleToBeFilteredFromGui()
    {
        return $this->_delegate->isAbleToBeFilteredFromGui();
    }

    /**
     * {@inheritdoc}
     */
    public function fieldsShouldBeInSeparateBoxes()
    {
        return $this->_delegate->fieldsShouldBeInSeparateBoxes();
    }
}
