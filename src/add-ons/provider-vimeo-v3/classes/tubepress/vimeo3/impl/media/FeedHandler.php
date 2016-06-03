<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_vimeo3_impl_media_FeedHandler implements tubepress_spi_media_HttpFeedHandlerInterface
{
    private static $_URL_BASE = 'https://api.vimeo.com';

    private static $_PATH_SEGMENT_VIDEOS = 'videos';
    private static $_PATH_SEGMENT_USERS  = 'users';

    private static $_SORT_ALPHABETICAL = 'alphabetical';
    private static $_SORT_ASC          = 'asc';
    private static $_SORT_CREATED_TIME = 'created_time';
    private static $_SORT_DATE         = 'date';
    private static $_SORT_DESC         = 'desc';
    private static $_SORT_DURATION     = 'duration';
    private static $_SORT_LIKES        = 'likes';
    private static $_SORT_MANUAL       = 'manual';
    private static $_SORT_PLAYS        = 'plays';
    private static $_SORT_RELEVANT     = 'relevant';

    /**
     * @var tubepress_api_log_LoggerInterface Logger.
     */
    private $_logger;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var array
     */
    private $_decodedJson;

    /**
     * @var array
     */
    private $_videoArray;

    /**
     * @var bool
     */
    private $_invokedAtLeastOnce;

    public function __construct(tubepress_api_log_LoggerInterface      $logger,
        tubepress_api_url_UrlFactoryInterface  $urlFactory,
        tubepress_api_options_ContextInterface $context)
    {
        $this->_logger     = $logger;
        $this->_urlFactory = $urlFactory;
        $this->_context    = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vimeo_v3';
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlForPage($currentPage)
    {
        $mode = $this->_context->get(tubepress_api_options_Names::GALLERY_SOURCE);
        $url  = $this->_urlFactory->fromString(self::$_URL_BASE);

        $this->_startGalleryUrl($mode, $url);
        $this->_addPagination($currentPage, $url);
        $this->_addSort($url);

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlForItem($id)
    {
        $url = $this->_urlFactory->fromString(self::$_URL_BASE);

        $url->addPath(self::$_PATH_SEGMENT_VIDEOS)
            ->addPath($id);

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalResultCount()
    {
        return isset($this->_decodedJson['total']) ? intval($this->_decodedJson['total']) : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonUnableToUseItemAtIndex($index)
    {
        // New Vimeo now displays a "Cannot display here" image, so this can be completely removed
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentResultCount()
    {
        return sizeof($this->_videoArray);
    }

    /**
     * {@inheritdoc}
     */
    public function onAnalysisStart($feed, tubepress_api_url_UrlInterface $url)
    {
        $this->_decodedJson = json_decode($feed, true);
        $this->_videoArray  = array();

        /*
         * Make sure we can actually unserialize the feed.
         */
        if (!is_array($this->_decodedJson)) {

            throw new RuntimeException('Unable to decode JSON from Vimeo');
        }

        /*
         * Make sure Vimeo is happy.
         */
        if (isset($this->_decodedJson['error'])) {

            $message = $this->_decodedJson['error'];

            if ($message === 'The requested video could not be found') {

                $this->_videoArray = array();

                return;
            }

            throw new RuntimeException($message);
        }

        /*
         * Is this a page of videos.
         */
        if (isset($this->_decodedJson['data'])) {

            $this->_videoArray = $this->_decodedJson['data'];

            return;
        }

        /*
         * Must be a single videos.
         */
        if (isset($this->_decodedJson)) {

            $this->_videoArray = array($this->_decodedJson);

            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onAnalysisComplete()
    {
        unset($this->_videoArray);
        unset($this->_decodedJson);
    }

    private function _addSort(tubepress_api_url_UrlInterface $url)
    {
        $mode  = $this->_context->get(tubepress_api_options_Names::GALLERY_SOURCE);
        $order = $this->_context->get(tubepress_api_options_Names::FEED_ORDER_BY);
        $query = $url->getQuery();

        $_dateDesc  = array(self::$_SORT_DATE, self::$_SORT_DESC);
        $_dateAsc   = array(self::$_SORT_DATE, self::$_SORT_ASC);
        $_alphaDesc = array(self::$_SORT_ALPHABETICAL, self::$_SORT_DESC);
        $_alphaAsc  = array(self::$_SORT_ALPHABETICAL, self::$_SORT_ASC);
        $_shortest  = array(self::$_SORT_DURATION, self::$_SORT_ASC);
        $_longest   = array(self::$_SORT_DURATION, self::$_SORT_DESC);
        $_viewCount = array(self::$_SORT_PLAYS, self::$_SORT_DESC);
        $_likes     = array(self::$_SORT_LIKES, self::$_SORT_DESC);
        $_relevance = array(self::$_SORT_RELEVANT);

        $map = array(

            /*
             * https://developer.vimeo.com/api/endpoints/me#/albums
             *
             *   manual
             *   date
             *   alphabetical
             *   plays
             *   likes
             * comments
             *   duration
             * modified_time
             */
            tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_ALBUM => array(

                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST           => $_dateDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST           => $_dateAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => $_alphaAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => $_alphaDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => $_shortest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => $_longest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LIKES            => $_likes,
                tubepress_vimeo3_api_Constants::ORDER_BY_VIEW_COUNT       => $_viewCount,
            ),

            /*
             * https://developer.vimeo.com/api/endpoints/users#/{user_id}/appearances
             *
             *   date
             *   alphabetical
             *   plays
             *   likes
             * comments
             *   duration
             */
            tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN => array(

                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST           => $_dateDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST           => $_dateAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => $_alphaAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => $_alphaDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => $_shortest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => $_longest,
                tubepress_vimeo3_api_Constants::ORDER_BY_VIEW_COUNT       => $_viewCount,
                tubepress_vimeo3_api_Constants::ORDER_BY_LIKES            => $_likes,
            ),

            /*
             * https://developer.vimeo.com/api/endpoints/categories#/{category}/videos
             *
             *   relevant
             *   date
             *   alphabetical
             *   plays
             *   likes
             * comments
             *   duration
             */
            tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CATEGORY => array(

                tubepress_vimeo3_api_Constants::ORDER_BY_RELEVANCE        => $_relevance,
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST           => $_dateDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST           => $_dateAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => $_alphaAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => $_alphaDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_VIEW_COUNT       => $_viewCount,
                tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => $_shortest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => $_longest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LIKES            => $_likes,
            ),

            /*
             * https://developer.vimeo.com/api/endpoints/channels#/{channel_id}/videos
             *
             *   date
             *   alphabetical
             *   plays
             *   likes
             * comments
             *   duration
             * added
             * modified_time
             *   manual
             */
            tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL => array(

                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST           => $_dateDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST           => $_dateAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => $_alphaAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => $_alphaDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_VIEW_COUNT       => $_viewCount,
                tubepress_vimeo3_api_Constants::ORDER_BY_LIKES            => $_likes,
                tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => $_shortest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => $_longest,
            ),

            /*
             * https://developer.vimeo.com/api/endpoints/groups#/{group_id}/videos
             *
             *   date
             *   alphabetical
             *   plays
             *   likes
             * comments
             *   duration
             */
            tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_GROUP => array(

                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST           => $_dateDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST           => $_dateAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => $_alphaAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => $_alphaDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_VIEW_COUNT       => $_viewCount,
                tubepress_vimeo3_api_Constants::ORDER_BY_LIKES            => $_likes,
                tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => $_shortest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => $_longest,
            ),

            /*
             * https://developer.vimeo.com/api/endpoints/users#/{user_id}/likes
             *
             *   date
             *   alphabetical
             *   plays
             *   likes
             * comments
             *   duration
             */
            tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_LIKES => array(

                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST           => $_dateDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST           => $_dateAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => $_alphaAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => $_alphaDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => $_shortest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => $_longest,
                tubepress_vimeo3_api_Constants::ORDER_BY_VIEW_COUNT       => $_viewCount,
                tubepress_vimeo3_api_Constants::ORDER_BY_LIKES            => $_likes,
            ),

            /*
             * https://developer.vimeo.com/api/endpoints/videos#
             *
             *   relevant
             *   date
             *   alphabetical
             *   plays
             *   likes
             * comments
             *   duration
             */
            tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH => array(

                tubepress_vimeo3_api_Constants::ORDER_BY_RELEVANCE        => $_relevance,
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST           => $_dateDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST           => $_dateAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => $_alphaAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => $_alphaDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => $_shortest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => $_longest,
                tubepress_vimeo3_api_Constants::ORDER_BY_VIEW_COUNT       => $_viewCount,
                tubepress_vimeo3_api_Constants::ORDER_BY_LIKES            => $_likes,
            ),

            /*
             * https://developer.vimeo.com/api/endpoints/tags#/{word}/videos
             *
             * created_time
             * name
             * duration
             */
            tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_TAG => array(

                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST => array(
                    self::$_SORT_CREATED_TIME, self::$_SORT_DESC,
                ),
                tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST => array(
                    self::$_SORT_CREATED_TIME, self::$_SORT_ASC,
                ),
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => $_alphaAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => $_alphaDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => $_shortest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => $_longest,
            ),

            /*
             * https://developer.vimeo.com/api/endpoints/users#/{user_id}/videos
             *
             *   date
             *   alphabetical
             *   plays
             *   likes
             * comments
             *   duration
             * default
             * modified_time
             */
            tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY => array(

                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST           => $_dateDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST           => $_dateAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => $_alphaAsc,
                tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => $_alphaDesc,
                tubepress_vimeo3_api_Constants::ORDER_BY_VIEW_COUNT       => $_viewCount,
                tubepress_vimeo3_api_Constants::ORDER_BY_LIKES            => $_likes,
                tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => $_shortest,
                tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => $_longest,
            ),
        );

        if (!isset($map[$mode]) || !isset($map[$mode][$order])) {

            $finalSort = $this->_calculateDefaultSortOrder($mode);

        } else {

            $finalSort = $map[$mode][$order];
        }

        if (count($finalSort) === 0) {

            return;
        }

        $query->set('sort', $finalSort[0]);

        if (count($finalSort) > 1) {

            $query->set('direction', $finalSort[1]);
        }
    }

    private function _calculateDefaultSortOrder($currentMode)
    {
        switch ($currentMode) {

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_ALBUM:
            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL:

                return array(self::$_SORT_MANUAL);
                
            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN;
            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_GROUP:
            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_LIKES:

                return array(self::$_SORT_DATE, self::$_SORT_DESC);

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CATEGORY:
            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH:

                return array(self::$_SORT_RELEVANT);

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_TAG:

                return array();

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY:

                return array('default');
        }

        return array();
    }

    private function _startGalleryUrl($mode, tubepress_api_url_UrlInterface $url)
    {
        switch ($mode) {

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_ALBUM:

                $albumId = $this->_context->get(tubepress_vimeo3_api_Constants::OPTION_VIMEO_ALBUM_VALUE);

                $url->addPath('albums')
                    ->addPath($albumId)
                    ->addPath(self::$_PATH_SEGMENT_VIDEOS);

                return;

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN:

                $userId = $this->_context->get(tubepress_vimeo3_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE);

                $url->addPath(self::$_PATH_SEGMENT_USERS)
                    ->addPath($userId)
                    ->addPath('appearances');

                return;

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CATEGORY:

                $categoryId = $this->_context->get(tubepress_vimeo3_api_Constants::OPTION_VIMEO_CATEGORY_VALUE);

                $url->addPath('categories')
                    ->addPath($categoryId)
                    ->addPath(self::$_PATH_SEGMENT_VIDEOS);

                return;

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL:

                $channelId = $this->_context->get(tubepress_vimeo3_api_Constants::OPTION_VIMEO_CHANNEL_VALUE);

                $url->addPath('channels')
                    ->addPath($channelId)
                    ->addPath(self::$_PATH_SEGMENT_VIDEOS);

                return;

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_GROUP:

                $groupId = $this->_context->get(tubepress_vimeo3_api_Constants::OPTION_VIMEO_GROUP_VALUE);

                $url->addPath('groups')
                    ->addPath($groupId)
                    ->addPath(self::$_PATH_SEGMENT_VIDEOS);

                return;

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH:

                $filter = $this->_context->get(tubepress_api_options_Names::SEARCH_ONLY_USER);
                $query  = $this->_context->get(tubepress_vimeo3_api_Constants::OPTION_VIMEO_SEARCH_VALUE);

                if ($filter) {

                    $newMode = tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY;
                    $this->_context->setEphemeralOption(tubepress_api_options_Names::GALLERY_SOURCE, $newMode);
                    $this->_context->setEphemeralOption(tubepress_vimeo3_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE, $filter);
                    $this->_startGalleryUrl($newMode, $url);

                } else {

                    $url->addPath(self::$_PATH_SEGMENT_VIDEOS);
                }

                $url->getQuery()->set('query', $query);

                return;

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_TAG:

                $tag = $this->_context->get(tubepress_vimeo3_api_Constants::OPTION_VIMEO_TAG_VALUE);

                $url->addPath('tags')
                    ->addPath($tag)
                    ->addPath(self::$_PATH_SEGMENT_VIDEOS);

                return;

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_LIKES:

                $userId = $this->_context->get(tubepress_vimeo3_api_Constants::OPTION_VIMEO_LIKES_VALUE);

                $url->addPath(self::$_PATH_SEGMENT_USERS)
                    ->addPath($userId)
                    ->addPath('likes');

                return;

            case tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY:

                $userId = $this->_context->get(tubepress_vimeo3_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE);

                $url->addPath(self::$_PATH_SEGMENT_USERS)
                    ->addPath($userId)
                    ->addPath(self::$_PATH_SEGMENT_VIDEOS);

                return;
        }

        throw new InvalidArgumentException(sprintf('Unknown Vimeo gallery source: %s', $mode));
    }

    private function _addPagination($currentPage, tubepress_api_url_UrlInterface $url)
    {
        if (isset($this->_invokedAtLeastOnce)) {

            $perPage = $this->_context->get(tubepress_api_options_Names::FEED_RESULTS_PER_PAGE);

        } else {

            $perPage = min($this->_context->get(tubepress_api_options_Names::FEED_RESULTS_PER_PAGE), ceil(2.04));
        }

        $url->getQuery()->set('page', $currentPage)
            ->set('per_page', $perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdForItemAtIndex($index)
    {
        if (!empty($this->_videoArray[$index]['uri'])) {

            return substr($this->_videoArray[$index]['uri'], strlen('/videos/'));
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewItemEventArguments(tubepress_api_media_MediaItem $mediaItemId, $index)
    {
        return array(
            'decodedJson'    => $this->_decodedJson,
            'videoArray'     => $this->_videoArray,
            'zeroBasedIndex' => $index,
        );
    }

    public function __invoke()
    {
        $this->_invokedAtLeastOnce = true;
    }
}
