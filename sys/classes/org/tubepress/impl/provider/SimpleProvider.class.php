<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_api_const_options_names_Feed',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_factory_VideoFactory',
    'org_tubepress_api_feed_FeedFetcher',
    'org_tubepress_api_feed_FeedInspector',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_api_querystring_QueryStringService',
    'org_tubepress_api_url_UrlBuilder',
    'org_tubepress_impl_log_Log'
));

/**
 * Interface to a remove video provider
 */
class org_tubepress_impl_provider_SimpleProvider implements org_tubepress_api_provider_Provider
{
    const LOG_PREFIX = 'Video Provider';

    /**
     * Get the video feed result.
     *
     * @return org_tubepress_api_provider_ProviderResult The feed result.
     */
    public function getMultipleVideos()
    {
        $result = new org_tubepress_api_provider_ProviderResult();

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();
        $qss     = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $context = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $pc      = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pm      = $ioc->get('org_tubepress_api_plugin_PluginManager');

        /* figure out which page we're on */
        $currentPage = $qss->getPageNum($_GET);
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Current page number is %d', $currentPage);

        $provider = $pc->calculateCurrentVideoProvider();

        /* build the request URL */
        $urlBuilder = $ioc->get('org_tubepress_api_url_UrlBuilder');
        $url        = $urlBuilder->buildGalleryUrl($currentPage);
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'URL to fetch is <a href="%s">%s</a>', $url, $url);

        /* make the request */
        $feedRetrievalService = $ioc->get('org_tubepress_api_feed_FeedFetcher');
        $useCache             = $context->get(org_tubepress_api_const_options_names_Feed::CACHE_ENABLED);
        $rawFeed              = $feedRetrievalService->fetch($url, $useCache);

        /* get the count */
        $feedInspectionService = $ioc->get('org_tubepress_api_feed_FeedInspector');
        $totalCount            = $feedInspectionService->getTotalResultCount($rawFeed);

        if ($totalCount == 0) {
            throw new Exception('Zero videos found');
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Reported total result count is %d video(s)', $totalCount);

        /* convert the XML to objects */
        $factory = $ioc->get('org_tubepress_api_factory_VideoFactory');
        $videos  = $factory->feedToVideoArray($rawFeed);

        if (count($videos) == 0) {
            throw new Exception('Zero videos built from factory');
        }

        $result->setEffectiveTotalResultCount($totalCount);
        $result->setVideoArray($videos);

        return $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $result, $provider);
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
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Fetching video with ID <tt>%s</tt>', $customVideoId);

        $ioc        = org_tubepress_impl_ioc_IocContainer::getInstance();
        $urlBuilder = $ioc->get('org_tubepress_api_url_UrlBuilder');
        $videoUrl   = $urlBuilder->buildSingleVideoUrl($customVideoId);

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'URL to fetch is %s', $videoUrl);

        $feedRetrievalService = $ioc->get('org_tubepress_api_feed_FeedFetcher');
        $context              = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $results              = $feedRetrievalService->fetch($videoUrl, $context->get(org_tubepress_api_const_options_names_Feed::CACHE_ENABLED));
        $factory              = $ioc->get('org_tubepress_api_factory_VideoFactory');
        $videoArray           = $factory->feedToVideoArray($results);
        $pm                   = $ioc->get('org_tubepress_api_plugin_PluginManager');
        $pc                   = $ioc->get('org_tubepress_api_provider_ProviderCalculator');

        if (empty($videoArray)) {
            throw new Exception("Could not find video with ID $customVideoId");
        }

        $result = new org_tubepress_api_provider_ProviderResult();
        $result->setEffectiveTotalResultCount(1);
        $result->setVideoArray($videoArray);
        
        $provider = $pc->calculateProviderOfVideoId($customVideoId);
        
        $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $result, $provider);

        return $videoArray[0];
    }
}
