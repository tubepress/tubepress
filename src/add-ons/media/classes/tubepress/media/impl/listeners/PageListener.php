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

class tubepress_media_impl_listeners_PageListener
{
    private $_logger;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var string
     */
    private $_perPageSortOrder;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_api_media_CollectorInterface
     */
    private $_collector;

    /**
     * @var bool
     */
    private $_shouldLog;

    private $_invokedAtLeastOnce;

    private static $_perPageSortMap = array(

        tubepress_api_options_AcceptableValues::PER_PAGE_SORT_COMMENT_COUNT => tubepress_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT,

        tubepress_api_options_AcceptableValues::PER_PAGE_SORT_NEWEST => tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME,

        tubepress_api_options_AcceptableValues::PER_PAGE_SORT_OLDEST => tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME,

        tubepress_api_options_AcceptableValues::PER_PAGE_SORT_DURATION => tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS,

        tubepress_api_options_AcceptableValues::PER_PAGE_SORT_TITLE => tubepress_api_media_MediaItem::ATTRIBUTE_TITLE,

        tubepress_api_options_AcceptableValues::PER_PAGE_SORT_VIEW_COUNT => tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
    );

    public function __construct(tubepress_api_log_LoggerInterface             $logger,
                                tubepress_api_options_ContextInterface        $context,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_media_CollectorInterface        $collector)
    {
        $this->_logger        = $logger;
        $this->_context       = $context;
        $this->_requestParams = $requestParams;
        $this->_collector     = $collector;
        $this->_shouldLog     = $logger->isEnabled();
    }

    public function blacklist(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var tubepress_api_media_MediaItem[]
         */
        $mediaItems     = $event->getSubject()->getItems();
        $blacklist      = $this->_context->get(tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST);
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

    public function prependItems(tubepress_api_event_EventInterface $event)
    {
        $customVideoId = $this->_requestParams->getParamValue('tubepress_item');

        /* they didn't set a custom video id */
        if ($customVideoId == '') {

            return;
        }

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Prepending item <code>%s</code> to the gallery', $customVideoId));
        }

        $this->_prependVideo($customVideoId, $event);
    }

    public function perPageSort(tubepress_api_event_EventInterface $event)
    {
        $this->_perPageSortOrder = $this->_context->get(tubepress_api_options_Names::FEED_PER_PAGE_SORT);

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Per-page sort order is set to <code>%s</code>.', $this->_perPageSortOrder));
        }

        /* No sort requested? */
        if ($this->_perPageSortOrder === tubepress_api_options_AcceptableValues::PER_PAGE_SORT_NONE) {

            if ($this->_shouldLog) {

                $this->_logDebug('Requested per-page sort order is <code>none</code>. Not applying per-page sorting.');
            }

            return;
        }

        /* Grab a handle to the videos. */
        $mediaItems = $event->getSubject()->getItems();

        if ($this->_perPageSortOrder === tubepress_api_options_AcceptableValues::PER_PAGE_SORT_RANDOM) {

            if ($this->_shouldLog) {

                $this->_logDebug('Shuffling videos');
            }

            shuffle($mediaItems);

        } else {

            if (isset(self::$_perPageSortMap[$this->_perPageSortOrder])) {

                usort($mediaItems, array($this, '__perPageSort'));
            }
        }

        $mediaItems = array_values($mediaItems);

        /* Modify the feed result. */
        $event->getSubject()->setItems($mediaItems);
    }

    public function capResults(tubepress_api_event_EventInterface $event)
    {
        $totalResults = $event->getSubject()->getTotalResultCount();
        $limit        = isset($this->_invokedAtLeastOnce) ?
            $this->_context->get(tubepress_api_options_Names::FEED_RESULT_COUNT_CAP) : min(
                ceil((1.1 + 1.0)),
                $this->_context->get(tubepress_api_options_Names::FEED_RESULT_COUNT_CAP)
            );

        $secondCut      = $limit == 0 ? $totalResults : min($limit, $totalResults);
        $mediaItemArray = $event->getSubject()->getItems();
        $resultCount    = count($mediaItemArray);

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Effective total result count (taking into account user-defined limit) is <code>%d</code> video(s)', $secondCut));
        }

        if ($resultCount > $secondCut) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Result has <code>%d</code> video(s), limit is <code>%d</code>. So we\'re chopping it down.', $resultCount, $secondCut));
            }

            $event->getSubject()->setItems(array_splice($mediaItemArray, 0, $secondCut - $resultCount));
        }

        $event->getSubject()->setTotalResultCount($secondCut);
    }

    public function filterDuplicates(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var tubepress_api_media_MediaPage
         */
        $mediaPage = $event->getSubject();
        $items     = $mediaPage->getItems();
        $ids       = array();
        $removed   = 0;

        for ($x = 0; $x < count($items); $x++) {

            $mediaItem = $items[$x];
            $id        = $mediaItem->getId();

            if (in_array($id, $ids)) {

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('Duplicate item detected (<code>%s</code>). Now removing.', $id));
                }

                unset($items[$x]);
                $removed++;

            } else {

                $ids[] = $id;
            }
        }

        $oldTotalResultCount = $mediaPage->getTotalResultCount();
        $newTotalResultCount = max(0, ($oldTotalResultCount - $removed));

        $mediaPage->setTotalResultCount($newTotalResultCount);
        $mediaPage->setItems($items);

        $event->setSubject($mediaPage);
    }

    public function __perPageSort(tubepress_api_media_MediaItem $first, tubepress_api_media_MediaItem $second)
    {
        $attributeName = self::$_perPageSortMap[$this->_perPageSortOrder];

        if (!$first->hasAttribute($attributeName) || !$second->hasAttribute($attributeName)) {

            return 0;
        }

        $firstAttributeValue  = $first->getAttribute($attributeName);
        $secondAttributeValue = $second->getAttribute($attributeName);

        switch ($this->_perPageSortOrder) {

            case tubepress_api_options_AcceptableValues::PER_PAGE_SORT_COMMENT_COUNT:
            case tubepress_api_options_AcceptableValues::PER_PAGE_SORT_NEWEST:
            case tubepress_api_options_AcceptableValues::PER_PAGE_SORT_DURATION:
            case tubepress_api_options_AcceptableValues::PER_PAGE_SORT_VIEW_COUNT:

                $firstAttributeValue  = intval($firstAttributeValue);
                $secondAttributeValue = intval($secondAttributeValue);

                if ($firstAttributeValue == $secondAttributeValue) {

                    return 0;
                }

                if ($firstAttributeValue < $secondAttributeValue) {

                    return 1;
                }

                return -1;

            case tubepress_api_options_AcceptableValues::PER_PAGE_SORT_OLDEST:

                $firstAttributeValue  = intval($firstAttributeValue);
                $secondAttributeValue = intval($secondAttributeValue);

                if ($firstAttributeValue == $secondAttributeValue) {

                    return 0;
                }

                if ($firstAttributeValue < $secondAttributeValue) {

                    return -1;
                }

                return 1;

            default:

                if ($firstAttributeValue == $secondAttributeValue) {

                    return 0;
                }

                if ($firstAttributeValue < $secondAttributeValue) {

                    return -1;
                }

                return 1;
        }
    }

    private function _prependVideo($id, tubepress_api_event_EventInterface $event)
    {
        $mediaItemArray = $event->getSubject()->getItems();

        /* see if the array already has it */
        if (self::_mediaItemArrayAlreadyHasItem($mediaItemArray, $id)) {

            $mediaItemArray = $this->_moveItemUpFront($mediaItemArray, $id);

        } else {

            $mediaItem = $this->_collector->collectSingle($id);

            if ($mediaItem) {

                array_unshift($mediaItemArray, $mediaItem);
                array_pop($mediaItemArray);
            }
        }

        /* modify the feed result */
        $event->getSubject()->setItems($mediaItemArray);
    }

    private function _moveItemUpFront($mediaItems, $targetId)
    {
        /*
         * @var tubepress_api_media_MediaItem[]
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
        /*
         * @var tubepress_api_media_MediaItem
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

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Video with ID <code>%s</code> is blacklisted. Skipping it.', $id));
            }

            return false;
        }

        return true;
    }

    public function __invoke()
    {
        $this->_invokedAtLeastOnce = true;
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Page Listener) %s', $msg));
    }
}
