<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
class org_tubepress_video_feed_FeedResult {
    
    private $_effectiveDisplayCount;
    private $_effectiveTotalResultCount;
    private $_videoArray;
    
    public function setVideoArray($videos)  { $this->_videoArray = $videos; }
    public function setEffectiveTotalResultCount($count) { $this->_effectiveTotalResultCount = $count; }
    public function setEffectiveDisplayCount($count) { $this->_effectiveDisplayCount = $count; }
    
    public function getVideoArray() { return $this->_videoArray; }
    public function getEffectiveTotalResultCount() { return $this->_effectiveTotalResultCount; }
    public function getEffectiveDisplayCount() { return $this->_effectiveDisplayCount; }
}
