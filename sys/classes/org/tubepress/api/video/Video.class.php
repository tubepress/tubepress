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
 * A video object that TubePress processes
 */
class org_tubepress_api_video_Video
{
    private $_authorDisplayName;
    private $_authorUid;
    private $_category;
    private $_commentCount;
    private $_description;
    private $_duration;
    private $_homeUrl;
    private $_id;
    private $_keywords;
    private $_likesCount;
    private $_ratingAverage;
    private $_ratingCount;
    private $_thumbnailUrl;
    private $_timeLastUpdated;
    private $_timePublished;
    private $_title;
    private $_viewCount;

    public function __construct()
    {
        $this->_keywords = array();	
    }

    public function getAuthorDisplayName() { return $this->_authorDisplayName; }
    public function setAuthorDisplayName($author) { $this->_authorDisplayName = $author; }
    
    public function getAuthorUid() { return $this->_authorUid; }
    public function setAuthorUid($author) { $this->_authorUid = $author; }

    public function getCategory() { return $this->_category; }
    public function setCategory($category) { $this->_category = $category; }

    public function getCommentCount() { return $this->_commentCount; }
    public function setCommentCount($count) { $this->_commentCount = $count; }
    
    public function getDescription() { return $this->_description; }
    public function setDescription($description) { $this->_description = $description; }
    
    public function getDuration() { return $this->_duration; }
    public function setDuration($duration) { $this->_duration = $duration; }

    public function getHomeUrl() { return $this->_homeUrl; }
    public function setHomeUrl($url) { $this->_homeUrl = $url; }
    
    public function getId() { return $this->_id; }
    public function setId($id) { $this->_id = $id; }
    
    public function getKeywords() { return $this->_keywords; }
    public function setKeywords($keywords) { $this->_keywords = $keywords; }
    
    public function getLikesCount() { return $this->_likesCount; }
    public function setLikesCount($c) { $this->_likesCount = $c; }

    public function getRatingAverage() { return $this->_ratingAverage; }
    public function setRatingAverage($average) { $this->_ratingAverage = $average; }
    
    public function getRatingCount() { return $this->_ratingCount; }
    public function setRatingCount($count) { $this->_ratingCount = $count; }

    public function getThumbnailUrl() { return $this->_thumbnailUrl; }
    public function setThumbnailUrl($url) { $this->_thumbnailUrl = $url; }

    public function getTimeLastUpdated() { return $this->_timeLastUpdated; }
    public function setTimeLastUpdated($time) { $this->_timeLastUpdated = $time; }

    public function getTimePublished() { return $this->_timePublished; }
    public function setTimePublished($time) { $this->_timePublished = $time; }

    public function getTitle() { return $this->_title; }
    public function setTitle($title) { $this->_title = $title; }
    
    public function getViewCount() { return $this->_viewCount; }
    public function setViewCount($count) { $this->_viewCount = $count; }
}
