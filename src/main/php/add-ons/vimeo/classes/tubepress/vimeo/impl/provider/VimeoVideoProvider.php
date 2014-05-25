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
class tubepress_vimeo_impl_provider_VimeoVideoProvider implements tubepress_core_api_provider_EasyHttpProviderInterface
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

    private static $_GALLERY_SOURCE_NAMES = array(

        tubepress_vimeo_api_const_options_Values::VIMEO_ALBUM,
        tubepress_vimeo_api_const_options_Values::VIMEO_APPEARS_IN,
        tubepress_vimeo_api_const_options_Values::VIMEO_CHANNEL,
        tubepress_vimeo_api_const_options_Values::VIMEO_CREDITED,
        tubepress_vimeo_api_const_options_Values::VIMEO_GROUP,
        tubepress_vimeo_api_const_options_Values::VIMEO_LIKES,
        tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH,
        tubepress_vimeo_api_const_options_Values::VIMEO_UPLOADEDBY
    );

    /**
     * @var tubepress_api_log_LoggerInterface Logger.
     */
    private $_logger;

    /**
     * @var tubepress_core_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    private $_unserialized;

    private $_videoArray;

    public function __construct(tubepress_api_log_LoggerInterface                 $logger,
                                tubepress_core_api_url_UrlFactoryInterface        $urlFactory,
                                tubepress_core_api_options_ContextInterface       $context,
                                tubepress_core_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_logger          = $logger;
        $this->_urlFactory      = $urlFactory;
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * @return array An array of the valid option values for the "mode" option.
     */
    public final function getGallerySourceNames()
    {
        return self::$_GALLERY_SOURCE_NAMES;
    }

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'vimeo';
    }

    /**
     * @return string The human-readable name of this video provider.
     */
    public final function getFriendlyName()
    {
        return 'Vimeo';
    }

    /**
     * @return array An array of meta names
     */
    public final function getAdditionalMetaNames()
    {
        return array(

            tubepress_vimeo_api_const_options_Names::LIKES
        );
    }

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return tubepress_core_api_url_UrlInterface The request URL for this gallery.
     */
    public function urlBuildForGallery($currentPage)
    {
        $params = array();
        $mode   = $this->_context->get(tubepress_core_api_const_options_Names::GALLERY_SOURCE);

        $this->_verifyKeyAndSecretExists();

        switch ($mode) {

            case tubepress_vimeo_api_const_options_Values::VIMEO_UPLOADEDBY:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETUPLOADED;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_UPLOADEDBY_VALUE);

                break;

            case tubepress_vimeo_api_const_options_Values::VIMEO_LIKES:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETLIKES;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_LIKES_VALUE);

                break;

            case tubepress_vimeo_api_const_options_Values::VIMEO_APPEARS_IN:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_APPEARSIN;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_APPEARS_IN_VALUE);

                break;

            case tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH:

                $params[self::$_URL_PARAM_METHOD] = self::$_METHOD_VIDEOS_SEARCH;
                $params[self::$_URL_PARAM_QUERY]  = $this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_SEARCH_VALUE);

                $filter = $this->_context->get(tubepress_core_api_const_options_Names::SEARCH_ONLY_USER);

                if ($filter != '') {

                    $params[self::$_URL_PARAM_USER_ID] = $filter;
                }

                break;

            case tubepress_vimeo_api_const_options_Values::VIMEO_CREDITED:

                $params[self::$_URL_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETALL;
                $params[self::$_URL_PARAM_USER_ID] = $this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_CREDITED_VALUE);

                break;

            case tubepress_vimeo_api_const_options_Values::VIMEO_CHANNEL:

                $params[self::$_URL_PARAM_METHOD]     = self::$_METHOD_CHANNEL_GETVIDEOS;
                $params[self::$_URL_PARAM_CHANNEL_ID] = $this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_CHANNEL_VALUE);

                break;

            case tubepress_vimeo_api_const_options_Values::VIMEO_ALBUM:

                $params[self::$_URL_PARAM_METHOD]   = self::$_METHOD_ALBUM_GETVIDEOS;
                $params[self::$_URL_PARAM_ALBUM_ID] = $this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_ALBUM_VALUE);

                break;

            case tubepress_vimeo_api_const_options_Values::VIMEO_GROUP:

                $params[self::$_URL_PARAM_METHOD]   = self::$_METHOD_GROUP_GETVIDEOS;
                $params[self::$_URL_PARAM_GROUP_ID] = $this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_GROUP_VALUE);
        }

        $params[self::$_URL_PARAM_FULL_RESPONSE] = 'true';
        $params[self::$_URL_PARAM_PAGE]          = $currentPage;
        $params[self::$_URL_PARAM_PER_PAGE]      = $this->_context->get(tubepress_core_api_const_options_Names::RESULTS_PER_PAGE);
        $sort                                    = $this->_getSort($mode);

        if ($sort != '') {

            $params[self::$_URL_PARAM_SORT] = $sort;
        }

        return $this->_finishUrl($params, tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY);
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return tubepress_core_api_url_UrlInterface The URL for the single video given.
     */
    public function urlBuildForSingle($id)
    {
        $this->_verifyKeyAndSecretExists();

        if (! $this->singleElementRecognizesId($id)) {

            throw new InvalidArgumentException("Unable to build Vimeo URL for video with ID $id");
        }

        $params                             = array();
        $params[self::$_URL_PARAM_METHOD]   = self::$_METHOD_VIDEOS_GETINFO;
        $params[self::$_URL_PARAM_VIDEO_ID] = $id;

        return $this->_finishUrl($params, tubepress_vimeo_api_const_VimeoEventNames::URL_SINGLE);
    }

    /**
     * Count the total videos in this feed result.
     *
     * @param mixed $feed The raw feed from the provider.
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     */
    public function feedGetTotalResultCount($feed)
    {
        return isset($this->_unserialized->videos->total) ? $this->_unserialized->videos->total : 0;
    }

    /**
     * Determine if we can build a video from this element of the feed.
     *
     * @param integer $index The index into the feed.
     *
     * @return boolean True if we can build a video from this element, false otherwise.
     */
    public function feedCanWorkWithVideoAtIndex($index)
    {
        return $this->_videoArray[$index]->embed_privacy !== 'nowhere';
    }

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @param mixed $feed The feed.
     *
     * @return integer An estimated count of videos in this feed.
     */
    public function feedCountElements($feed)
    {
        return sizeof($this->_videoArray);
    }

    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed $feed The feed to construct.
     *
     * @return void
     */
    public function freePrepareForAnalysis($feed)
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

            throw new RuntimeException($this->_getErrorMessageFromVimeo());
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
     */
    public function feedOnAnalysisComplete($feed)
    {
        unset($this->_videoArray);
        unset($this->_unserialized);
    }

    /**
     * Let's subclasses add arguments to the video construction event.
     *
     * @param tubepress_core_api_event_EventInterface $event The event we're about to fire.
     */
    public function singleElementOnBeforeConstruction(tubepress_core_api_event_EventInterface $event)
    {
        $event->setArgument('unserializedFeed', $this->_unserialized);
        $event->setArgument('videoArray', $this->_videoArray);
    }

    /**
     * Ask this video provider if it recognizes the given video ID.
     *
     * @param string $videoId The globally unique video identifier.
     *
     * @return boolean True if this provider recognizes the given video ID, false otherwise.
     */
    public function singleElementRecognizesId($videoId)
    {
        return is_numeric($videoId);
    }

    private function _finishUrl($params, $eventName)
    {
        $finalUrl = $this->_buildUrl($params);
        $event    = $this->_eventDispatcher->newEventInstance($this->_urlFactory->fromString($finalUrl));

        $this->_eventDispatcher->dispatch($eventName, $event);

        /**
         * @var $url tubepress_core_api_url_UrlInterface
         */
        $url = $event->getSubject();

        return $url;
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
        if ($mode == tubepress_vimeo_api_const_options_Values::VIMEO_CHANNEL
            || $mode == tubepress_vimeo_api_const_options_Values::VIMEO_ALBUM) {

            return '';
        }

        $order = $this->_context->get(tubepress_core_api_const_options_Names::ORDER_BY);

        if ($order === tubepress_core_api_const_options_ValidValues::ORDER_BY_DEFAULT) {

            return $this->_calculateDefaultSortOrder($mode);
        }

        /* handle "relevance" sort */
        if ($mode == tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH
            && $order == tubepress_core_api_const_options_ValidValues::ORDER_BY_RELEVANCE) {

            return self::$_SORT_RELEVANT;
        }

        /* handle "random" sort */
        if ($mode == tubepress_vimeo_api_const_options_Values::VIMEO_GROUP
            && $order == tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM) {

            return $order;
        }

        switch ($order) {

            case tubepress_core_api_const_options_ValidValues::ORDER_BY_NEWEST:

                return self::$_SORT_NEWEST;

            case tubepress_core_api_const_options_ValidValues::ORDER_BY_OLDEST:

                return self::$_SORT_OLDEST;

            case tubepress_core_api_const_options_ValidValues::ORDER_BY_VIEW_COUNT:

                return self::$_SORT_MOST_PLAYED;

            case tubepress_core_api_const_options_ValidValues::ORDER_BY_COMMENT_COUNT:

                return self::$_SORT_MOST_COMMENTS;

            case tubepress_core_api_const_options_ValidValues::ORDER_BY_RATING:

                return self::$_SORT_MOST_LIKED;

            default:

                return '';
        }
    }

    private function _buildUrl($params)
    {
        $params[self::$_URL_PARAM_FORMAT] = 'php';

        return self::$_URL_BASE . '?' . http_build_query($params, '', '&');
    }

    private function _verifyKeyAndSecretExists()
    {
        if ($this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_KEY) === '') {

            throw new RuntimeException('Missing Vimeo API Consumer Key.');
        }
        if ($this->_context->get(tubepress_vimeo_api_const_options_Names::VIMEO_SECRET) === '') {

            throw new RuntimeException('Missing Vimeo API Consumer Secret.');
        }
    }

    private function _calculateDefaultSortOrder($currentMode)
    {
        switch ($currentMode) {

            case tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH:

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
}
