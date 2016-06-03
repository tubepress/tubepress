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
class tubepress_options_ui_impl_BaseElement implements tubepress_api_options_ui_ElementInterface
{
    protected static $PROPERTY_ID                   = 'id';
    protected static $PROPERTY_UNTRANS_DISPLAY_NAME = 'untranslatedDisplayName';
    /**
     * @var tubepress_api_collection_MapInterface
     */
    private $_properties;

    public function __construct($id, $untranslatedDisplayName = null)
    {
        if (!is_string($id)) {

            throw new InvalidArgumentException('Option page item IDs must be of type string');
        }

        $this->_properties = new tubepress_internal_collection_Map();

        $this->setProperty(self::$PROPERTY_ID, $id);

        if ($untranslatedDisplayName) {

            $this->setProperty(self::$PROPERTY_UNTRANS_DISPLAY_NAME, $untranslatedDisplayName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_properties->get(self::$PROPERTY_ID);
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
        return $this->getOptionalProperty(self::$PROPERTY_UNTRANS_DISPLAY_NAME, null);
    }

    protected function getOptionalProperty($propertyName, $default)
    {
        if (!$this->_properties->containsKey($propertyName)) {

            return $default;
        }

        return $this->_properties->get($propertyName);
    }
}
