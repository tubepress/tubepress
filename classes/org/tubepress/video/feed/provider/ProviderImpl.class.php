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
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_video_feed_provider_Provider',
    'org_tubepress_querystring_QueryStringService',
    'org_tubepress_log_Log',
    'org_tubepress_url_UrlBuilder',
    'org_tubepress_video_feed_inspection_FeedInspectionService',
    'org_tubepress_video_feed_retrieval_FeedRetrievalService',
    'org_tubepress_options_category_Feed',
    'org_tubepress_video_factory_VideoFactory'));

/**
 * Somewhat generic class that pulls vidoes from a remote provider
 * @author ehough
 */
class org_tubepress_video_feed_provider_ProviderImpl implements org_tubepress_video_feed_provider_Provider {

    private $_feedInspectionService;
    private $_feedRetrievalService;
    private $_log;
    private $_logPrefix;
    private $_optionsManager;
    private $_queryStringService;
    private $_urlBuilder;
    private $_videoFactory;
    
    public function __construct()
    {
        $this->_logPrefix = "Video Provider";
    }
    
    public function getFeedResult()
    {
        /* figure out which page we're on */
        $currentPage = $this->_queryStringService->getPageNum($_GET);
        $this->_log->log($this->_logPrefix, 'Current page number is %d', $currentPage);
        
        /* build the request URL */
        $url = $this->_urlBuilder->buildGalleryUrl($currentPage);
        $this->_log->log($this->_logPrefix, 'URL to fetch is %s', $url);
        
        /* make the request */
        $useCache = $this->_optionsManager->get(org_tubepress_options_category_Feed::CACHE_ENABLED);
        $xml      = $this->_feedRetrievalService->fetch($url, $useCache);
        
        /* get reported total result count */
        $reportedTotalResultCount = $this->_feedInspectionService->getTotalResultCount($xml);
        $this->_log->log($this->_logPrefix, 'Reported total result count is %d videos', $reportedTotalResultCount);
        
        /* count the results in this particular response */
        $queryResult = $this->_feedInspectionService->getQueryResultCount($xml);
        $this->_log->log($this->_logPrefix, 'Query result count is %d videos', $queryResult);
        
        /* no videos? bail. */
        if ($queryResult == 0) {
            throw new Exception("No videos to populate this TubePress gallery.");
        }
        
        /* limit the result count if requested */
        $effectiveTotalResultCount = $this->_capTotalResultsIfNeeded($reportedTotalResultCount);
        $this->_log->log($this->_logPrefix, 'Effective total result count (taking into account user-defined limit) is %d videos', $effectiveTotalResultCount);
        
        /* find out how many videos per page the user wants to show */
        $perPageLimit = $this->_optionsManager->get(org_tubepress_options_category_Display::RESULTS_PER_PAGE);
        $this->_log->log($this->_logPrefix, 'Results-per-page limit is %d', $perPageLimit);
        
        /* find out how many videos this gallery will actually show (could be less than user limit) */
        $effectiveDisplayCount = min($queryResult, $perPageLimit);
        $this->_log->log($this->_logPrefix, 'Effective display count (taking into account user-defined limit) is %d videos', $effectiveDisplayCount);
        
        /* convert the XML to objects */
        $videos = $this->_videoFactory->feedToVideoArray($xml, $effectiveDisplayCount);
        
        /* shuffle if we need to */
        if ($this->_optionsManager->get(org_tubepress_options_category_Display::ORDER_BY) == 'random') {
            $this->_log->log($this->_logPrefix, 'Shuffling videos');
            shuffle($videos);
        }
        
        $result = new org_tubepress_video_feed_FeedResult();
        $result->setEffectiveDisplayCount($effectiveDisplayCount);
        $result->setEffectiveTotalResultCount($effectiveTotalResultCount);
        $result->setVideoArray($videos);
        return $result;
    }
    
    public function getSingleVideo($customVideoId)
    {
        $videoUrl = $this->_urlBuilder->buildSingleVideoUrl($customVideoId);
        $results = $this->_feedRetrievalService->fetch($videoUrl, $this->_optionsManager->get(org_tubepress_options_category_Feed::CACHE_ENABLED));
        $videoArray = $this->_videoFactory->convertSingleVideo($results, 1);
        return $videoArray[0];
    }
    
    public function setLog(org_tubepress_log_Log $log) { $this->_log = $log; }
    public function setQueryStringService(org_tubepress_querystring_QueryStringService $qss) { $this->_queryStringService = $qss; }
    public function setUrlBuilder(org_tubepress_url_UrlBuilder $urlBuilder) { $this->_urlBuilder = $urlBuilder; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom) { $this->_optionsManager = $tpom; }
    public function setFeedInspectionService(org_tubepress_video_feed_inspection_FeedInspectionService $feedInspector) { $this->_feedInspectionService = $feedInspector; }
    public function setFeedRetrievalService(org_tubepress_video_feed_retrieval_FeedRetrievalService $feedRetriever) { $this->_feedRetrievalService = $feedRetriever; }
    public function setVideoFactory(org_tubepress_video_factory_VideoFactory $factory) { $this->_videoFactory = $factory; }
    
    private function _capTotalResultsIfNeeded($totalResults)
    {
        $limit = $this->_optionsManager-> get(org_tubepress_options_category_Feed::RESULT_COUNT_CAP);
        return $limit == 0 ? $totalResults : min($limit, $totalResults);
    }
    
}
