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
 * Base class for video providers. This is a complex class, but it's also at the heart of TubePress.
 */
abstract class tubepress_impl_provider_AbstractPluggableVideoProviderService implements tubepress_spi_provider_PluggableVideoProviderService
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * Fetch a video gallery page.
     *
     * @param int $currentPage The requested page number of the gallery.
     *
     * @return tubepress_api_video_VideoGalleryPage The video gallery page for this page. May be empty, never null.
     */
    public final function fetchVideoGalleryPage($currentPage)
    {
        $this->_cacheLogger();

        $result       = new tubepress_api_video_VideoGalleryPage();
        $debugEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($debugEnabled) {

            $this->_logger->debug(sprintf('Current page number is %d', $currentPage));
        }

        $url = $this->buildGalleryUrl($currentPage);

        if ($debugEnabled) {

            $this->_logger->debug(sprintf('URL to fetch is <code>%s</code>', $url));
        }

        $rawFeed                  = $this->_fetchFeedAndPrepareForAnalysis($url);
        $reportedTotalResultCount = $this->getTotalResultCount($rawFeed);

        /**
         * If no results, we can shortcut things here.
         */
        if ($reportedTotalResultCount < 1) {

            $result->setTotalResultCount(0);
            $result->setVideos(array());

            return $result;
        }

        if ($debugEnabled) {

            $this->_logger->debug(sprintf('Reported total result count is %d video(s)', $reportedTotalResultCount));
        }

        /* convert the feed to videos */
        $videoArray = $this->_feedToVideoArray($rawFeed);

        if (count($videoArray) == 0) {

            $result->setTotalResultCount(0);
            $result->setVideos(array());

            return $result;
        }

        $result->setTotalResultCount($reportedTotalResultCount);
        $result->setVideos($videoArray);

        return $result;
    }

    /**
     * Fetch a single video.
     *
     * @param string $videoId The video ID to fetch.
     *
     * @return tubepress_api_video_Video The video, or null if unable to retrive.
     */
    public final function fetchSingleVideo($videoId)
    {
        $this->_cacheLogger();

        $isLoggerDebugEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($isLoggerDebugEnabled) {

            $this->_logger->debug(sprintf('Fetching video with ID <code>%s</code>', $videoId));
        }

        $videoUrl = $this->buildSingleVideoUrl($videoId);

        if ($isLoggerDebugEnabled) {

            $this->_logger->debug(sprintf('URL to fetch is <a href="%s">this</a>', $videoUrl));
        }

        $feed       = $this->_fetchFeedAndPrepareForAnalysis($videoUrl);
        $videoArray = $this->_feedToVideoArray($feed);
        $toReturn   = null;

        if (! empty($videoArray)) {

            $toReturn = $videoArray[0];
        }

        return $toReturn;
    }

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return string The request URL for this gallery.
     */
    protected abstract function buildGalleryUrl($currentPage);

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return string The URL for the single video given.
     */
    protected abstract function buildSingleVideoUrl($id);

    /**
     * Count the total videos in this feed result.
     *
     * @param mixed $feed The raw feed from the provider.
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     */
    protected abstract function getTotalResultCount($feed);

    /**
     *
     *
     * @return ehough_epilog_psr_LoggerInterface
     */
    protected abstract function getLogger();

    /**
     * Determine if we can build a video from this element of the feed.
     *
     * @param integer $index The index into the feed.
     *
     * @return boolean True if we can build a video from this element, false otherwise.
     */
    protected abstract function canWorkWithVideoAtIndex($index);

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @param mixed $feed The feed.
     *
     * @return integer An estimated count of videos in this feed.
     */
    protected abstract function countVideosInFeed($feed);




    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed $feed The feed to construct.
     *
     * @return void
     */
    protected function prepareForFeedAnalysis($feed)
    {
        //override point
    }

    /**
     * Perform post-construction activites for the feed.
     *
     * @param mixed $feed The feed we used.
     *
     * @return void
     */
    protected function onFeedAnalysisComplete($feed)
    {
        //override point
    }

    /**
     * Let's subclasses add arguments to the video construction event.
     *
     * @param tubepress_api_event_EventInterface $event The event we're about to fire.
     */
    protected function onBeforeFiringVideoConstructionEvent(tubepress_api_event_EventInterface $event)
    {
        //override point
    }

    private function _feedToVideoArray($feed)
    {
        $toReturn       = array();
        $total          = $this->countVideosInFeed($feed);
        $isDebugEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($isDebugEnabled) {

            $this->_logger->debug(sprintf('Now attempting to build %d video(s) from raw feed', $total));
        }

        for ($index = 0; $index < $total; $index++) {

            if (! $this->canWorkWithVideoAtIndex($index)) {

                if ($isDebugEnabled) {

                    $this->_logger->debug(sprintf('Skipping video at index %d', $index));
                }

                continue;
            }

            /*
             * Let's build a video!
             */
            $video = new tubepress_api_video_Video();

            /*
             * Every video needs to have a provider.
             */
            $video->setAttribute(tubepress_api_video_Video::ATTRIBUTE_PROVIDER_NAME, $this->getName());

            /*
             * Let add-ons build the rest of the video.
             */
            $event = new tubepress_spi_event_EventBase($video);
            $event->setArgument('zeroBasedFeedIndex', $index);
            $event->setArgument('rawFeed', $feed);

            /*
             * Let subclasses add to the event.
             */
            $this->onBeforeFiringVideoConstructionEvent($event);

            $video = $this->_fireEventAndGetSubject(

                tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION,
                $event
            );

            array_push($toReturn, $video);
        }

        $this->onFeedAnalysisComplete($feed);

        if ($isDebugEnabled) {

            $this->_logger->debug(sprintf('Built %d video(s) from raw feed', sizeof($toReturn)));
        }

        return $toReturn;
    }

    private function _fetchFeedAndPrepareForAnalysis($url)
    {
        $feedFetcher = tubepress_impl_patterns_sl_ServiceLocator::getFeedFetcher();
        $context     = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $useCache    = $context->get(tubepress_api_const_options_names_Cache::CACHE_ENABLED);
        $rawFeed     = $feedFetcher->fetch($url, $useCache);

        $this->prepareForFeedAnalysis($rawFeed);

        return $rawFeed;
    }

    private function _cacheLogger()
    {
        if (! isset($this->_logger)) {

            $this->_logger = $this->getLogger();
        }
    }

    private function _fireEventAndGetSubject($eventName, tubepress_api_event_EventInterface $event)
    {
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }
}
