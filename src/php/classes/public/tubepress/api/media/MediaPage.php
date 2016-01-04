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
 * Represents a set of media items.
 *
 * @package TubePress\Video
 *
 * @api
 * @since 4.0.0
 */
class tubepress_api_media_MediaPage
{
    /**
     * @var int
     */
    private $_totalResultCount = 0;

    /**
     * @var tubepress_api_media_MediaItem[]
     */
    private $_itemArray = array();

    /**
     * Set the media item array
     *
     * @param tubepress_api_media_MediaItem[] $items The media item array.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function setItems(array $items)
    {
        $this->_itemArray = $items;
    }

    /**
     * Get the media item array
     *
     * @return tubepress_api_media_MediaItem[] The media item array. May be empty but never null.
     *
     * @api
     * @since 4.0.0
     */
    public function getItems()
    {
        return $this->_itemArray;
    }

    /**
     * Set the effective total result count
     *
     * @param integer $count The effective total result count.
     *
     * @throws InvalidArgumentException If you pass a non-integral or non-positive integer.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function setTotalResultCount($count)
    {
        if (!is_numeric($count) || intval($count) < 0) {

            throw new InvalidArgumentException('setTotalResultCount must take on a positive integer. You supplied ' . $count);
        }
        
        $this->_totalResultCount = intval($count);
    }

    /**
     * Get the effective total result count
     *
     * @return integer The effective total result count.
     *
     * @api
     * @since 4.0.0
     */
    public function getTotalResultCount()
    {
        return $this->_totalResultCount;
    }
}
