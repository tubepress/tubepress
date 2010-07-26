<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_video_feed_provider_Provider',
    'org_tubepress_querystring_QueryStringService',
    'org_tubepress_log_Log',
    'org_tubepress_url_UrlBuilder',
    'org_tubepress_video_feed_inspection_DelegatingFeedInspectionService',
    'org_tubepress_video_feed_retrieval_FeedRetrievalService',
    'org_tubepress_options_category_Feed',
    'org_tubepress_video_factory_DelegatingVideoFactory',
    'org_tubepress_video_feed_FeedResult',
    'org_tubepress_url_DelegatingUrlBuilder'));

/**
 * Interface to a remove video provider
 */
class org_tubepress_video_feed_provider_Provider
{
    const LOG_PREFIX = 'Video Provider';

    const DIRECTORY = 'directory';
    const YOUTUBE   = 'youtube';
    const VIMEO     = 'vimeo';

    /**
     * Get the video feed result.
     *
     * @param org_tubepress_ioc_IocService $ioc The IOC container.
     *
     * @return org_tubepress_video_feed_FeedResult The feed result.
     */
    public static function getFeedResult(org_tubepress_ioc_IocService $ioc)
    {
        /* figure out which page we're on */
        $currentPage = org_tubepress_querystring_QueryStringService::getPageNum($_GET);
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Current page number is %d', $currentPage);

        $tpom     = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        $provider = self::calculateCurrentVideoProvider($tpom);

        if ($provider === self::DIRECTORY) {
            $rawFeed = $tpom->get(org_tubepress_options_category_Gallery::DIRECTORY_VALUE);
        } else {

            /* build the request URL */
            $url = org_tubepress_url_DelegatingUrlBuilder::buildGalleryUrl($ioc, $currentPage);
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'URL to fetch is %s', $url);

            /* make the request */
            $feedRetrievalService = $ioc->get(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE);
            $useCache             = $tpom->get(org_tubepress_options_category_Feed::CACHE_ENABLED);
            $rawFeed              = $feedRetrievalService->fetch($ioc, $url, $useCache);
        }

        /* get reported total result count */
        $reportedTotalResultCount = org_tubepress_video_feed_inspection_DelegatingFeedInspectionService::getTotalResultCount($ioc, $rawFeed);
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Reported total result count is %d video(s)', $reportedTotalResultCount);

        /* count the results in this particular response */
        $queryResult = org_tubepress_video_feed_inspection_DelegatingFeedInspectionService::getQueryResultCount($ioc, $rawFeed);
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Query result count is %d video(s)', $queryResult);

        /* no videos? bail. */
        if ($queryResult == 0) {
            throw new Exception("No videos to populate this TubePress gallery.");
        }

        /* limit the result count if requested */
        $effectiveTotalResultCount = self::_capTotalResultsIfNeeded($tpom, $reportedTotalResultCount);
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Effective total result count (taking into account user-defined limit) is %d video(s)', $effectiveTotalResultCount);

        /* find out how many videos per page the user wants to show */
        $perPageLimit = $tpom->get(org_tubepress_options_category_Display::RESULTS_PER_PAGE);
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Results-per-page limit is %d', $perPageLimit);

        /* find out how many videos this gallery will actually show (could be less than user limit) */
        $effectiveDisplayCount = min($effectiveTotalResultCount, min($queryResult, $perPageLimit));
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Effective display count for this page is %d video(s)', $effectiveDisplayCount);

        /* convert the XML to objects */
        $videos = org_tubepress_video_factory_DelegatingVideoFactory::feedToVideoArray($ioc, $rawFeed, $effectiveDisplayCount);

        /* shuffle if we need to */
        if ($tpom->get(org_tubepress_options_category_Display::ORDER_BY) == 'random') {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Shuffling videos');
            shuffle($videos);
        }

        $result = new org_tubepress_video_feed_FeedResult();
        $result->setEffectiveDisplayCount($effectiveDisplayCount);
        $result->setEffectiveTotalResultCount($effectiveTotalResultCount);
        $result->setVideoArray($videos);
        return $result;
    }

    /**
     * Fetch a single video.
     *
     * @param string                       $customVideoId The video ID to fetch.
     * @param org_tubepress_ioc_IocService $ioc           The IOC container.
     *
     * @return org_tubepress_video_Video The video.
     */
    public static function getSingleVideo($customVideoId, org_tubepress_ioc_IocService $ioc)
    {
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Fetching video with ID %s', $customVideoId);

        $videoUrl = org_tubepress_url_DelegatingUrlBuilder::buildSingleVideoUrl($ioc, $customVideoId);
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'URL to fetch is %s', $videoUrl);

        $feedRetrievalService = $ioc->get(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE);
        $tpom                 = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        $results              = $feedRetrievalService->fetch($ioc, $videoUrl, $tpom->get(org_tubepress_options_category_Feed::CACHE_ENABLED));
        $videoArray           = org_tubepress_video_factory_DelegatingVideoFactory::convertSingleVideo($ioc, $results, 1);

        return $videoArray[0];
    }

    /**
     * Determine the current video provider.
     *
     * @param org_tubepress_options_manager_OptionsManager $tpom The options manager.
     *
     * @return string 'youtube', 'vimeo', or 'directory'
     */
    public static function calculateCurrentVideoProvider(org_tubepress_options_manager_OptionsManager $tpom)
    {
        $video = $tpom->get(org_tubepress_options_category_Gallery::VIDEO);

        /* vimeo video IDs are always just numbers */
        if (is_numeric($video) === true) {
            return self::VIMEO;
        }

        if (preg_match_all('/^.*\.[A-Za-z]{3}$/', $video, $arr, PREG_PATTERN_ORDER) === 1) {
            return self::DIRECTORY;
        }

        /* requested a single video, and it's not vimeo or directory, so must be youtube */
        if ($video != '') {
            return self::YOUTUBE;
        }

        /* calculate based on gallery content */
        $currentMode = $tpom->get(org_tubepress_options_category_Gallery::MODE);
        if (strpos($currentMode, 'vimeo') === 0) {
            return self::VIMEO;
        }
        if (strpos($currentMode, 'directory') === 0) {
            return self::DIRECTORY;
        }
        return self::YOUTUBE;
    }

    private static function _capTotalResultsIfNeeded(org_tubepress_options_manager_OptionsManager $tpom, $totalResults)
    {
        $limit = $tpom-> get(org_tubepress_options_category_Feed::RESULT_COUNT_CAP);
        return $limit == 0 ? $totalResults : min($limit, $totalResults);
    }

}
