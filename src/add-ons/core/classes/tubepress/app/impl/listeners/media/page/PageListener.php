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
 * Filters out any videos that the user has in their blacklist.
 */
class tubepress_app_impl_listeners_media_page_PageListener
{
    private $_logger;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var string
     */
    private $_perPageSortOrder;

    /**
     * @var tubepress_lib_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_app_api_media_CollectorInterface
     */
    private $_collector;

    public function __construct(tubepress_platform_api_log_LoggerInterface        $logger,
                                tubepress_app_api_options_ContextInterface        $context,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams,
                                tubepress_app_api_media_CollectorInterface        $collector)
    {
        $this->_logger        = $logger;
        $this->_context       = $context;
        $this->_requestParams = $requestParams;
        $this->_collector     = $collector;
    }

    public function blacklist(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $mediaItems tubepress_app_api_media_MediaItem[]
         */
        $mediaItems     = $event->getSubject()->getItems();
        $blacklist      = $this->_context->get(tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST);
        $itemsToKeep    = array();
        $blacklistCount = 0;

        foreach ($mediaItems as $mediaItem) {

            $id = $mediaItem->getId();

            /* keep videos without an ID or that aren't blacklisted */
            if (!isset($id) || $this->_isNotBlacklisted($id, $blacklist)) {

                $itemsToKeep[] = $mediaItem;

            } else {

                $blacklistCount++;
            }
        }

        /* modify the feed result */
        $event->getSubject()->setItems($itemsToKeep);
    }

    public function prependItems(tubepress_lib_api_event_EventInterface $event)
    {
        $customVideoId = $this->_requestParams->getParamValue('tubepress_item');
        $shouldLog     = $this->_logger->isEnabled();

        /* they didn't set a custom video id */
        if ($customVideoId == '') {

            return;
        }

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Prepending item %s to the gallery', $customVideoId));
        }

        $this->_prependVideo($customVideoId, $event);
    }

    public function perPageSort(tubepress_lib_api_event_EventInterface $event)
    {
        $this->_perPageSortOrder = $this->_context->get(tubepress_app_api_options_Names::FEED_PER_PAGE_SORT);
        $shouldLog               = $this->_logger->isEnabled();

        /** No sort requested? */
        if ($this->_perPageSortOrder === tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_NONE) {

            if ($shouldLog) {

                $this->_logger->debug('Requested per-page sort order is "none". Not applying per-page sorting.');
            }

            return;
        }

        /** Grab a handle to the videos. */
        $mediaItems = $event->getSubject()->getItems();

        if ($this->_perPageSortOrder === tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_RANDOM) {

            if ($shouldLog) {

                $this->_logger->debug('Shuffling videos');
            }

            shuffle($mediaItems);

        } else {

            $mediaItems = $this->_performComplexSort($mediaItems, $shouldLog);
        }

        $mediaItems = array_values($mediaItems);

        /** Modify the feed result. */
        $event->getSubject()->setItems($mediaItems);
    }

    public function capResults(tubepress_lib_api_event_EventInterface $event)
    {
        $totalResults   = $event->getSubject()->getTotalResultCount();
        $limit          = $this->_context->get(tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP);
        $firstCut       = $limit == 0 ? $totalResults : min($limit, $totalResults);
        $secondCut      = min($firstCut, $this->_calculateRealMax($firstCut));
        $mediaItemArray = $event->getSubject()->getItems();
        $resultCount    = count($mediaItemArray);
        $shouldLog      = $this->_logger->isEnabled();

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Effective total result count (taking into account user-defined limit) is %d video(s)', $secondCut));
        }

        if ($resultCount > $secondCut) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Result has %d video(s), limit is %d. So we\'re chopping it down.', $resultCount, $secondCut));
            }

            $event->getSubject()->setItems(array_splice($mediaItemArray, 0, $secondCut - $resultCount));
        }

        $event->getSubject()->setTotalResultCount($secondCut);
    }

    private function _calculateRealMax($reported)
    {
        $mode = $this->_context->get(tubepress_app_api_options_Names::GALLERY_SOURCE);

        switch ($mode) {
            case tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH:
                //http://code.google.com/apis/youtube/2.0/reference.html#Videos_feed
                return 500;
            case tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES:
                //http://code.google.com/apis/youtube/2.0/reference.html#User_favorites_feed
                return 50;
            case tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST:
                //http://code.google.com/apis/youtube/2.0/reference.html#Playlist_feed
                return 200;
        }

        return $reported;
    }

    private function _performComplexSort(array $items, $shouldLog)
    {
//        $provider = $this->_findMostCommonProviderThatSupportsSort($items);
//
//        if (!$provider) {
//
//            if ($shouldLog) {
//
//                $this->_logger->debug(sprintf('Could not find a provider to sort videos by %s',
//                    $this->_perPageSortOrder));
//            }
//
//            return $items;
//        }
//
//        $this->_mostCommonProvider = $provider;
//
//        if ($shouldLog) {
//
//            $this->_logger->debug(sprintf('Provider "%s" chosen to sort videos by %s',
//                $this->_mostCommonProvider->getDisplayName(), $this->_perPageSortOrder));
//        }
//
//        @usort($items, array($this, '__providerSort'));
//
//        return $items;

        return array();
    }

    private function _prependVideo($id, tubepress_lib_api_event_EventInterface $event)
    {
        $mediaItemArray = $event->getSubject()->getItems();

        /* see if the array already has it */
        if (self::_mediaItemArrayAlreadyHasItem($mediaItemArray, $id)) {

            $mediaItemArray = $this->_moveItemUpFront($mediaItemArray, $id);

        } else {

            $mediaItem = $this->_collector->collectSingle($id);

            if ($mediaItem) {

                array_unshift($mediaItemArray, $mediaItem);
            }
        }

        /* modify the feed result */
        $event->getSubject()->setItems($mediaItemArray);
    }

    private function _moveItemUpFront($mediaItems, $targetId)
    {
        /**
         * @var $mediaItems tubepress_app_api_media_MediaItem[]
         */
        for ($x = 0; $x < count($mediaItems); $x++) {

            $mediaItem = $mediaItems[$x];
            $id        = $mediaItem->getId();

            if ($id == $targetId) {

                $saved = $mediaItems[$x];

                unset($mediaItems[$x]);

                array_unshift($mediaItems, $saved);

                break;
            }
        }

        return $mediaItems;
    }

    private function _mediaItemArrayAlreadyHasItem($mediaItems, $targetId)
    {
        /**
         * @var $mediaItem tubepress_app_api_media_MediaItem
         */
        foreach ($mediaItems as $mediaItem) {

            $id = $mediaItem->getId();

            if ($id == $targetId) {

                return true;
            }
        }
        return false;
    }

    private function _isNotBlacklisted($id, $blacklist)
    {
        if (strpos($blacklist, $id) !== false) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->debug(sprintf('Video with ID %s is blacklisted. Skipping it.', $id));
            }

            return false;
        }
        return true;
    }
}