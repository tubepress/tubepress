<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles the heavy lifting for YouTube.
 */
class tubepress_youtube3_impl_media_MediaProvider implements tubepress_app_api_media_MediaProviderInterface
{
    private static $_SOURCE_NAMES = array(

        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED,
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_LIST,
    );

    private static $_MODE_TEMPLATE_MAP = array(

        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED   => 'videos related to %s',
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST  => 'playlist %s',
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES => 'videos favorited by %s',
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH    => 'videos matching search term %s',
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_USER      => 'uploads of %s',
        tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_LIST      => 'manual list of videos: %s',
    );

    /**
     * @var tubepress_app_api_media_HttpCollectorInterface
     */
    private $_httpCollector;

    /**
     * @var tubepress_app_api_media_HttpFeedHandlerInterface
     */
    private $_feedHandler;

    /**
     * @var tubepress_platform_api_collection_MapInterface
     */
    private $_properties;

    public function __construct(tubepress_app_api_media_HttpCollectorInterface     $httpCollector,
                                tubepress_app_api_media_HttpFeedHandlerInterface   $feedHandler,
                                tubepress_app_api_environment_EnvironmentInterface $environment)
    {
        $this->_httpCollector = $httpCollector;
        $this->_feedHandler   = $feedHandler;
        $this->_properties    = new tubepress_platform_impl_collection_Map();

        $baseUrlClone = $environment->getBaseUrl()->getClone();
        $miniIconUrl  = $baseUrlClone->addPath('/src/add-ons/youtube_v3/web/images/icons/youtube-icon-34w_x_34h.png')->toString();
        $this->getProperties()->put('miniIconUrl', $miniIconUrl);
        $this->getProperties()->put('untranslatedModeTemplateMap', self::$_MODE_TEMPLATE_MAP);
    }

    /**
     * Collects a media gallery page.
     *
     * @param int $pageNumber The page number.
     *
     * @return tubepress_app_api_media_MediaPage The media gallery page, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function collectPage($pageNumber)
    {
        return $this->_httpCollector->collectPage($pageNumber, $this->_feedHandler);
    }

    /**
     * Fetch a single media item.
     *
     * @param string $id The media item ID to fetch.
     *
     * @return tubepress_app_api_media_MediaItem The media item, or null not found.
     *
     * @api
     * @since 4.0.0
     */
    public function collectSingle($id)
    {
        return $this->_httpCollector->collectSingle($id, $this->_feedHandler);
    }

    /**
     * @param string $itemId The item ID.
     *
     * @return bool True if this provider "owns" the given item ID, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function ownsItem($itemId)
    {
        return preg_match_all('/^[A-Za-z0-9-_]{11}$/', $itemId, $matches) === 1;
    }

    /**
     * @return array An array of the valid option values for the "mode" option.
     */
    public function getGallerySourceNames()
    {
        return self::$_SOURCE_NAMES;
    }

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public function getName()
    {
        return 'youtube';
    }

    /**
     * @return string The human-readable name of this video provider.
     */
    public function getDisplayName()
    {
        return 'YouTube';
    }

    /**
     * @return string The name of the "mode" value that this provider uses for searching.
     *
     * @api
     * @since 4.0.0
     */
    public function getSearchModeName()
    {
        return tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH;
    }

    /**
     * @return string The option name where TubePress should put the users search results.
     *
     * @api
     * @since 4.0.0
     */
    public function getSearchQueryOptionName()
    {
        return tubepress_youtube3_api_Constants::OPTION_YOUTUBE_TAG_VALUE;
    }

    /**
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getMapOfFeedSortNamesToUntranslatedLabels()
    {
        return array(
            tubepress_youtube3_api_Constants::ORDER_BY_DEFAULT    => 'default',                         //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_NEWEST     => 'date published (newest first)',   //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_RATING     => 'rating',                          //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_RELEVANCE  => 'relevance',                       //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_TITLE      => 'title',                           //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_VIEW_COUNT => 'view count',                      //>(translatable)<
        );
    }

    /**
     * @return string[] An array of meta names
     *
     * @api
     * @since 4.0.0
     */
    public function getMapOfMetaOptionNamesToAttributeDisplayNames()
    {
        return array(

            tubepress_app_api_options_Names::META_DISPLAY_TITLE       => tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE,
            tubepress_app_api_options_Names::META_DISPLAY_LENGTH      => tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_AUTHOR      => tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
            tubepress_app_api_options_Names::META_DISPLAY_KEYWORDS    => tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_URL         => tubepress_app_api_media_MediaItem::ATTRIBUTE_HOME_URL,
            tubepress_app_api_options_Names::META_DISPLAY_CATEGORY    => tubepress_app_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME,

            tubepress_youtube3_api_Constants::OPTION_META_COUNT_LIKES     => tubepress_app_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT_FORMATTED,
            tubepress_youtube3_api_Constants::OPTION_META_COUNT_DISLIKES  => tubepress_app_api_media_MediaItem::ATTRIBUTE_COUNT_DISLIKES_FORMATTED,
            tubepress_youtube3_api_Constants::OPTION_META_COUNT_COMMENTS  => tubepress_app_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT_FORMATTED,
            tubepress_youtube3_api_Constants::OPTION_META_COUNT_FAVORITES => tubepress_app_api_media_MediaItem::ATTRIBUTE_COUNT_FAVORITED_FORMATTED,

            tubepress_app_api_options_Names::META_DISPLAY_ID          => tubepress_app_api_media_MediaItem::ATTRIBUTE_ID,
            tubepress_app_api_options_Names::META_DISPLAY_VIEWS       => tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_UPLOADED    => tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_DESCRIPTION => tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
        );
    }

    /**
     * @api
     * @since 4.1.11
     *
     * @return tubepress_platform_api_collection_MapInterface
     */
    public function getProperties()
    {
        return $this->_properties;
    }
}