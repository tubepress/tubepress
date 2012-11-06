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
 * Simple video collector.
 */
class tubepress_impl_collector_DefaultVideoCollector implements tubepress_spi_collector_VideoCollector
{
    /**
     * @var ehough_epilog_api_ILogger Logger.
     */
    private $_logger;

    /**
     * @var boolean Is debug enabled?
     */
    private $_isDebugEnabled;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Default Video Collector');
    }

    /**
     * Collects a video gallery page.
     *
     * @return tubepress_api_video_VideoGalleryPage The video gallery page, never null.
     */
    public final function collectVideoGalleryPage()
    {
        $this->_isDebugEnabled = $this->_logger->isDebugEnabled();

        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();
        $executionContext           = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        $videoSource  = $executionContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);
        $providers    = $serviceCollectionsRegistry->getAllServicesOfType(tubepress_spi_provider_PluggableVideoProviderService::_);
        $result       = null;
        $providerName = null;
        $currentPage  = $this->_getCurrentPage();

        if ($this->_isDebugEnabled) {

            $this->_logger->debug('There are ' . count($providers) . ' pluggable video provider service(s) registered');

            $this->_logger->debug('Asking to see who wants to handle page ' . $currentPage . ' for gallery source "' . $videoSource . '"');
        }

        foreach ($providers as $videoProvider) {

            /** @noinspection PhpUndefinedMethodInspection */
            $sources = $videoProvider->getGallerySourceNames();

            if (in_array($videoSource, $sources)) {

                if ($this->_isDebugEnabled) {

                    /** @noinspection PhpUndefinedMethodInspection */
                    $this->_logger->debug($videoProvider->getName() . ' chosen to handle page ' . $currentPage
                        . ' for gallery source "' . $videoSource . '"');
                }

                /** @noinspection PhpUndefinedMethodInspection */
                $result = $videoProvider->fetchVideoGalleryPage($currentPage);

                break;
            }

            if ($this->_isDebugEnabled) {

                /** @noinspection PhpUndefinedMethodInspection */
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

        $event = new tubepress_api_event_TubePressEvent($result);

        $eventDispatcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        $eventDispatcher->dispatch(

            tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
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
        $this->_isDebugEnabled = $this->_logger->isDebugEnabled();

        if ($this->_isDebugEnabled) {

            $this->_logger->debug(sprintf('Fetching video with ID <code>%s</code>', $customVideoId));
        }

        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        $providers = $serviceCollectionsRegistry->getAllServicesOfType(tubepress_spi_provider_PluggableVideoProviderService::_);

        foreach ($providers as $videoProvider) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($videoProvider->recognizesVideoId($customVideoId)) {

                if ($this->_isDebugEnabled) {

                    /** @noinspection PhpUndefinedMethodInspection */
                    $this->_logger->debug($videoProvider->getName() . ' recognizes video ID ' . $customVideoId);
                }

                /** @noinspection PhpUndefinedMethodInspection */
                return $videoProvider->fetchSingleVideo($customVideoId);
            }

            if ($this->_isDebugEnabled) {

                /** @noinspection PhpUndefinedMethodInspection */
                $this->_logger->debug($videoProvider->getName() . ' does not recognize video ID ' . $customVideoId);
            }
        }

        return null;
    }

    private function _getCurrentPage()
    {
        $qss = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();

        $page = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Current page number is %d', $page));
        }

        return $page;
    }
}
