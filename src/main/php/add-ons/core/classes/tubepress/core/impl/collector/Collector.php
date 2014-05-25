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
 * Simple video collector.
 */
class tubepress_core_impl_collector_Collector implements tubepress_core_api_collector_CollectorInterface
{
    /**
     * @var tubepress_api_log_LoggerInterface Logger.
     */
    private $_logger;

    /**
     * @var boolean Is debug enabled?
     */
    private $_isDebugEnabled;

    /**
     * @var tubepress_core_api_provider_VideoProviderInterface[]
     */
    private $_videoProviders = array();

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_api_http_RequestParametersInterface
     */
    private $_requestParams;

    public function __construct(tubepress_api_log_LoggerInterface                  $logger,
                                tubepress_core_api_options_ContextInterface        $context,
                                tubepress_core_api_event_EventDispatcherInterface  $eventDispatcher,
                                tubepress_core_api_http_RequestParametersInterface $requestParams)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_logger          = $logger;
        $this->_isDebugEnabled  = $logger->isEnabled();
        $this->_requestParams   = $requestParams;
    }

    /**
     * Collects a video gallery page.
     *
     * @return tubepress_core_api_video_VideoGalleryPage The video gallery page, never null.
     *
     * @api
     * @since 4.0.0
     */
    public final function collectPage()
    {
        $videoSource           = $this->_context->get(tubepress_core_api_const_options_Names::GALLERY_SOURCE);
        $result                = null;
        $providerName          = null;
        $currentPage           = $this->_getCurrentPage();

        if ($this->_isDebugEnabled) {

            $this->_logger->debug('There are ' . count($this->_videoProviders) . ' pluggable video provider service(s) registered');

            $this->_logger->debug('Asking to see who wants to handle page ' . $currentPage . ' for gallery source "' . $videoSource . '"');
        }

        foreach ($this->_videoProviders as $videoProvider) {

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

            $result = new tubepress_core_api_video_VideoGalleryPage();
        }

        $event = $this->_eventDispatcher->newEventInstance($result);

        $this->_eventDispatcher->dispatch(

            tubepress_core_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            $event
        );

        return $event->getSubject();
    }

    /**
     * Fetch a single video.
     *
     * @param string $id The video ID to fetch.
     *
     * @return tubepress_core_api_video_Video The video, or null not found.
     *
     * @api
     * @since 4.0.0
     */
    public final function collectSingle($id)
    {
        if ($this->_isDebugEnabled) {

            $this->_logger->debug(sprintf('Fetching video with ID <code>%s</code>', $id));
        }

        foreach ($this->_videoProviders as $videoProvider) {

            if ($videoProvider->recognizesVideoId($id)) {

                if ($this->_isDebugEnabled) {

                    $this->_logger->debug($videoProvider->getName() . ' recognizes video ID ' . $id);
                }

                return $videoProvider->fetchSingleVideo($id);
            }

            if ($this->_isDebugEnabled) {

                $this->_logger->debug($videoProvider->getName() . ' does not recognize video ID ' . $id);
            }
        }

        return null;
    }

    public function setPluggableVideoProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    private function _getCurrentPage()
    {
        $page = $this->_requestParams->getParamValueAsInt(tubepress_core_api_const_http_ParamName::PAGE, 1);

        if ($this->_isDebugEnabled) {

            $this->_logger->debug(sprintf('Current page number is %d', $page));
        }

        return $page;
    }
}
