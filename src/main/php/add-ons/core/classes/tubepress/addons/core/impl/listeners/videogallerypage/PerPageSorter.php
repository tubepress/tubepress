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
 * Shuffles videos on request.
 */
class tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Per-page Sorter');
    }

    public function onVideoGalleryPage(tubepress_api_event_EventInterface $event)
    {
        $context          = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $perPageSortOrder = $context->get(tubepress_api_const_options_names_Feed::PER_PAGE_SORT);
        $feedSortOrder    = $context->get(tubepress_api_const_options_names_Feed::ORDER_BY);
        $shouldLog        = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        /** No sort requested? */
        if ($perPageSortOrder === tubepress_api_const_options_values_PerPageSortValue::NONE) {

            if ($shouldLog) {
                
                $this->_logger->debug('Requested per-page sort order is "none". Not applying per-page sorting.');
            }

            return;
        }

        /** Grab a handle to the videos. */
        $videos = $event->getSubject()->getVideos();

        if ($perPageSortOrder === tubepress_api_const_options_values_PerPageSortValue::RANDOM) {

            if ($shouldLog) {

                $this->_logger->debug('Shuffling videos');
            }

            shuffle($videos);

        } else {

            /** Determine the sort method name. */
            $sortCallback = '_' . $perPageSortOrder . '_compare';

            /** If we have a sorter, use it. */
            if (method_exists($this, $sortCallback)) {

                if ($shouldLog) {

                    $this->_logger->debug(sprintf('Now sorting %s videos on page (%s)', count($videos), $perPageSortOrder));
                }

                uasort($videos, array($this, $sortCallback));

            } else {

                if ($shouldLog) {

                    $this->_logger->debug(sprintf('No sort available for this page (%s)', $perPageSortOrder));
                }
            }
        }

        $videos = array_values($videos);

        /** Modify the feed result. */
        $event->getSubject()->setVideos($videos);
    }

    private function _commentCount_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
    {
        return $this->_compareGreatestToLeast($this->_safeIntVal($one->getCommentCount()), $this->_safeIntVal($two->getCommentCount()));
    }

    private function _duration_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
    {
        return $this->_compareGreatestToLeast($one->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS),
            $two->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS));
    }

    private function _newest_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
    {
        return $this->_compareGreatestToLeast($one->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME),
            $two->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
    }

    private function _oldest_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
    {
        return $this->_compareLeastToGreatest($one->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME),
            $two->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
    }

    private function _rating_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
    {
        return $this->_compareGreatestToLeast(floatval($one->getRatingAverage()), floatval($two->getRatingAverage()));
    }

    private function _title_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
    {
        return strcmp($one->getTitle(), $two->getTitle());
    }

    private function _viewCount_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
    {
        return $this->_compareGreatestToLeast($this->_safeIntVal($one->getViewCount()), $this->_safeIntVal($two->getViewCount()));
    }

    private function _safeIntVal($val)
    {
        if (is_string($val)) {

            $x = str_replace(',', '', $val);

        } else {

            $x = $val;
        }

        return intval($x);
    }

    private function _compareLeastToGreatest($one, $two)
    {
        if ($one == $two) {

            return 0;
        }

        return $one < $two ? -1 : 1;
    }

    private function _compareGreatestToLeast($one, $two)
    {
        if ($one == $two) {

            return 0;
        }

        return $one > $two ? -1 : 1;
    }
}