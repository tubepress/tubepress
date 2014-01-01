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
 * Represents a set of videos.
 */
class tubepress_api_video_VideoGalleryPage
{
    private $_totalResultCount = 0;
    private $_videoArray       = array();

    /**
     * Set the video array
     *
     * @param array $videos The video array.
     *
     * @return void
     */
    public final function setVideos(array $videos)
    {
        $this->_videoArray = $videos;
    }

    /**
     * Get the video array
     *
     * @return array The video array.
     */
    public final function getVideos()
    {
        return $this->_videoArray;
    }

    /**
     * Set the effective total result count
     *
     * @param integer $count The effective total result count.
     *
     * @throws InvalidArgumentException If you pass a non-integral or non-positive integer.
     *
     * @return void
     */
    public final function setTotalResultCount($count)
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
     */
    public final function getTotalResultCount()
    {
        return $this->_totalResultCount;
    }
}
