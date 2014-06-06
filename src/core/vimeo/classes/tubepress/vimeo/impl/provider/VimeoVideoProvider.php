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
 * Handles the heavy lifting for Vimeo.
 */
class tubepress_vimeo_impl_provider_VimeoVideoProvider implements tubepress_core_media_provider_api_HttpProviderInterface
{
    private static $_URL_PARAM_ALBUM_ID      = 'album_id';
    private static $_URL_PARAM_CHANNEL_ID    = 'channel_id';
    private static $_URL_PARAM_FORMAT        = 'format';
    private static $_URL_PARAM_FULL_RESPONSE = 'full_response';
    private static $_URL_PARAM_GROUP_ID      = 'group_id';
    private static $_URL_PARAM_METHOD        = 'method';
    private static $_URL_PARAM_PAGE          = 'page';
    private static $_URL_PARAM_PER_PAGE      = 'per_page';
    private static $_URL_PARAM_QUERY         = 'query';
    private static $_URL_PARAM_SORT          = 'sort';
    private static $_URL_PARAM_USER_ID       = 'user_id';
    private static $_URL_PARAM_VIDEO_ID      = 'video_id';

    private static $_METHOD_ALBUM_GETVIDEOS    = 'vimeo.albums.getItems';
    private static $_METHOD_CHANNEL_GETVIDEOS  = 'vimeo.channels.getItems';
    private static $_METHOD_GROUP_GETVIDEOS    = 'vimeo.groups.getItems';
    private static $_METHOD_VIDEOS_APPEARSIN   = 'vimeo.videos.getAppearsIn';
    private static $_METHOD_VIDEOS_GETALL      = 'vimeo.videos.getAll';
    private static $_METHOD_VIDEOS_GETINFO     = 'vimeo.videos.getInfo';
    private static $_METHOD_VIDEOS_GETLIKES    = 'vimeo.videos.getLikes';
    private static $_METHOD_VIDEOS_GETUPLOADED = 'vimeo.videos.getUploaded';
    private static $_METHOD_VIDEOS_SEARCH      = 'vimeo.videos.search';

    private static $_SORT_MOST_COMMENTS = 'most_commented';
    private static $_SORT_MOST_LIKED    = 'most_liked';
    private static $_SORT_MOST_PLAYED   = 'most_played';
    private static $_SORT_RELEVANT      = 'relevant';
    private static $_SORT_NEWEST        = 'newest';
    private static $_SORT_OLDEST        = 'oldest';

    private static $_URL_BASE = 'http://vimeo.com/api/rest/v2';

    private static $_GALLERY_SOURCE_NAMES = array(

        tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
        tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
        tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
        tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CREDITED,
        tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
        tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
        tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
        tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY
    );

    /**
     * @var tubepress_api_log_LoggerInterface Logger.
     */
    private $_logger;

    /**
     * @var tubepress_core_url_api_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_media_provider_api_ItemSorterInterface
     */
    private $_itemSorter;

    private $_unserialized;

    private $_videoArray;

    public function __construct(tubepress_api_log_LoggerInterface                 $logger,
                                tubepress_core_url_api_UrlFactoryInterface        $urlFactory,
                                tubepress_core_options_api_ContextInterface       $context,
                                tubepress_core_media_provider_api_ItemSorterInterface   $itemSorter)
    {
        $this->_logger          = $logger;
        $this->_urlFactory      = $urlFactory;
        $this->_context         = $context;
        $this->_itemSorter      = $itemSorter;
    }

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return tubepress_core_url_api_UrlInterface The request URL for this gallery.
     *
     * @api
     * @since 4.0.0
     */
    public function buildUrlForPage($currentPage)
    {
        $params = array();
        $mode   = $this->_context->get(tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE);

        $this->_verifyKeyAndSecretExists();

        switch ($mode) {

            case tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETUPLOADED;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE);

                break;

            case tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_LIKES:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETLIKES;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_LIKES_VALUE);

                break;

            case tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_APPEARSIN;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE);

                break;

            case tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH:

                $params[self::$_URL_PARAM_METHOD] = self::$_METHOD_VIDEOS_SEARCH;
                $params[self::$_URL_PARAM_QUERY]  = $this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_SEARCH_VALUE);

                $filter = $this->_context->get(tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER);

                if ($filter != '') {

                    $params[self::$_URL_PARAM_USER_ID] = $filter;
                }

                break;

            case tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CREDITED:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETALL;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_CREDITED_VALUE);

                break;

            case tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL:

                $params[self::$_URL_PARAM_METHOD]     = self::$_METHOD_CHANNEL_GETVIDEOS;
                $params[self::$_URL_PARAM_CHANNEL_ID] = $this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_CHANNEL_VALUE);

                break;

            case tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_ALBUM:

                $params[self::$_URL_PARAM_METHOD]   = self::$_METHOD_ALBUM_GETVIDEOS;
                $params[self::$_URL_PARAM_ALBUM_ID] = $this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_ALBUM_VALUE);

                break;

            case tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_GROUP:

                $params[self::$_URL_PARAM_METHOD]   = self::$_METHOD_GROUP_GETVIDEOS;
                $params[self::$_URL_PARAM_GROUP_ID] = $this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_GROUP_VALUE);
        }

        $params[self::$_URL_PARAM_FULL_RESPONSE] = 'true';
        $params[self::$_URL_PARAM_PAGE]          = $currentPage;
        $params[self::$_URL_PARAM_PER_PAGE]      = $this->_context->get(tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE);
        $sort                                    = $this->_getSort($mode);

        if ($sort != '') {

            $params[self::$_URL_PARAM_SORT] = $sort;
        }

        return $this->_buildUrl($params);
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return tubepress_core_url_api_UrlInterface The URL for the single video given.
     *
     * @api
     * @since 4.0.0
     */
    public function buildUrlForSingle($id)
    {
        $this->_verifyKeyAndSecretExists();

        if (! $this->recognizesItemId($id)) {

            throw new InvalidArgumentException("Unable to build Vimeo URL for video with ID $id");
        }

        $params                             = array();
        $params[self::$_URL_PARAM_METHOD]   = self::$_METHOD_VIDEOS_GETINFO;
        $params[self::$_URL_PARAM_VIDEO_ID] = $id;

        return $this->_buildUrl($params);
    }

    /**
     * @return array An array of the valid option valu
es for the "mode" option.
     */
    public function getGallerySourceNames()
    {
        return self::$_GALLERY_SOURCE_NAMES;
    }

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public function getName()
    {
        return 'vimeo';
    }

    /**
     * @return string The human-readable name of this video provider.
     */
    public function getDisplayName()
    {
        return 'Vimeo';
    }

    /**
     * @return array An array of meta names
     */
    public function getMetaOptionNames()
    {
        return array(

            tubepress_core_media_item_api_Constants::OPTION_AUTHOR,
            tubepress_core_media_item_api_Constants::OPTION_CATEGORY,
            tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION,
            tubepress_core_media_item_api_Constants::OPTION_ID,
            tubepress_core_media_item_api_Constants::OPTION_KEYWORDS,
            tubepress_core_media_item_api_Constants::OPTION_LENGTH,
            tubepress_core_media_item_api_Constants::OPTION_TITLE,
            tubepress_core_media_item_api_Constants::OPTION_UPLOADED,
            tubepress_core_media_item_api_Constants::OPTION_URL,
            tubepress_core_media_item_api_Constants::OPTION_VIEWS,

            tubepress_vimeo_api_Constants::OPTION_LIKES
        );
    }

    /**
     * Count the total videos in this feed result.
     *
     * @param mixed $feed The raw feed from the provider.
     *
     * @return int The total result count of this query.
     *
     * @api
     * @since 4.0.0
     */
    public function getTotalResultCount($feed)
    {
        return isset($this->_unserialized->videos->total) ? $this->_unserialized->videos->total : 0;
    }

    /**
     * Determine if we can build a video from this element of the feed.
     *
     * @param integer $index The index into the feed.
     * @param mixed   $feed  The raw feed.
     *
     * @return boolean True if we can build a video from this element, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function canWorkWithItemAtIndex($index, $feed)
    {
        return $this->_videoArray[$index]->embed_privacy !== 'nowhere';
    }

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @param mixed $feed The feed.
     *
     * @return integer A count of videos in this feed.
     *
     * @api
     * @since 4.0.0
     */
    public function getCurrentResultCount($feed)
    {
        return sizeof($this->_videoArray);
    }

    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed $feed The feed to construct.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function onAnalysisStart($feed)
    {
        $this->_unserialized = @unserialize($feed);
        $this->_videoArray   = array();

        /*
         * Make sure we can actually unserialize the feed.
         */
        if ($this->_unserialized === false) {

            throw new tubepress_core_media_provider_api_exception_ProviderException('Unable to unserialize PHP from Vimeo');
        }

        /*
         * Make sure Vimeo is happy.
         */
        if ($this->_unserialized->stat !== 'ok') {

            throw new tubepress_core_media_provider_api_exception_ProviderException($this->_getErrorMessageFromVimeo());
        }

        /*
         * Is this just a single video?
         */
        if (isset($this->_unserialized->video)) {

            $this->_videoArray = (array) $this->_unserialized->video;

            return;
        }

        /*
         * Must be a page of videos.
         */
        if (isset($this->_unserialized->videos) && isset($this->_unserialized->videos->video)) {

            $this->_videoArray = (array) $this->_unserialized->videos->video;
        }
    }

    /**
     * Perform post-construction activites for the feed.
     *
     * @param mixed $feed The feed we used.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function onAnalysisComplete($feed)
    {
        unset($this->_videoArray);
        unset($this->_unserialized);
    }

    /**
     * Let's subclasses add arguments to the video construction event.
     *
     * @param tubepress_core_event_api_EventInterface $event The event we're about to fire.
     *
     * @api
     * @since 4.0.0
     */
    public function onPreFireNewMediaItemEvent(tubepress_core_event_api_EventInterface $event)
    {
        $event->setArgument('unserializedFeed', $this->_unserialized);
        $event->setArgument('videoArray', $this->_videoArray);
    }

    /**
     * Ask this media provider if it recognizes the given item ID.
     *
     * @param string $mediaId The globally unique media item identifier.
     *
     * @return boolean True if this provider recognizes the given item ID, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function recognizesItemId($mediaId)
    {
        return is_numeric($mediaId);
    }

    /**
     * Determine why the given item cannot be used.
     *
     * @param integer $index The index into the feed.
     * @param mixed   $feed  The raw feed.
     *
     * @return string The reason why we can't work with this video, or null if we can.
     *
     * @api
     * @since 4.0.0
     */
    public function getReasonUnableToWorkWithItemAtIndex($index, $feed)
    {
        return 'Vimeo privacy options restrict embedding.';
    }

    /**
     * @return string The name of the "mode" value that this provider uses for searching.
     *
     * @api
     * @since 4.0.0
     */
    public function getSearchModeName()
    {
        return tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH;
    }

    /**
     * @return string The option name where TubePress should put the users search results.
     *
     * @api
     * @since 4.0.0
     */
    public function getSearchQueryOptionName()
    {
        return tubepress_vimeo_api_Constants::OPTION_VIMEO_SEARCH_VALUE;
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

            tubepress_vimeo_api_Constants::ORDER_BY_COMMENT_COUNT => 'comment count',                   //>(translatable)<
            tubepress_vimeo_api_Constants::ORDER_BY_NEWEST        => 'date published (newest first)',   //>(translatable)<
            tubepress_vimeo_api_Constants::ORDER_BY_OLDEST        => 'date published (oldest first)',   //>(translatable)<
            tubepress_vimeo_api_Constants::ORDER_BY_RANDOM        => 'randomly',                        //>(translatable)<
            tubepress_vimeo_api_Constants::ORDER_BY_RATING        => 'rating',                          //>(translatable)<
            tubepress_vimeo_api_Constants::ORDER_BY_RELEVANCE     => 'relevance',                       //>(translatable)<
            tubepress_vimeo_api_Constants::ORDER_BY_VIEW_COUNT    => 'view count',                      //>(translatable)<
        );
    }

    /**
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getMapOfPerPageSortNamesToUntranslatedLabels()
    {
        return array(

            tubepress_vimeo_api_Constants::PER_PAGE_SORT_COMMENT_COUNT => 'comment count',  //>(translatable)<
            tubepress_vimeo_api_Constants::PER_PAGE_SORT_DURATION      => 'length',         //>(translatable)<
            tubepress_vimeo_api_Constants::PER_PAGE_SORT_NEWEST        => 'date published (newest first)',   //>(translatable)<
            tubepress_vimeo_api_Constants::PER_PAGE_SORT_OLDEST        => 'date published (oldest first)',   //>(translatable)<
            tubepress_vimeo_api_Constants::PER_PAGE_SORT_RATING        => 'title',                           //>(translatable)<
            tubepress_vimeo_api_Constants::PER_PAGE_SORT_VIEW_COUNT    => 'view count',                      //>(translatable)<
        );
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getAttributeNameOfItemDescription()
    {
        return tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION;
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getAttributeNameOfItemTitle()
    {
        return tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE;
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getAttributeNameOfItemId()
    {
        return tubepress_core_media_item_api_Constants::ATTRIBUTE_ID;
    }

    /**
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getMapOfFormattedDateAttributeNamesToUnixTimeAttributeNames()
    {
        return array(
            tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED =>
                tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME
        );
    }

    /**
     * @param tubepress_core_media_item_api_MediaItem $first
     * @param tubepress_core_media_item_api_MediaItem $second
     * @param string                                $perPageSort
     *
     * @return int
     */
    public function compareForPerPageSort(tubepress_core_media_item_api_MediaItem $first, tubepress_core_media_item_api_MediaItem $second, $perPageSort)
    {
        $attributeName = null;
        $desc          = true;

        switch ($perPageSort) {

            case tubepress_vimeo_api_Constants::PER_PAGE_SORT_COMMENT_COUNT:
                $attributeName = tubepress_core_media_item_api_Constants::ATTRIBUTE_COMMENT_COUNT;
                break;

            case tubepress_vimeo_api_Constants::PER_PAGE_SORT_DURATION:
                $attributeName = tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_SECONDS;
                break;

            case tubepress_vimeo_api_Constants::PER_PAGE_SORT_NEWEST:
                $attributeName = tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME;
                break;

            case tubepress_vimeo_api_Constants::PER_PAGE_SORT_OLDEST:
                $attributeName = tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME;
                $desc          = false;
                break;

            case tubepress_vimeo_api_Constants::PER_PAGE_SORT_RATING:
                $attributeName = tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_AVERAGE;
                break;

            case tubepress_vimeo_api_Constants::PER_PAGE_SORT_VIEW_COUNT:
                $attributeName = tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT;
                break;

            default:
                return 0;
        }

        return $this->_itemSorter->numericSort($first, $second, $attributeName, $desc);
    }

    /**
     * @return string[]
     *
     * @api
     * @since 4.0.0
     */
    public function getAttributeNamesOfIntegersToFormat()
    {
        return array(

            tubepress_core_media_item_api_Constants::ATTRIBUTE_LIKES_COUNT,
            tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT,
            tubepress_core_media_item_api_Constants::ATTRIBUTE_COMMENT_COUNT
        );
    }

    /**
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getMapOfHhMmSsAttributeNamesToSecondsAttributeNames()
    {
        return array(

            tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_FORMATTED =>
                tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_SECONDS
        );
    }

    private function _getSort($mode)
    {
        /**
         * 'vimeoUploadedBy'    : newest, oldest, most_played, most_commented, or most_liked
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getUploaded
         *
         * 'vimeoLikes'         : newest, oldest, most_played, most_commented, or most_liked
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getLikes
         *
         * 'vimeoAppearsIn'     : newest, oldest, most_played, most_commented, or most_liked
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getAppearsIn
         *
         * 'vimeoSearch'        : newest, oldest, most_played, most_commented, or most_liked, or relevant
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getByTag
         *
         * 'vimeoCreditedTo'    : newest, oldest, most_played, most_commented, or most_liked
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getAll
         *
         * 'vimeoChannel'       : N/A
         * 'vimeoAlbum'         : N/A
         *
         * 'vimeoGroup'         : newest, oldest, most_played, most_commented, most_liked, or random
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.groups.getItems
         */

        /* these two modes can't be sorted */
        if ($mode == tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL
            || $mode == tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_ALBUM) {

            return '';
        }

        $order = $this->_context->get(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY);

        if ($order === tubepress_core_media_provider_api_Constants::ORDER_BY_DEFAULT) {

            return $this->_calculateDefaultSortOrder($mode);
        }

        /* handle "relevance" sort */
        if ($mode == tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH
            && $order == tubepress_vimeo_api_Constants::ORDER_BY_RELEVANCE) {

            return self::$_SORT_RELEVANT;
        }

        /* handle "random" sort */
        if ($mode == tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_GROUP
            && $order == tubepress_vimeo_api_Constants::ORDER_BY_RANDOM) {

            return $order;
        }

        switch ($order) {

            case tubepress_vimeo_api_Constants::ORDER_BY_NEWEST:

                return self::$_SORT_NEWEST;

            case tubepress_vimeo_api_Constants::ORDER_BY_OLDEST:

                return self::$_SORT_OLDEST;

            case tubepress_vimeo_api_Constants::ORDER_BY_VIEW_COUNT:

                return self::$_SORT_MOST_PLAYED;

            case tubepress_vimeo_api_Constants::ORDER_BY_COMMENT_COUNT:

                return self::$_SORT_MOST_COMMENTS;

            case tubepress_vimeo_api_Constants::ORDER_BY_RATING:

                return self::$_SORT_MOST_LIKED;

            default:

                return '';
        }
    }

    private function _buildUrl($params)
    {
        $params[self::$_URL_PARAM_FORMAT] = 'php';

        $asString = self::$_URL_BASE . '?' . http_build_query($params, '', '&');

        return $this->_urlFactory->fromString($asString);
    }

    private function _verifyKeyAndSecretExists()
    {
        if ($this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY) === '') {

            throw new RuntimeException('Missing Vimeo API Consumer Key.');
        }
        if ($this->_context->get(tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET) === '') {

            throw new RuntimeException('Missing Vimeo API Consumer Secret.');
        }
    }

    private function _calculateDefaultSortOrder($currentMode)
    {
        switch ($currentMode) {

            case tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH:

                return self::$_SORT_RELEVANT;

            default:

                return self::$_SORT_NEWEST;
        }
    }

    private function _getErrorMessageFromVimeo()
    {
        $unserialized = $this->_unserialized;

        if (!$unserialized || !isset($unserialized->stat) || $unserialized->stat !== 'fail') {

            return 'Vimeo responded with an unknown error.';
        }

        return 'Vimeo responded to TubePress with an error: ' . $unserialized->err->msg;
    }

    /**
     * Fetch a media page.
     *
     * @param int $currentPage The requested page number of the gallery.
     *
     * @return tubepress_core_media_provider_api_Page The media gallery page for this page. May be empty, never null.
     *
     * @throws tubepress_core_media_provider_api_exception_ProviderException
     *
     * @api
     * @since 4.0.0
     */
    public function fetchPage($currentPage)
    {
        throw new LogicException();
    }

    /**
     * Fetch a single media item.
     *
     * @param string $itemId The item ID to fetch.
     *
     * @return tubepress_core_media_item_api_MediaItem The media item, or null if unable to retrive.
     *
     * @throws tubepress_core_media_provider_api_exception_ProviderException
     *
     * @api
     * @since 4.0.0
     */
    public function fetchSingle($itemId)
    {
        throw new LogicException();
    }

    /**
     * Get the item ID of an element of the feed.
     *
     * @param integer $index The index into the feed.
     * @param mixed $feed The raw feed.
     *
     * @return string The globally unique item ID.
     *
     * @api
     * @since 4.0.0
     */
    public function getIdForItemAtIndex($index, $feed)
    {
        return $this->_videoArray[$index]->id;
    }
}