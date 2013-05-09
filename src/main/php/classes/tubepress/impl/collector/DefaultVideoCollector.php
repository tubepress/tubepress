<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Simple video collector.
 */
class tubepress_impl_collector_DefaultVideoCollector implements tubepress_spi_collector_VideoCollector
{
    /**
     * @var ehough_epilog_Logger Logger.
     */
    private $_logger;

    /**
     * @var boolean Is debug enabled?
     */
    private $_isDebugEnabled;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Video Collector');
    }

    /**
     * Collects a video gallery page.
     *
     * @return tubepress_api_video_VideoGalleryPage The video gallery page, never null.
     */
    public final function collectVideoGalleryPage()
    {
        $this->_isDebugEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);
        $executionContext      = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $providers             = tubepress_impl_patterns_sl_ServiceLocator::getVideoProviders();
        $videoSource           = $executionContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);
        $result                = null;
        $providerName          = null;
        $currentPage           = $this->_getCurrentPage();

        if ($this->_isDebugEnabled) {

            $this->_logger->debug('There are ' . count($providers) . ' pluggable video provider service(s) registered');

            $this->_logger->debug('Asking to see who wants to handle page ' . $currentPage . ' for gallery source "' . $videoSource . '"');
        }

        /**
         * @var $videoProvider tubepress_spi_provider_PluggableVideoProviderService
         */
        foreach ($providers as $videoProvider) {

            $sources = $videoProvider->getGallerySourceNames();

            if (in_array($videoSource, $sources)) {

                if ($this->_isDebugEnabled) {

                    $this->_logger->debug($videoProvider->getName() . ' chosen to handle page ' . $currentPage
                        . ' for gallery source "' . $videoSource . '"');
                }

                $result = $videoProvider->fetchVideoGalleryPage($currentPage);

                break;
            }

            if ($this->_isDebugEnabled) {

                $this->_logger->debug($videoProvider->getName() . ' cannot handle ' . $currentPage
                    . ' for gallery source "' . $videoSource . '"');
            }
        }

        if ($result === null) {

            if ($this->_isDebugEnabled) {

                $this->_logger->debug('No video providers could handle this request');
            }

            $result = new tubepress_api_video_VideoGalleryPage();
        }

        $event = new tubepress_spi_event_EventBase($result);

        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $eventDispatcher->publish(

            tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            $event
        );

        return $event->getSubject();
    }

    /**
     * Fetch a single video.
     *
     * @param string $customVideoId The video ID to fetch.
     *
     * @return tubepress_api_video_Video The video, or null if there's a problem.
     */
    public final function collectSingleVideo($customVideoId)
    {
        $this->_isDebugEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($this->_isDebugEnabled) {

            $this->_logger->debug(sprintf('Fetching video with ID <code>%s</code>', $customVideoId));
        }

        $providers = tubepress_impl_patterns_sl_ServiceLocator::getVideoProviders();

        /**
         * @var $videoProvider tubepress_spi_provider_PluggableVideoProviderService
         */
        foreach ($providers as $videoProvider) {

            if ($videoProvider->recognizesVideoId($customVideoId)) {

                if ($this->_isDebugEnabled) {

                    $this->_logger->debug($videoProvider->getName() . ' recognizes video ID ' . $customVideoId);
                }

                return $videoProvider->fetchSingleVideo($customVideoId);
            }

            if ($this->_isDebugEnabled) {

                $this->_logger->debug($videoProvider->getName() . ' does not recognize video ID ' . $customVideoId);
            }
        }

        return null;
    }

    private function _getCurrentPage()
    {
        $qss = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();

        $page = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);

        if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            $this->_logger->debug(sprintf('Current page number is %d', $page));
        }

        return $page;
    }
}
