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
class tubepress_core_provider_api_MediaItem
{
    /**
     * @var array
     */
    private $_attributes = array();

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


    /**
     * @deprecated
     */
    public function getAuthorDisplayName() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME); }
    /**
     * @deprecated
     */
    public function setAuthorDisplayName($author) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME, $author); }

    /**
     * @deprecated
     */
    public function getAuthorUid() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_AUTHOR_USER_ID); }
    /**
     * @deprecated
     */
    public function setAuthorUid($author) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_AUTHOR_USER_ID, $author); }

    /**
     * @deprecated
     */
    public function getCategory() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_CATEGORY_DISPLAY_NAME); }
    /**
     * @deprecated
     */
    public function setCategory($category) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_CATEGORY_DISPLAY_NAME, $category); }

    /**
     * @deprecated
     */
    public function getCommentCount() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_COMMENT_COUNT); }
    /**
     * @deprecated
     */
    public function setCommentCount($count) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_COMMENT_COUNT, $count); }

    /**
     * @deprecated
     */
    public function getDescription() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_DESCRIPTION); }
    /**
     * @deprecated
     */
    public function setDescription($description) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_DESCRIPTION, $description); }

    /**
     * @deprecated
     */
    public function getDuration() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_DURATION_FORMATTED); }
    /**
     * @deprecated
     */
    public function setDuration($duration) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_DURATION_FORMATTED, $duration); }

    /**
     * @deprecated
     */
    public function getHomeUrl() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_HOME_URL); }
    /**
     * @deprecated
     */
    public function setHomeUrl($url) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_HOME_URL, $url); }

    /**
     * @deprecated
     */
    public function getId() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_ID); }
    /**
     * @deprecated
     */
    public function setId($id) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_ID, $id); }

    /**
     * @deprecated
     */
    public function getKeywords() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_KEYWORD_ARRAY); }
    /**
     * @deprecated
     */
    public function setKeywords($keywords) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_KEYWORD_ARRAY, $keywords); }

    /**
     * @deprecated
     */
    public function getLikesCount() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_LIKES_COUNT); }
    /**
     * @deprecated
     */
    public function setLikesCount($c) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_LIKES_COUNT, $c); }

    /**
     * @deprecated
     */
    public function getRatingAverage() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_RATING_AVERAGE); }
    /**
     * @deprecated
     */
    public function setRatingAverage($average) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_RATING_AVERAGE, $average); }

    /**
     * @deprecated
     */
    public function getRatingCount() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_RATING_COUNT); }
    /**
     * @deprecated
     */
    public function setRatingCount($count) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_RATING_COUNT, $count); }

    /**
     * @deprecated
     */
    public function getThumbnailUrl() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_THUMBNAIL_URL); }
    /**
     * @deprecated
     */
    public function setThumbnailUrl($url) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_THUMBNAIL_URL, $url); }

    /**
     * @deprecated
     */
    public function getTimeLastUpdated() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_TIME_UPDATED_FORMATTED); }
    /**
     * @deprecated
     */
    public function setTimeLastUpdated($time) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_TIME_UPDATED_FORMATTED, $time); }

    /**
     * @deprecated
     */
    public function getTimePublished() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED); }
    /**
     * @deprecated
     */
    public function setTimePublished($time) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED, $time); }

    /**
     * @deprecated
     */
    public function getTitle() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_TITLE); }
    /**
     * @deprecated
     */
    public function setTitle($title) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_TITLE, $title); }

    /**
     * @deprecated
     */
    public function getViewCount() { return $this->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_VIEW_COUNT); }
    /**
     * @deprecated
     */
    public function setViewCount($count) { $this->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_VIEW_COUNT, $count); }
}
