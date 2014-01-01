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
 * Trims down the number of results based on various criteria.
 */
class tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Result Count Capper');
    }

    public function onVideoGalleryPage(tubepress_api_event_EventInterface $event)
    {
        $totalResults = $event->getSubject()->getTotalResultCount();
        $context      = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $limit        = $context->get(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $firstCut     = $limit == 0 ? $totalResults : min($limit, $totalResults);
        $secondCut    = min($firstCut, self::_calculateRealMax($context, $firstCut));
        $videos       = $event->getSubject()->getVideos();
        $resultCount  = count($videos);
        $shouldLog    = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Effective total result count (taking into account user-defined limit) is %d video(s)', $secondCut));
        }

        if ($resultCount > $secondCut) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Result has %d video(s), limit is %d. So we\'re chopping it down.', $resultCount, $secondCut));
            }

            $event->getSubject()->setVideos(array_splice($videos, 0, $secondCut - $resultCount));
        }

        $event->getSubject()->setTotalResultCount($secondCut);
    }

    private static function _calculateRealMax($context, $reported)
    {
        $mode = $context->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        switch ($mode) {
            case tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH:
                //http://code.google.com/apis/youtube/2.0/reference.html#Videos_feed
                return 999;
            case tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES:
                //http://code.google.com/apis/youtube/2.0/reference.html#User_favorites_feed
                return 50;
            case tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST:
                //http://code.google.com/apis/youtube/2.0/reference.html#Playlist_feed
                return 200;
        }

        return $reported;
    }
}
