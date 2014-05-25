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
class tubepress_core_impl_provider_HttpVideoProviderService implements tubepress_core_api_provider_VideoProviderInterface
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_api_provider_EasyHttpProviderInterface
     */
    private $_delegate;

    /**
     * @var tubepress_core_api_http_HttpClientInterface
     */
    private $_httpClient;

    public function __construct(tubepress_core_api_provider_EasyHttpProviderInterface $delegate,
                                tubepress_api_log_LoggerInterface                 $logger,
                                tubepress_core_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_core_api_http_HttpClientInterface       $httpClient)
    {
        $this->_logger          = $logger;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_delegate        = $delegate;
        $this->_httpClient      = $httpClient;
    }

    /**
     * Fetch a video gallery page.
     *
     * @param int $currentPage The requested page number of the gallery.
     *
     * @return tubepress_core_api_video_VideoGalleryPage The video gallery page for this page. May be empty, never null.
     */
    public final function fetchVideoGalleryPage($currentPage)
    {
        $result       = new tubepress_core_api_video_VideoGalleryPage();
        $debugEnabled = $this->_logger->isEnabled();

        if ($debugEnabled) {

            $this->_logger->debug(sprintf('Current page number is %d', $currentPage));
        }

        $url = $this->_delegate->urlBuildForGallery($currentPage);

        if ($debugEnabled) {

            $this->_logger->debug(sprintf('URL to fetch is <code>%s</code>', $url));
        }

        $rawFeed                  = $this->_fetchFeedAndPrepareForAnalysis($url);
        $reportedTotalResultCount = $this->_delegate->feedGetTotalResultCount($rawFeed);

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
     * @return tubepress_core_api_video_Video The video, or null if unable to retrive.
     */
    public final function fetchSingleVideo($videoId)
    {
        $isLoggerDebugEnabled = $this->_logger->isEnabled();

        if ($isLoggerDebugEnabled) {

            $this->_logger->debug(sprintf('Fetching video with ID <code>%s</code>', $videoId));
        }

        $videoUrl = $this->_delegate->urlBuildForSingle($videoId);

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
     * Ask this video provider if it recognizes the given video ID.
     *
     * @param string $videoId The globally unique video identifier.
     *
     * @return boolean True if this provider recognizes the given video ID, false otherwise.
     */
    public function recognizesVideoId($videoId)
    {
        return $this->_delegate->singleElementRecognizesId($videoId);
    }

    /**
     * @return array An array of the valid option values for the "mode" option.
     */
    public function getGallerySourceNames()
    {
        return $this->_delegate->getGallerySourceNames();
    }

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public function getName()
    {
        return $this->_delegate->getName();
    }

    /**
     * @return string The human-readable name of this video provider.
     */
    public function getFriendlyName()
    {
        return $this->_delegate->getFriendlyName();
    }

    /**
     * @return array An array of meta names
     */
    public function getAdditionalMetaNames()
    {
        return $this->_delegate->getAdditionalMetaNames();
    }

    private function _feedToVideoArray($feed)
    {
        $toReturn       = array();
        $total          = $this->_delegate->feedCountElements($feed);
        $isDebugEnabled = $this->_logger->isEnabled();

        if ($isDebugEnabled) {

            $this->_logger->debug(sprintf('Now attempting to build %d video(s) from raw feed', $total));
        }

        for ($index = 0; $index < $total; $index++) {

            if (! $this->_delegate->feedCanWorkWithVideoAtIndex($index)) {

                if ($isDebugEnabled) {

                    $this->_logger->debug(sprintf('Skipping video at index %d', $index));
                }

                continue;
            }

            /*
             * Let's build a video!
             */
            $video = new tubepress_core_api_video_Video();

            /*
             * Every video needs to have a provider.
             */
            $video->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_PROVIDER_NAME, $this->getName());

            /*
             * Let add-ons build the rest of the video.
             */
            $event = $this->_eventDispatcher->newEventInstance($video);
            $event->setArgument('zeroBasedFeedIndex', $index);
            $event->setArgument('rawFeed', $feed);

            /*
             * Let subclasses add to the event.
             */
            $this->_delegate->singleElementOnBeforeConstruction($event);

            $video = $this->_fireEventAndGetSubject(

                tubepress_core_api_const_event_EventNames::VIDEO_CONSTRUCTION,
                $event
            );

            array_push($toReturn, $video);
        }

        $this->_delegate->feedOnAnalysisComplete($feed);

        if ($isDebugEnabled) {

            $this->_logger->debug(sprintf('Built %d video(s) from raw feed', sizeof($toReturn)));
        }

        return $toReturn;
    }

    private function _fetchFeedAndPrepareForAnalysis($url)
    {
        $httpResponse = $this->_httpClient->get($url);
        $rawFeed      = $httpResponse->getBody()->toString();

        $this->_delegate->freePrepareForAnalysis($rawFeed);

        return $rawFeed;
    }

    private function _fireEventAndGetSubject($eventName, tubepress_core_api_event_EventInterface $event)
    {
        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }
}
