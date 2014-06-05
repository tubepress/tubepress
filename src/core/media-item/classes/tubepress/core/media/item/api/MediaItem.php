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
 * A video object that TubePress processes. It's essentially just a key-value store.
 *
 * @package TubePress\Video
 *
 * @api
 * @since 4.0.0
 */
class tubepress_core_media_item_api_MediaItem
{
    /**
     * @var array
     */
    private $_attributes = array();

    /**
     * @var string
     */
    private $_id;

    public function __construct($id)
    {
        if (!is_scalar($id)) {

            throw new InvalidArgumentException('Item IDs must be scalar');
        }

        $this->_id = "$id";
    }

    /**
     * Get an attribute for this video.
     *
     * @param string $key The name of the attribute.
     *
     * @return mixed The value of the attribute. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getAttribute($key)
    {
        if (! isset($this->_attributes[$key])) {

            return null;
        }

        return $this->_attributes[$key];
    }

    /**
     * Set an attribute for this video.
     *
     * @param string $key   The attribute name.
     * @param mixed  $value The attribute value.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function setAttribute($key, $value)
    {
        $this->_attributes[$key] = $value;
    }

    public function hasAttribute($key)
    {
        return isset($this->_attributes[$key]);
    }

    public function getAttributeNames()
    {
        return array_keys($this->_attributes);
    }

    public function getId()
    {
        return $this->_id;
    }
}