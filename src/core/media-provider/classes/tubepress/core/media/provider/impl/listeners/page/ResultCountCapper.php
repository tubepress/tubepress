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
class tubepress_core_media_provider_impl_listeners_page_ResultCountCapper
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_api_log_LoggerInterface           $logger,
                                tubepress_core_options_api_ContextInterface $context)
    {
        $this->_logger  = $logger;
        $this->_context = $context;
    }

    public function onVideoGalleryPage(tubepress_core_event_api_EventInterface $event)
    {
        $totalResults = $event->getSubject()->getTotalResultCount();
        $limit        = $this->_context->get(tubepress_core_media_provider_api_Constants::OPTION_RESULT_COUNT_CAP);
        $firstCut     = $limit == 0 ? $totalResults : min($limit, $totalResults);
        $secondCut    = min($firstCut, $this->_calculateRealMax($firstCut));
        $videos       = $event->getSubject()->getItems();
        $resultCount  = count($videos);
        $shouldLog    = $this->_logger->isEnabled();

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Effective total result count (taking into account user-defined limit) is %d video(s)', $secondCut));
        }

        if ($resultCount > $secondCut) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Result has %d video(s), limit is %d. So we\'re chopping it down.', $resultCount, $secondCut));
            }

            $event->getSubject()->setItems(array_splice($videos, 0, $secondCut - $resultCount));
        }

        $event->getSubject()->setTotalResultCount($secondCut);
    }

    private function _calculateRealMax($reported)
    {
        $mode = $this->_context->get(tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE);

        switch ($mode) {
            case tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH:
                //http://code.google.com/apis/youtube/2.0/reference.html#Videos_feed
                return 500;
            case tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES:
                //http://code.google.com/apis/youtube/2.0/reference.html#User_favorites_feed
                return 50;
            case tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST:
                //http://code.google.com/apis/youtube/2.0/reference.html#Playlist_feed
                return 200;
        }

        return $reported;
    }
}
