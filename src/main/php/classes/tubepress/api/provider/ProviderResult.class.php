<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Simple class to abstract the response from a video provider
 */
class org_tubepress_api_provider_ProviderResult
{
    private $_effectiveDisplayCount;
    private $_effectiveTotalResultCount;
    private $_videoArray;

    public function __construct()
    {
        $this->_effectiveDisplayCount = 0;
        $this->_effectiveTotalResultCount = 0;
        $this->_videoArray = array();
    }
    
    /**
     * Set the video array
     *
     * @param array $videos The video array.
     *
     * @return void
     */
    public function setVideoArray($videos)
    {
        if (!is_array($videos)) {
            throw new Exception('setVideoArray can only take on an array. You supplied ' . var_export($videos, true));
        }
        $this->_videoArray = $videos;
    }

    /**
     * Set the effective total result count
     *
     * @param integer $count The effective total result count.
     *
     * @return void
     */
    public function setEffectiveTotalResultCount($count)
    {
        if (!is_numeric($count) || intval($count) < 0) {
            throw new Exception('setEffectiveTotalResultCount must take on a positive integer. You supplied ' . $count);
        }
        
        $this->_effectiveTotalResultCount = intval($count);
    }

    /**
     * Get the video array
     *
     * @return array The video array.
     */
    public function getVideoArray()
    {
        return $this->_videoArray;
    }

    /**
     * Get the effective total result count
     *
     * @return integer The effective total result count.
     */
    public function getEffectiveTotalResultCount()
    {
        return $this->_effectiveTotalResultCount;
    }
}
