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
 *
 * @package TubePress\Video
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
     * @deprecated
     */
    public function getAuthorDisplayName() { return $this->getAttribute(self::ATTRIBUTE_AUTHOR_DISPLAY_NAME); }
    /**
     * @deprecated
     */
    public function setAuthorDisplayName($author) { $this->setAttribute(self::ATTRIBUTE_AUTHOR_DISPLAY_NAME, $author); }

    /**
     * @deprecated
     */
    public function getAuthorUid() { return $this->getAttribute(self::ATTRIBUTE_AUTHOR_USER_ID); }
    /**
     * @deprecated
     */
    public function setAuthorUid($author) { $this->setAttribute(self::ATTRIBUTE_AUTHOR_USER_ID, $author); }

    /**
     * @deprecated
     */
    public function getCategory() { return $this->getAttribute(self::ATTRIBUTE_CATEGORY_DISPLAY_NAME); }
    /**
     * @deprecated
     */
    public function setCategory($category) { $this->setAttribute(self::ATTRIBUTE_CATEGORY_DISPLAY_NAME, $category); }

    /**
     * @deprecated
     */
    public function getCommentCount() { return $this->getAttribute(self::ATTRIBUTE_COMMENT_COUNT); }
    /**
     * @deprecated
     */
    public function setCommentCount($count) { $this->setAttribute(self::ATTRIBUTE_COMMENT_COUNT, $count); }

    /**
     * @deprecated
     */
    public function getDescription() { return $this->getAttribute(self::ATTRIBUTE_DESCRIPTION); }
    /**
     * @deprecated
     */
    public function setDescription($description) { $this->setAttribute(self::ATTRIBUTE_DESCRIPTION, $description); }

    /**
     * @deprecated
     */
    public function getDuration() { return $this->getAttribute(self::ATTRIBUTE_DURATION_FORMATTED); }
    /**
     * @deprecated
     */
    public function setDuration($duration) { $this->setAttribute(self::ATTRIBUTE_DURATION_FORMATTED, $duration); }

    /**
     * @deprecated
     */
    public function getHomeUrl() { return $this->getAttribute(self::ATTRIBUTE_HOME_URL); }
    /**
     * @deprecated
     */
    public function setHomeUrl($url) { $this->setAttribute(self::ATTRIBUTE_HOME_URL, $url); }

    /**
     * @deprecated
     */
    public function getId() { return $this->getAttribute(self::ATTRIBUTE_ID); }
    /**
     * @deprecated
     */
    public function setId($id) { $this->setAttribute(self::ATTRIBUTE_ID, $id); }

    /**
     * @deprecated
     */
    public function getKeywords() { return $this->getAttribute(self::ATTRIBUTE_KEYWORD_ARRAY); }
    /**
     * @deprecated
     */
    public function setKeywords($keywords) { $this->setAttribute(self::ATTRIBUTE_KEYWORD_ARRAY, $keywords); }

    /**
     * @deprecated
     */
    public function getLikesCount() { return $this->getAttribute(self::ATTRIBUTE_LIKES_COUNT); }
    /**
     * @deprecated
     */
    public function setLikesCount($c) { $this->setAttribute(self::ATTRIBUTE_LIKES_COUNT, $c); }

    /**
     * @deprecated
     */
    public function getRatingAverage() { return $this->getAttribute(self::ATTRIBUTE_RATING_AVERAGE); }
    /**
     * @deprecated
     */
    public function setRatingAverage($average) { $this->setAttribute(self::ATTRIBUTE_RATING_AVERAGE, $average); }

    /**
     * @deprecated
     */
    public function getRatingCount() { return $this->getAttribute(self::ATTRIBUTE_RATING_COUNT); }
    /**
     * @deprecated
     */
    public function setRatingCount($count) { $this->setAttribute(self::ATTRIBUTE_RATING_COUNT, $count); }

    /**
     * @deprecated
     */
    public function getThumbnailUrl() { return $this->getAttribute(self::ATTRIBUTE_THUMBNAIL_URL); }
    /**
     * @deprecated
     */
    public function setThumbnailUrl($url) { $this->setAttribute(self::ATTRIBUTE_THUMBNAIL_URL, $url); }

    /**
     * @deprecated
     */
    public function getTimeLastUpdated() { return $this->getAttribute(self::ATTRIBUTE_TIME_UPDATED_FORMATTED); }
    /**
     * @deprecated
     */
    public function setTimeLastUpdated($time) { $this->setAttribute(self::ATTRIBUTE_TIME_UPDATED_FORMATTED, $time); }

    /**
     * @deprecated
     */
    public function getTimePublished() { return $this->getAttribute(self::ATTRIBUTE_TIME_PUBLISHED_FORMATTED); }
    /**
     * @deprecated
     */
    public function setTimePublished($time) { $this->setAttribute(self::ATTRIBUTE_TIME_PUBLISHED_FORMATTED, $time); }

    /**
     * @deprecated
     */
    public function getTitle() { return $this->getAttribute(self::ATTRIBUTE_TITLE); }
    /**
     * @deprecated
     */
    public function setTitle($title) { $this->setAttribute(self::ATTRIBUTE_TITLE, $title); }

    /**
     * @deprecated
     */
    public function getViewCount() { return $this->getAttribute(self::ATTRIBUTE_VIEW_COUNT); }
    /**
     * @deprecated
     */
    public function setViewCount($count) { $this->setAttribute(self::ATTRIBUTE_VIEW_COUNT, $count); }
}
