<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Trims down the number of results based on various criteria.
 */
class tubepress_plugins_core_filters_videogallerypage_ResultCountCapper
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Result Count Capper');
    }

    public function onVideoGalleryPage(tubepress_api_event_VideoGalleryPageConstruction $event)
    {
        $totalResults = $event->getSubject()->getTotalResultCount();
        $context      = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $limit        = $context->get(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $firstCut     = $limit == 0 ? $totalResults : min($limit, $totalResults);
        $secondCut    = min($firstCut, self::_calculateRealMax($context, $firstCut));
        $videos       = $event->getSubject()->getVideos();
        $resultCount  = count($videos);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Effective total result count (taking into account user-defined limit) is %d video(s)', $secondCut));
        }

        if ($resultCount > $secondCut) {

            if ($this->_logger->isDebugEnabled()) {

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
            case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH:
                //http://code.google.com/apis/youtube/2.0/reference.html#Videos_feed
                return 999;
            case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES:
                //http://code.google.com/apis/youtube/2.0/reference.html#User_favorites_feed
                return 50;
            case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST:
                //http://code.google.com/apis/youtube/2.0/reference.html#Playlist_feed
                return 200;
        }

        return $reported;
    }
}
