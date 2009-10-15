<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
 * A YouTube video that TubePress can pass around easily
 */
class org_tubepress_video_Video
{
    private $_author;
    private $_category;
    private $_commentCount;
    private $_defaultThumbnailUrl;
    private $_description;
    private $_displayable;
    private $_duration;
    private $_embeddedObjectDataUrl;
    private $_highQualityThumbnailUrls;
    private $_homeUrl;
    private $_id;
    private $_keywords;
    private $_ratingAverage;
    private $_ratingCount;
    private $_regularQualityThumbnailUrls;
    private $_timeLastUpdated;
    private $_timePublished;
    private $_title;
    private $_viewCount;

    public function getAuthor() { return $this->_author; }
    public function setAuthor($author) { $this->_author = $author; }

    public function getCategory() { return $this->_category; }
    public function setCategory($category) { $this->_category = $category; }

    public function getCommentCount() { return $this->_commentCount; }
    public function setCommentCount($count) { $this->_commentCount = $count; }
    
    public function getDefaultThumbnailUrl() { return 'http://img.youtube.com/vi/' . $this->getId() . '/default.jpg'; }
    public function setDefaultThumbnailUrl($url) { $this->_defaultThumbnailUrl = $url; }
    
    public function getDescription() { return $this->_description; }
    public function setDescription($description) { $this->_description = $description; }
    
    public function isDisplayable() { return $this->_displayable; }
    public function setDisplayable($displayable) { $this->_displayable = $displayable; }
    
    public function getDuration() { return $this->_duration; }
    public function setDuration($duration) { $this->_duration = $duration; }

    public function getEmbeddedObjectDataUrl() { return $this->_embeddedObjectDataUrl; }
    public function setEmbeddedObjectDataUrl($url) { $this->_embeddedObjectDataUrl = $url; }
    
    public function getHighQualityThumbnailUrls() { return $this->_highQualityThumbnailUrls; }
    public function setHighQualityThumbnailUrls($urls) { $this->_highQualityThumbnailUrls = $urls; }
    
    public function getHomeUrl() { return $this->_homeUrl; }
    public function setHomeUrl($url) { $this->_homeUrl = $url; }
    
    public function getId() { return $this->_id; }
    public function setId($id) { $this->_id = $id; }
    
    public function getKeywords() { return $this->_keywords; }
    public function setKeywords($keywords) { $this->_keywords = $keywords; }

    public function getRatingAverage() { return $this->_ratingAverage; }
    public function setRatingAverage($average) { $this->_ratingAverage = $average; }
    
    public function getRatingCount() { return $this->_ratingCount; }
    public function setRatingCount($count) { $this->_ratingCount = $count; }

    public function getRegularQualityThumbnailUrls() { return $this->_regularQualityThumbnailUrls; }
    public function setRegularQualityThumbnailUrls($urls) { $this->_regularQualityThumbnailUrls = $urls; }

    public function getTimeLastUpdated() { return $this->_timeLastUpdated; }
    public function setTimeLastUpdated($time) { $this->_timeLastUpdated = $time; }

    public function getTimePublished() { return $this->_uploadTime; }
    public function setTimePublished($time) { $this->_uploadTime = $time; }

    public function getTitle() { return $this->_title; }
    public function setTitle($title) { $this->_title = $title; }
    
    public function getViewCount() { return $this->_viewCount; }
    public function setViewCount($count) { $this->_viewCount = $count; }
}
