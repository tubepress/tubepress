<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_ioc_ContainerAware',
    'org_tubepress_options_category_Feed',
    'org_tubepress_options_category_Display',
    'org_tubepress_player_Player',
    'org_tubepress_util_StringUtils',
    'org_tubepress_gdata_inspection_FeedInspectionService',
    'org_tubepress_gdata_retrieval_FeedRetrievalService',
    'org_tubepress_message_MessageService',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_pagination_PaginationService',
    'org_tubepress_querystring_QueryStringService',
    'org_tubepress_thumbnail_ThumbnailService',
    'org_tubepress_url_UrlBuilder',
    'org_tubepress_video_factory_VideoFactory',
    'org_tubepress_template_Template'));

/**
 * TubePress gallery
 */
class org_tubepress_gallery_TubePressGalleryImpl implements org_tubepress_gallery_TubePressGallery, org_tubepress_ioc_ContainerAware
{
    private $_feedInspectionService;
    private $_feedRetrievalService;
    private $_iocContainer;
    private $_template;
    private $_log;
    private $_logPrefix;
    private $_messageService;
    private $_optionsManager;
    private $_paginationService;
    private $_queryStringService;
    private $_thumbnailService;
    private $_thumbnailTemplate;    
    private $_urlBuilder;
    private $_videoFactory;
    
    public function __construct()
    {
        $this->_logPrefix = "Gallery";
    }
    
    public function getHtml($galleryId) 
    {
        try {
            $customTemplate = $this->_optionsManager->
                get(org_tubepress_gallery_TubePressGallery::TEMPLATE);
                
            if ($customTemplate != "") {
                $this->_log($this->_logPrefix, sprintf("Using custom template at %s", $customTemplate));
                $this->_template->setFile($customTemplate);
            }
            
            return $this->generateThumbs($galleryId);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * Generates the content of this gallery
     * 
     * @return The HTML content for this gallery
     */
    public final function generateThumbs($galleryId = 1)
    {
        $currentPage  = $this->_queryStringService->getPageNum($_GET);
        $this->_log->log($this->_logPrefix, sprintf("Current page number is %d", $currentPage));
        
        $url          = $this->_urlBuilder->buildGalleryUrl($currentPage);
        $this->_log->log($this->_logPrefix, sprintf("URL to fetch is %s", $url));
        
        $useCache     = $this->_optionsManager->get(
            org_tubepress_options_category_Feed::CACHE_ENABLED);
        $xml          = $this->_feedRetrievalService->fetch($url, $useCache);
        
        $reportedTotalResultCount = $this->_feedInspectionService->getTotalResultCount($xml);
        $this->_log->log($this->_logPrefix, sprintf("Reported total result count is %d videos", $reportedTotalResultCount));
        
        $queryResult  = $this->_feedInspectionService->getQueryResultCount($xml);
        $this->_log->log($this->_logPrefix, sprintf("Query result count is %d videos", $queryResult));
        
        /* see if we got any */
        if ($queryResult == 0) {
            throw new Exception("No videos to populate this TubePress gallery.");
        }
        
        /* limit the result count if requested */
        $effectiveTotalResultCount = $this->_capTotalResultsIfNeeded($reportedTotalResultCount);
        $this->_log->log($this->_logPrefix, 
            sprintf("Effective total result count (taking into account user-defined limit) is %d videos", 
                $effectiveTotalResultCount));
        
        /* Figure out how many videos we're going to show */
        $perPageLimit =
            $this->_optionsManager->get(org_tubepress_options_category_Display::RESULTS_PER_PAGE);
        $this->_log->log($this->_logPrefix, 
            sprintf("Results-per-page limit is %d", 
                $perPageLimit));
        $effectiveDisplayCount = min($queryResult, $perPageLimit);
        $this->_log->log($this->_logPrefix, 
            sprintf("Effective display count (taking into account user-defined limit) is %d videos", 
                $effectiveDisplayCount));
        
        /* convert the XML to objects */
        $videos = $this->_videoFactory->dom2TubePressVideoArray($xml, $effectiveDisplayCount);
        
        /* shuffle if we need to */
        if ($this->_optionsManager->get(org_tubepress_options_category_Display::ORDER_BY) == "random") {
            $this->_log->log($this->_logPrefix, "Shuffling videos");
            shuffle($videos);
        }
        
        $playerName =
            $this->_optionsManager->
                get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);
        $player     = $this->_iocContainer->safeGet($playerName . "-player", 
            org_tubepress_player_Player::NORMAL . "-player");
        
        $this->_template->setVariable('GALLERYID', $galleryId);
        $this->_template->setVariable("THUMBS", $this->_loopOverThumbs($videos, $player, $galleryId));
            
        /* Spit out the top/bottom pagination if we have any videos */
        if ($effectiveDisplayCount > 0) {
            $this->_parsePaginationHTML($effectiveTotalResultCount);
        }
        
        $output = $this->_template->getHtml();
        return org_tubepress_util_StringUtils::removeEmptyLines($output);
    }
    
    private function _capTotalResultsIfNeeded($totalResults)
    {
        $limit = $this->_optionsManager->
                get(org_tubepress_options_category_Feed::RESULT_COUNT_CAP);
        return $limit == 0 ? $totalResults : $limit;
    }
    
    private function _loopOverThumbs($videos, $player, $galleryId)
    {
        $thumbsHtml = "";
        $numVideos = sizeof($videos);
        $printedCount = 0;
        
        for ($x = 0; $x < $numVideos; $x++) {
            
            /* ignore videos we can't display */
            if (!$videos[$x]->isDisplayable()) {
                $this->_log->log($this->_logPrefix, sprintf("Video %d/%d can't be displayed. Skipping it.", $x + 1, $numVideos));
                continue;
            }
                
            /* Top of the gallery is special */
            if ($printedCount == 0) {
                $this->_template->setVariable("PRE_GALLERY_PLAYER_HTML", 
                    $player->getPreGalleryHtml($this->_getPreGalleryVideo($videos, $x), $galleryId));
            }
                    
            /* Here's where each thumbnail gets printed */
            $thumbsHtml .= $this->_thumbnailService->getHtml(
                $videos[$x], $galleryId);
            
            $printedCount++;
        }
        return $thumbsHtml;
    }
    
    private function _getPreGalleryVideo($videos, $index)
    {
        $customVideoId = $this->_queryStringService->getCustomVideo($_GET);
        if ($customVideoId != "") {
            $videoUrl = $this->_urlBuilder->buildSingleVideoUrl($customVideoId);
            $results = $this->_feedRetrievalService->fetch($videoUrl,
                $this->_optionsManager->get(org_tubepress_options_category_Feed::CACHE_ENABLED));
            $videoArray = $this->_videoFactory->dom2TubePressVideoArray($results, 1);
            return $videoArray[0];
        }
        return $videos[$index];
    }
    
    /**
     * Handles the parsing of pagination links ("next" and "prev")
     * 
     * @param int                     $vidCount The grand total video count
     * 
     * @return void
     */
    private function _parsePaginationHTML($vidCount)
    {
        $pagination = $this->_paginationService->getHtml($vidCount);
        
        if ($this->_optionsManager->get(org_tubepress_options_category_Display::PAGINATE_ABOVE)) {
            $this->_template->setVariable('TOPPAGINATION', $pagination);
        }
        if ($this->_optionsManager->get(org_tubepress_options_category_Display::PAGINATE_BELOW)) {
            $this->_template->setVariable('BOTPAGINATION', $pagination);
        }
    }
    
    public function setContainer(org_tubepress_ioc_IocService $container)
    {
        $this->_iocContainer = $container;
    }

    public function setTemplate(org_tubepress_template_Template $template) 
    {                                          
        $this->_template = $template; 
    }
    
    public function setFeedInspectionService(org_tubepress_gdata_inspection_FeedInspectionService $feedInspector) 
    { 
        $this->_feedInspectionService = $feedInspector; 
    }
    
    public function setFeedRetrievalService(org_tubepress_gdata_retrieval_FeedRetrievalService $feedRetriever) 
    {   
        $this->_feedRetrievalService = $feedRetriever; 
    }

    public function setMessageService(org_tubepress_message_MessageService $messageService) 
    {              
        $this->_messageService = $messageService; 
    }
    
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom) 
    {                        
        $this->_optionsManager = $tpom; 
    }
    
    public function setLog(org_tubepress_log_Log $log)
    {
        $this->_log = $log;
    }
    
    public function setPaginationService(org_tubepress_pagination_PaginationService $paginator) 
    {             
        $this->_paginationService = $paginator; 
    }

    public function setQueryStringService(org_tubepress_querystring_QueryStringService $qss) 
    {             
        $this->_queryStringService = $qss; 
    }
    
    public function setThumbnailService(org_tubepress_thumbnail_ThumbnailService $thumbService) 
    {            
        $this->_thumbnailService = $thumbService; 
    }
    
    public function setUrlBuilderService(org_tubepress_url_UrlBuilder $urlBuilder) 
    {                   
        $this->_urlBuilder = $urlBuilder; 
    }
    
    public function setVideoFactory(org_tubepress_video_factory_VideoFactory $factory) 
    {
        $this->_videoFactory = $factory; 
    }
}
