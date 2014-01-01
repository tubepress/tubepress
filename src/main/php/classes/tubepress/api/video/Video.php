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
 * A video object that TubePress processes. It's basically just a key-value store.
 */
class tubepress_api_video_Video
{
    /**
     * We provide constansts here for commonly used keys.
     */
    const ATTRIBUTE_AUTHOR_DISPLAY_NAME      = 'authorDisplayName';
    const ATTRIBUTE_AUTHOR_USER_ID           = 'authorUid';
    const ATTRIBUTE_CATEGORY_DISPLAY_NAME    = 'category';
    const ATTRIBUTE_COMMENT_COUNT            = 'commentCount';
    const ATTRIBUTE_DESCRIPTION              = 'description';
    const ATTRIBUTE_DURATION_FORMATTED       = 'duration';
    const ATTRIBUTE_DURATION_SECONDS         = 'durationInSeconds';
    const ATTRIBUTE_HOME_URL                 = 'homeUrl';
    const ATTRIBUTE_ID                       = 'id';
    const ATTRIBUTE_KEYWORD_ARRAY            = 'keywords';
    const ATTRIBUTE_LIKES_COUNT              = 'likesCount';
    const ATTRIBUTE_PROVIDER_NAME            = 'providerName';
    const ATTRIBUTE_RATING_AVERAGE           = 'ratingAverage';
    const ATTRIBUTE_RATING_COUNT             = 'ratingCount';
    const ATTRIBUTE_THUMBNAIL_URL            = 'thumbnailUrl';
    const ATTRIBUTE_TIME_UPDATED_FORMATTED   = 'timeLastUpdatedFormatted';
    const ATTRIBUTE_TIME_PUBLISHED_FORMATTED = 'timePublishedFormatted';
    const ATTRIBUTE_TIME_PUBLISHED_UNIXTIME  = 'timePublishedUnixTime';
    const ATTRIBUTE_TITLE                    = 'title';
    const ATTRIBUTE_VIEW_COUNT               = 'viewCount' ;

    private $_attributes = array();

    /**
     * Get an attribute for this video.
     *
     * @param string $key The name of the attribute.
     *
     * @return mixed The value of the attribute. May be null.
     */
    public final function getAttribute($key)
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
     */
    public final function setAttribute($key, $value)
    {
        $this->_attributes[$key] = $value;
    }





    /**
     * ALL OF THE BELOW IS DEPRECATED AND SHOULD BE AVOIDED.
     */
    public function getAuthorDisplayName() { return $this->getAttribute(self::ATTRIBUTE_AUTHOR_DISPLAY_NAME); }
    public function setAuthorDisplayName($author) { $this->setAttribute(self::ATTRIBUTE_AUTHOR_DISPLAY_NAME, $author); }
    
    public function getAuthorUid() { return $this->getAttribute(self::ATTRIBUTE_AUTHOR_USER_ID); }
    public function setAuthorUid($author) { $this->setAttribute(self::ATTRIBUTE_AUTHOR_USER_ID, $author); }

    public function getCategory() { return $this->getAttribute(self::ATTRIBUTE_CATEGORY_DISPLAY_NAME); }
    public function setCategory($category) { $this->setAttribute(self::ATTRIBUTE_CATEGORY_DISPLAY_NAME, $category); }

    public function getCommentCount() { return $this->getAttribute(self::ATTRIBUTE_COMMENT_COUNT); }
    public function setCommentCount($count) { $this->setAttribute(self::ATTRIBUTE_COMMENT_COUNT, $count); }
    
    public function getDescription() { return $this->getAttribute(self::ATTRIBUTE_DESCRIPTION); }
    public function setDescription($description) { $this->setAttribute(self::ATTRIBUTE_DESCRIPTION, $description); }
    
    public function getDuration() { return $this->getAttribute(self::ATTRIBUTE_DURATION_FORMATTED); }
    public function setDuration($duration) { $this->setAttribute(self::ATTRIBUTE_DURATION_FORMATTED, $duration); }

    public function getHomeUrl() { return $this->getAttribute(self::ATTRIBUTE_HOME_URL); }
    public function setHomeUrl($url) { $this->setAttribute(self::ATTRIBUTE_HOME_URL, $url); }
    
    public function getId() { return $this->getAttribute(self::ATTRIBUTE_ID); }
    public function setId($id) { $this->setAttribute(self::ATTRIBUTE_ID, $id); }
    
    public function getKeywords() { return $this->getAttribute(self::ATTRIBUTE_KEYWORD_ARRAY); }
    public function setKeywords($keywords) { $this->setAttribute(self::ATTRIBUTE_KEYWORD_ARRAY, $keywords); }
    
    public function getLikesCount() { return $this->getAttribute(self::ATTRIBUTE_LIKES_COUNT); }
    public function setLikesCount($c) { $this->setAttribute(self::ATTRIBUTE_LIKES_COUNT, $c); }

    public function getRatingAverage() { return $this->getAttribute(self::ATTRIBUTE_RATING_AVERAGE); }
    public function setRatingAverage($average) { $this->setAttribute(self::ATTRIBUTE_RATING_AVERAGE, $average); }
    
    public function getRatingCount() { return $this->getAttribute(self::ATTRIBUTE_RATING_COUNT); }
    public function setRatingCount($count) { $this->setAttribute(self::ATTRIBUTE_RATING_COUNT, $count); }

    public function getThumbnailUrl() { return $this->getAttribute(self::ATTRIBUTE_THUMBNAIL_URL); }
    public function setThumbnailUrl($url) { $this->setAttribute(self::ATTRIBUTE_THUMBNAIL_URL, $url); }

    public function getTimeLastUpdated() { return $this->getAttribute(self::ATTRIBUTE_TIME_UPDATED_FORMATTED); }
    public function setTimeLastUpdated($time) { $this->setAttribute(self::ATTRIBUTE_TIME_UPDATED_FORMATTED, $time); }

    public function getTimePublished() { return $this->getAttribute(self::ATTRIBUTE_TIME_PUBLISHED_FORMATTED); }
    public function setTimePublished($time) { $this->setAttribute(self::ATTRIBUTE_TIME_PUBLISHED_FORMATTED, $time); }

    public function getTitle() { return $this->getAttribute(self::ATTRIBUTE_TITLE); }
    public function setTitle($title) { $this->setAttribute(self::ATTRIBUTE_TITLE, $title); }
    
    public function getViewCount() { return $this->getAttribute(self::ATTRIBUTE_VIEW_COUNT); }
    public function setViewCount($count) { $this->setAttribute(self::ATTRIBUTE_VIEW_COUNT, $count); }
}
