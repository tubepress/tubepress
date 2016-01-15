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
 * A media object that TubePress processes. It's essentially just a key-value store.
 *
 * @package TubePress\Video
 *
 * @api
 * @since 4.0.0
 */
class tubepress_api_media_MediaItem
{
    const ATTRIBUTE_AUTHOR_DISPLAY_NAME       = 'authorDisplayName';
    const ATTRIBUTE_AUTHOR_USER_ID            = 'authorUid';
    const ATTRIBUTE_AUTHOR_URL                = 'authorUrl';
    const ATTRIBUTE_CATEGORY_DISPLAY_NAME     = 'category';
    const ATTRIBUTE_COMMENT_COUNT             = 'commentCount';
    const ATTRIBUTE_COMMENT_COUNT_FORMATTED   = 'commentCountFormatted';
    const ATTRIBUTE_DESCRIPTION               = 'description';
    const ATTRIBUTE_COUNT_DISLIKES            = 'countDislikes';
    const ATTRIBUTE_COUNT_DISLIKES_FORMATTED  = 'countDislikesFormatted';
    const ATTRIBUTE_COUNT_FAVORITED           = 'countFavorited';
    const ATTRIBUTE_COUNT_FAVORITED_FORMATTED = 'countFavoritedFormatted';
    const ATTRIBUTE_DURATION_FORMATTED        = 'duration';
    const ATTRIBUTE_DURATION_SECONDS          = 'durationInSeconds';
    const ATTRIBUTE_HOME_URL                  = 'homeUrl';
    const ATTRIBUTE_ID                        = 'id';
    const ATTRIBUTE_INVOKING_ANCHOR_HREF      = 'invokingAnchorHref';
    const ATTRIBUTE_INVOKING_ANCHOR_REL       = 'invokingAnchorRel';
    const ATTRIBUTE_INVOKING_ANCHOR_TARGET    = 'invokingAnchorTarget';
    const ATTRIBUTE_KEYWORD_ARRAY             = 'keywords';
    const ATTRIBUTE_KEYWORDS_FORMATTED        = 'keywordsFormatted';
    const ATTRIBUTE_LIKES_COUNT               = 'likesCount';
    const ATTRIBUTE_LIKES_COUNT_FORMATTED     = 'likesCount';
    const ATTRIBUTE_PROVIDER                  = 'provider';
    const ATTRIBUTE_RATING_AVERAGE            = 'ratingAverage';
    const ATTRIBUTE_RATING_COUNT              = 'ratingCount';
    const ATTRIBUTE_THUMBNAIL_URL             = 'thumbnailUrl';
    const ATTRIBUTE_TIME_PUBLISHED_FORMATTED  = 'timePublishedFormatted';
    const ATTRIBUTE_TIME_PUBLISHED_UNIXTIME   = 'timePublishedUnixTime';
    const ATTRIBUTE_TITLE                     = 'title';
    const ATTRIBUTE_VIEW_COUNT                = 'viewCount';
    const ATTRIBUTE_VIEW_COUNT_FORMATTED      = 'viewCountFormatted';

    /**
     * @var tubepress_api_collection_MapInterface
     */
    private $_properties;
    
    public function __construct($id)
    {
        if (!is_scalar($id)) {

            throw new InvalidArgumentException('Item IDs must be scalar');
        }
        
        $this->_properties = new tubepress_internal_collection_Map();

        $this->_properties->put(self::ATTRIBUTE_ID, "$id");
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
        if (!$this->_properties->containsKey($key)) {

            return null;
        }

        return $this->_properties->get($key);
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
        $this->_properties->put($key, $value);
    }

    public function hasAttribute($key)
    {
        return $this->_properties->containsKey($key);
    }

    public function getAttributeNames()
    {
        return $this->_properties->keySet();
    }

    public function getId()
    {
        return $this->_properties->get('id');
    }

    public function toHtmlSafeArray()
    {
        $allNames = $this->_properties->keySet();
        $toReturn = array();
        foreach ($allNames as $name) {
            $toReturn[$name] = $this->_properties->get($name);
        }

        $toReturn = array_filter($toReturn, array($this, '__toJsonFilter'));

        return array_map(array($this, '__makeItSafe'), $toReturn);
    }

    public function __toJsonFilter($element)
    {
        return !is_object($element) && !is_resource($element);
    }

    public function __makeItSafe($element)
    {
        if (is_array($element)) {

            return array_map(array($this, '__makeItSafe'), $element);
        }

        return htmlspecialchars("$element", ENT_QUOTES, 'UTF-8');
    }

    public function __get($attributeName)
    {
        if ($this->hasAttribute($attributeName)) {

            return $this->getAttribute($attributeName);
        }

        return null;
    }

    public function __isset($attributeName)
    {
        return $this->hasAttribute($attributeName);
    }




    /**
     * @deprecated
     */
    public function getAuthorDisplayName() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME); }
    /**
     * @deprecated
     */
    public function setAuthorDisplayName($author) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME, $author); }

    /**
     * @deprecated
     */
    public function getAuthorUid() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID); }
    /**
     * @deprecated
     */
    public function setAuthorUid($author) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID, $author); }

    /**
     * @deprecated
     */
    public function getCategory() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME); }
    /**
     * @deprecated
     */
    public function setCategory($category) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME, $category); }

    /**
     * @deprecated
     */
    public function getCommentCount() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT); }
    /**
     * @deprecated
     */
    public function setCommentCount($count) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT, $count); }

    /**
     * @deprecated
     */
    public function getDescription() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION); }
    /**
     * @deprecated
     */
    public function setDescription($description) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, $description); }

    /**
     * @deprecated
     */
    public function getDuration() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED); }
    /**
     * @deprecated
     */
    public function setDuration($duration) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED, $duration); }

    /**
     * @deprecated
     */
    public function getHomeUrl() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL); }
    /**
     * @deprecated
     */
    public function setHomeUrl($url) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL, $url); }

    /**
     * @deprecated
     */
    public function getKeywords() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY); }
    /**
     * @deprecated
     */
    public function setKeywords($keywords) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY, $keywords); }

    /**
     * @deprecated
     */
    public function getLikesCount() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT); }
    /**
     * @deprecated
     */
    public function setLikesCount($c) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT, $c); }

    /**
     * @deprecated
     */
    public function getRatingAverage() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_RATING_AVERAGE); }
    /**
     * @deprecated
     */
    public function setRatingAverage($average) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_RATING_AVERAGE, $average); }

    /**
     * @deprecated
     */
    public function getRatingCount() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_RATING_COUNT); }
    /**
     * @deprecated
     */
    public function setRatingCount($count) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_RATING_COUNT, $count); }

    /**
     * @deprecated
     */
    public function getThumbnailUrl() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL); }
    /**
     * @deprecated
     */
    public function setThumbnailUrl($url) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL, $url); }

    /**
     * @deprecated
     */
    public function getTimePublished() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED); }
    /**
     * @deprecated
     */
    public function setTimePublished($time) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED, $time); }

    /**
     * @deprecated
     */
    public function getTitle() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_TITLE); }
    /**
     * @deprecated
     */
    public function setTitle($title) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_TITLE, $title); }

    /**
     * @deprecated
     */
    public function getViewCount() { return $this->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT); }
    /**
     * @deprecated
     */
    public function setViewCount($count) { $this->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, $count); }
}