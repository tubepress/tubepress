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

/**
 * Parent class of all TubePress galleries
 */
abstract class org_tubepress_gallery_AbstractGallery implements org_tubepress_ioc_ContainerAware
{
    private $_feedInspectionService;
    private $_feedRetrievalService;
    private $_iocContainer;
    private $_templateDirectory;
    private $_messageService;
    private $_optionsManager;
    private $_paginationService;
    private $_queryStringService;
    private $_thumbnailService;
    private $_thumbnailTemplate;    
    private $_urlBuilder;
    private $_videoFactory;
    
    /**
     * Generates the content of this gallery
     * 
     * @return The HTML content for this gallery
     */
    public final function generateThumbs()
    {
        /* load up the gallery template */
        $tpl = new net_php_pear_HTML_Template_IT($this->_templateDirectory);
        if (!$tpl->loadTemplatefile("gallery.tpl.html", true, true)) {
            throw new Exception("Couldn't load gallery template");
        }

        $currentPage  = $this->_queryStringService->getPageNum($_GET);
        $url          = $this->_urlBuilder->buildGalleryUrl($currentPage);
        $useCache     = $this->_optionsManager->get(
            org_tubepress_options_category_Feed::CACHE_ENABLED);
        $xml          = $this->_feedRetrievalService->fetch($url, $useCache);
        $totalResults = $this->_feedInspectionService->getTotalResultCount($xml);
        $queryResult  = $this->_feedInspectionService->getQueryResultCount($xml);

        /* see if we got any */
        if ($totalResults == 0) {
            throw new Exception("YouTube returned no videos for your query!");
        }
        
        /* Figure out how many videos we're going to show */
        $vidLimit =
            $this->_optionsManager->get(org_tubepress_options_category_Display::RESULTS_PER_PAGE);
        if ($queryResult < $vidLimit) {
            $vidLimit = $queryResult;
        }   
        
        $videos = $this->_videoFactory->dom2TubePressVideoArray($xml, $vidLimit);
        
        if ($this->_optionsManager->get(org_tubepress_options_category_Display::ORDER_BY) == "random") {
            shuffle($videos);
        }
        
        $thumbsHtml = "";
        $playerName =
            $this->_optionsManager->
                get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);
        $player     = $this->_iocContainer->safeGet($playerName . "-player", org_tubepress_player_Player::NORMAL . "-player");
        
        $galleryId = mt_rand();
        $tpl->setVariable('GALLERYID', $galleryId);
        
        for ($x = 0; $x < sizeof($videos); $x++) {
            
            /* Top of the gallery is special */
            if ($x == 0) {
                $tpl->setVariable("PRE_GALLERY_PLAYER_HTML", 
                    $player->getPreGalleryHtml($this->_getPreGalleryVideo($videos), $galleryId));
            }
                
            /* Here's where each thumbnail gets printed */
            $thumbsHtml .= $this->_thumbnailService->getHtml(
                $this->_templateDirectory, $videos[$x], $galleryId);     
        }
        
        $tpl->setVariable("THUMBS", $thumbsHtml);
            
        /* Spit out the top/bottom pagination if we have any videos */
        if ($vidLimit > 0) {
               $this->_parsePaginationHTML($totalResults, $tpl);
        }
        
        return $tpl->get();
    }
    
    private function _getPreGalleryVideo($videos)
    {
        $customVideoId = $this->_queryStringService->getCustomVideo($_GET);
        if ($customVideoId != "") {
            $videoUrl = $this->_urlBuilder->buildSingleVideoUrl($customVideoId);
            $results = $this->_feedRetrievalService->fetch($videoUrl,
                $this->_optionsManager->get(org_tubepress_options_category_Feed::CACHE_ENABLED));
            $videoArray = $this->_videoFactory->dom2TubePressVideoArray($results, 1);
            return $videoArray[0];
        }
        return $videos[0];
    }
    
    /**
     * Handles the parsing of pagination links ("next" and "prev")
     * 
     * @param int                     $vidCount The grand total video count
     * @param net_php_pear_HTML_Template_IT        $tpl      The HTML template to write to
     * 
     * @return void
     */
    private function _parsePaginationHTML($vidCount, 
        net_php_pear_HTML_Template_IT $tpl)
    {
         $pagination = $this->_paginationService->getHtml($vidCount);
        $tpl->setVariable('TOPPAGINATION', $pagination);
        $tpl->setVariable('BOTPAGINATION', $pagination);
    }
    
    public function setContainer(org_tubepress_ioc_IocService $container)
    {
        $this->_iocContainer = $container;
    }

    public function setTemplateDirectory($directory) 
    {                                          
        $this->_templateDirectory = $directory; 
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
