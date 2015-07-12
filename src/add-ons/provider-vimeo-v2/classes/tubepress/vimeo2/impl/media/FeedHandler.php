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

class tubepress_vimeo2_impl_media_FeedHandler implements tubepress_app_api_media_HttpFeedHandlerInterface
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

    private static $_METHOD_ALBUM_GETVIDEOS    = 'vimeo.albums.getVideos';
    private static $_METHOD_CHANNEL_GETVIDEOS  = 'vimeo.channels.getVideos';
    private static $_METHOD_GROUP_GETVIDEOS    = 'vimeo.groups.getVideos';
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

    /**
     * @var tubepress_platform_api_log_LoggerInterface Logger.
     */
    private $_logger;

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    private $_unserialized;

    private $_videoArray;

    private $_invokedAtLeastOnce;

    public function __construct(tubepress_platform_api_log_LoggerInterface     $logger,
                                tubepress_platform_api_url_UrlFactoryInterface $urlFactory,
                                tubepress_app_api_options_ContextInterface     $context)
    {
        $this->_logger     = $logger;
        $this->_urlFactory = $urlFactory;
        $this->_context    = $context;
    }

    /**
     * @return string The name of this feed handler. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'vimeo_v2';
    }

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return tubepress_platform_api_url_UrlInterface The request URL for this gallery.
     *
     * @api
     * @since 4.0.0
     */
    public function buildUrlForPage($currentPage)
    {
        $params = array();
        $mode   = $this->_context->get(tubepress_app_api_options_Names::GALLERY_SOURCE);

        $this->_verifyKeyAndSecretExists();

        switch ($mode) {

            case tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETUPLOADED;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE);

                break;

            case tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_LIKES:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETLIKES;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_LIKES_VALUE);

                break;

            case tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_APPEARSIN;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE);

                break;

            case tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH:

                $params[self::$_URL_PARAM_METHOD] = self::$_METHOD_VIDEOS_SEARCH;
                $params[self::$_URL_PARAM_QUERY]  = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_SEARCH_VALUE);

                $filter = $this->_context->get(tubepress_app_api_options_Names::SEARCH_ONLY_USER);

                if ($filter != '') {

                    $params[self::$_URL_PARAM_USER_ID] = $filter;
                }

                break;

            case tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CREDITED:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETALL;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_CREDITED_VALUE);

                break;

            case tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL:

                $params[self::$_URL_PARAM_METHOD]     = self::$_METHOD_CHANNEL_GETVIDEOS;
                $params[self::$_URL_PARAM_CHANNEL_ID] = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_CHANNEL_VALUE);

                break;

            case tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_ALBUM:

                $params[self::$_URL_PARAM_METHOD]   = self::$_METHOD_ALBUM_GETVIDEOS;
                $params[self::$_URL_PARAM_ALBUM_ID] = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_ALBUM_VALUE);

                break;

            case tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_GROUP:

                $params[self::$_URL_PARAM_METHOD]   = self::$_METHOD_GROUP_GETVIDEOS;
                $params[self::$_URL_PARAM_GROUP_ID] = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_GROUP_VALUE);
        }

        $params[self::$_URL_PARAM_FULL_RESPONSE] = 'true';
        $params[self::$_URL_PARAM_PAGE]          = $currentPage;

        if (isset($this->_invokedAtLeastOnce)) {
            $params[self::$_URL_PARAM_PER_PAGE] = $this->_context->get(tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE);
        } else {
            $params[self::$_URL_PARAM_PER_PAGE] = min($this->_context->get(tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE), ceil(2.04));
        }

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
     * @return tubepress_platform_api_url_UrlInterface The URL for the single video given.
     *
     * @api
     * @since 4.0.0
     */
    public function buildUrlForItem($id)
    {
        $this->_verifyKeyAndSecretExists();

        $params                             = array();
        $params[self::$_URL_PARAM_METHOD]   = self::$_METHOD_VIDEOS_GETINFO;
        $params[self::$_URL_PARAM_VIDEO_ID] = $id;

        return $this->_buildUrl($params);
    }

    /**
     * Count the total videos in this feed result.
     *
     * @return int The total result count of this query.
     *
     * @api
     * @since 4.0.0
     */
    public function getTotalResultCount()
    {
        return isset($this->_unserialized->videos->total) ? $this->_unserialized->videos->total : 0;
    }

    /**
     * Determine why the given item cannot be used.
     *
     * @param integer $index The index into the feed.
     *
     * @return string The reason why we can't work with this media item, or null if we can.
     *
     * @api
     * @since 4.0.0
     */
    public function getReasonUnableToUseItemAtIndex($index)
    {
        if ($this->_videoArray[$index]->embed_privacy !== 'nowhere') {

            return null;
        }

        return 'Vimeo privacy options restrict embedding.';
    }

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @return integer A count of videos in this feed.
     *
     * @api
     * @since 4.0.0
     */
    public function getCurrentResultCount()
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

            throw new RuntimeException('Unable to unserialize PHP from Vimeo');
        }

        /*
         * Make sure Vimeo is happy.
         */
        if ($this->_unserialized->stat !== 'ok') {

            $message = $this->_getErrorMessageFromVimeo();

            if ($message === 'Video not found') {

                $this->_videoArray = array();
                return;
            }

            throw new RuntimeException($message);
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
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function onAnalysisComplete()
    {
        unset($this->_videoArray);
        unset($this->_unserialized);
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
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.groups.getVideos
         */

        /* these two modes can't be sorted */
        if ($mode == tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL
            || $mode == tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_ALBUM) {

            return '';
        }

        $order = $this->_context->get(tubepress_app_api_options_Names::FEED_ORDER_BY);

        if ($order === tubepress_app_api_options_AcceptableValues::ORDER_BY_DEFAULT) {

            return $this->_calculateDefaultSortOrder($mode);
        }

        /* handle "relevance" sort */
        if ($mode == tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH
            && $order == tubepress_vimeo2_api_Constants::ORDER_BY_RELEVANCE) {

            return self::$_SORT_RELEVANT;
        }

        /* handle "random" sort */
        if ($mode == tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_GROUP
            && $order == tubepress_vimeo2_api_Constants::ORDER_BY_RANDOM) {

            return $order;
        }

        switch ($order) {

            case tubepress_vimeo2_api_Constants::ORDER_BY_NEWEST:

                return self::$_SORT_NEWEST;

            case tubepress_vimeo2_api_Constants::ORDER_BY_OLDEST:

                return self::$_SORT_OLDEST;

            case tubepress_vimeo2_api_Constants::ORDER_BY_VIEW_COUNT:

                return self::$_SORT_MOST_PLAYED;

            case tubepress_vimeo2_api_Constants::ORDER_BY_COMMENT_COUNT:

                return self::$_SORT_MOST_COMMENTS;

            case tubepress_vimeo2_api_Constants::ORDER_BY_RATING:

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
        if ($this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY) === '') {

            throw new RuntimeException('Missing Vimeo API Consumer Key.');
        }
        if ($this->_context->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET) === '') {

            throw new RuntimeException('Missing Vimeo API Consumer Secret.');
        }
    }

    private function _calculateDefaultSortOrder($currentMode)
    {
        switch ($currentMode) {

            case tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH:

                return self::$_SORT_RELEVANT;

            default:

                return self::$_SORT_NEWEST;
        }
    }

    private function _getErrorMessageFromVimeo()
    {
        $unserialized = $this->_unserialized;

        if (!$unserialized || !isset($unserialized->stat) || $unserialized->stat !== 'fail') {

            return 'Unknown Vimeo error.';
        }

        return $unserialized->err->msg;
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
    public function getIdForItemAtIndex($index)
    {
        return $this->_videoArray[$index]->id;
    }

    /**
     * Gather data that might be needed from the feed to build attributes for this media item.
     *
     * @param tubepress_app_api_media_MediaItem $mediaItemId The media item.
     * @param int                               $index       The zero-based index.
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getNewItemEventArguments(tubepress_app_api_media_MediaItem $mediaItemId, $index)
    {
        return array(
            'unserializedFeed' => $this->_unserialized,
            'videoArray'       => $this->_videoArray,
            'zeroBasedIndex'   => $index,
        );
    }

    public function __invoke()
    {
        $this->_invokedAtLeastOnce = true;
    }
}