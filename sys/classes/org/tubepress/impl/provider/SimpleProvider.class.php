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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_http_ParamName',
    'org_tubepress_api_const_options_names_Feed',
	'org_tubepress_api_const_options_names_Cache',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_factory_VideoFactory',
    'org_tubepress_api_feed_FeedFetcher',
    'org_tubepress_api_feed_FeedInspector',
    'org_tubepress_api_feed_FeedInspector',
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_api_feed_UrlBuilder',
    'org_tubepress_impl_log_Log'
));

/**
 * Interface to a remove video provider
 */
class org_tubepress_impl_provider_SimpleProvider implements org_tubepress_api_provider_Provider
{
    private static $_logPrefix = 'Simple Video Provider';

    /**
     * Get the video feed result.
     *
     * @return org_tubepress_api_provider_ProviderResult The feed result.
     */
    public function getMultipleVideos()
    {
    	$ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
    	$pm  = $ioc->get(org_tubepress_api_plugin_PluginManager::_);

    	$result = $this->collectMultipleVideos();

        return $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $result);
    }

    protected function collectMultipleVideos()
    {
    	$result = new org_tubepress_api_provider_ProviderResult();

    	$ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();
    	$qss     = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
    	$context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
    	$pc      = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);

    	/* figure out which page we're on */
    	$currentPage = $qss->getParamValueAsInt(org_tubepress_api_const_http_ParamName::PAGE, 1);
    	org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Current page number is %d', $currentPage);

    	$provider = $pc->calculateCurrentVideoProvider();

    	/* build the request URL */
    	$urlBuilder = $ioc->get(org_tubepress_api_feed_UrlBuilder::_);
    	$url        = $urlBuilder->buildGalleryUrl($currentPage);

    	org_tubepress_impl_log_Log::log(self::$_logPrefix, 'URL to fetch is <tt>%s</tt>', $url);

    	/* make the request */
    	$feedRetrievalService = $ioc->get(org_tubepress_api_feed_FeedFetcher::_);
    	$useCache             = $context->get(org_tubepress_api_const_options_names_Cache::CACHE_ENABLED);
    	$rawFeed              = $feedRetrievalService->fetch($url, $useCache);

    	/* get the count */
    	$feedInspectionService = $ioc->get(org_tubepress_api_feed_FeedInspector::_);
    	$totalCount            = $feedInspectionService->getTotalResultCount($rawFeed);

    	if ($totalCount == 0) {

    		throw new Exception('No matching videos');    //>(translatable)<
    	}

    	org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Reported total result count is %d video(s)', $totalCount);

    	/* convert the XML to objects */
    	$factory = $ioc->get(org_tubepress_api_factory_VideoFactory::_);
    	$videos  = $factory->feedToVideoArray($rawFeed);

    	if (count($videos) == 0) {

    		throw new Exception('No viewable videos');    //>(translatable)<
    	}

    	$result->setEffectiveTotalResultCount($totalCount);
    	$result->setVideoArray($videos);

    	return $result;
    }

    /**
     * Fetch a single video.
     *
     * @param string $customVideoId The video ID to fetch.
     *
     * @return org_tubepress_api_video_Video The video.
     */
    public function getSingleVideo($customVideoId)
    {
        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Fetching video with ID <tt>%s</tt>', $customVideoId);

        $ioc        = org_tubepress_impl_ioc_IocContainer::getInstance();
        $urlBuilder = $ioc->get(org_tubepress_api_feed_UrlBuilder::_);
        $videoUrl   = $urlBuilder->buildSingleVideoUrl($customVideoId);

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'URL to fetch is <a href="%s">this</a>', $videoUrl);

        $feedRetrievalService = $ioc->get(org_tubepress_api_feed_FeedFetcher::_);
        $context              = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $results              = $feedRetrievalService->fetch($videoUrl, $context->get(org_tubepress_api_const_options_names_Cache::CACHE_ENABLED));
        $factory              = $ioc->get(org_tubepress_api_factory_VideoFactory::_);
        $videoArray           = $factory->feedToVideoArray($results);
        $pm                   = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $pc                   = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);

        if (empty($videoArray)) {

            throw new Exception(sprintf('Could not find video with ID %s', $customVideoId));    //>(translatable)<
        }

        $result = new org_tubepress_api_provider_ProviderResult();
        $result->setEffectiveTotalResultCount(1);
        $result->setVideoArray($videoArray);

        $provider = $pc->calculateProviderOfVideoId($customVideoId);

        $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $result, $provider);

        return $videoArray[0];
    }
}
