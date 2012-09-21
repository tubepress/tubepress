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
 * Single video provider.
 */
class tubepress_impl_provider_SimpleProvider implements tubepress_spi_provider_Provider
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Simple Video Provider');
    }

    /**
     * Get the video feed result.
     *
     * @return tubepress_api_video_VideoGalleryPage The feed result.
     */
    public final function getMultipleVideos()
    {
    	$result = $this->collectMultipleVideos();

        $eventDispatcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();
        $pc              = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProviderCalculator();
        $provider        = $pc->calculateCurrentVideoProvider();

        $event = new tubepress_api_event_TubePressEvent($result);
        $event->setArgument('providerName', $provider);

        $eventDispatcher->dispatch(

            tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            $event
        );

        return $event->getSubject();
    }

    protected function collectMultipleVideos()
    {
    	$result = new tubepress_api_video_VideoGalleryPage();

    	$qss           = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
    	$context       = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
    	$pc            = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProviderCalculator();
        $urlBuilder    = tubepress_impl_patterns_ioc_KernelServiceLocator::getUrlBuilder();
        $feedFetcher   = tubepress_impl_patterns_ioc_KernelServiceLocator::getFeedFetcher();
        $feedInspector = tubepress_impl_patterns_ioc_KernelServiceLocator::getFeedInspector();
        $factory       = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoFactory();

    	/* figure out which page we're on */
    	$currentPage = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Current page number is %d', $currentPage));
        }

    	$provider = $pc->calculateCurrentVideoProvider();

    	/* build the request URL */
    	$url = $urlBuilder->buildGalleryUrl($currentPage);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('URL to fetch is <tt>%s</tt>', $url));
        }

    	/* make the request */
    	$useCache = $context->get(tubepress_api_const_options_names_Cache::CACHE_ENABLED);
    	$rawFeed  = $feedFetcher->fetch($url, $useCache);

    	/* get the count */
    	$totalCount = $feedInspector->getTotalResultCount($rawFeed);

    	if ($totalCount == 0) {

    		throw new RuntimeException('No matching videos');    //>(translatable)<
    	}

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Reported total result count is %d video(s)', $totalCount));
        }

    	/* convert the XML to objects */
    	$videos  = $factory->feedToVideoArray($rawFeed);

    	if (count($videos) == 0) {

    		throw new RuntimeException('No viewable videos');    //>(translatable)<
    	}

    	$result->setTotalResultCount($totalCount);
    	$result->setVideos($videos);

    	return $result;
    }

    /**
     * Fetch a single video.
     *
     * @param string $customVideoId The video ID to fetch.
     *
     * @return tubepress_api_video_Video The video, or null if there's a problem.
     */
    public function getSingleVideo($customVideoId)
    {
        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Fetching video with ID <tt>%s</tt>', $customVideoId));
        }

        $context         = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $pc              = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProviderCalculator();
        $urlBuilder      = tubepress_impl_patterns_ioc_KernelServiceLocator::getUrlBuilder();
        $feedFetcher     = tubepress_impl_patterns_ioc_KernelServiceLocator::getFeedFetcher();
        $factory         = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoFactory();
        $eventDispatcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        $videoUrl   = $urlBuilder->buildSingleVideoUrl($customVideoId);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('URL to fetch is <a href="%s">this</a>', $videoUrl));
        }

        $results    = $feedFetcher->fetch($videoUrl, $context->get(tubepress_api_const_options_names_Cache::CACHE_ENABLED));
        $videoArray = $factory->feedToVideoArray($results);

        if (empty($videoArray)) {

            return null;
        }

        $result = new tubepress_api_video_VideoGalleryPage();
        $result->setTotalResultCount(1);
        $result->setVideos($videoArray);

        $provider = $pc->calculateProviderOfVideoId($customVideoId);

        $event = new tubepress_api_event_TubePressEvent($result);
        $event->setArgument('providerName', $provider);

        $eventDispatcher->dispatch(

            tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            $event
        );

        $galleryPage = $event->getSubject();
        $videoArray  = $galleryPage->getVideos();

        return $videoArray[0];
    }
}
