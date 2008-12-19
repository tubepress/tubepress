<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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
abstract class AbstractTubePressGallery
{
    private $_cache;
    private $_feedInspectionService;
    private $_feedRetrievalService;
    private $_galleryTemplate;
    private $_messageService;
    private $_optionsManager;
    private $_paginationService;
    private $_urlBuilder;
    private $_thumbnailService;
    private $_thumbnailTemplate;
    private $_videoFactory;
    
    /**
     * Generates the content of this gallery
     * 
     * @return The HTML content for this gallery
     */
    public final function generateThumbs()
    {
        /* load up the gallery template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile($this->_galleryTemplate, true, true)) {
            throw new Exception("Couldn't load gallery template");
        }

        $xml = $this->_getFeed();
        
        $totalResults = $this->_feedInspectionService->getTotalResultCount($xml);
        $thisResult   = $this->_feedInspectionService->getQueryResultCount($xml);

        /* see if we got any */
        if ($totalResults == 0) {
            throw new Exception("YouTube returned no videos for your query!");
        }
        
        /* Figure out how many videos we're going to show */
        $vidLimit =
            $this->_tpom->get(TubePressDisplayOptions::RESULTS_PER_PAGE);
        if ($thisResult < $vidLimit) {
            $vidLimit = $thisResult;
        }   
        
        $videos = $this->_videoFactory->dom2TubePressVideoArray($xml, $vidLimit);
        
    	if ($this->_tpom->get(TubePressDisplayOptions::ORDER_BY) == "random") {
            $videos = shuffle($videos);
        }
        
        $thumbsHtml = "";
        $playerName =
        	$this->_tpom->
            	get(TubePressDisplayOptions::CURRENT_PLAYER_NAME);
        $player     = TubePressPlayer::getInstance($playerName);
        
        for ($x = 0; $x < sizeof($videos); $x++) {
        	
            /* Top of the gallery is special */
	        if ($x == 0) {
	            $tpl->setVariable("PRE_GALLERY_PLAYER_HTML", $player->getPreGalleryHtml($videos[$x], $this->_tpom));
	        }
	            
	        /* Here's where each thumbnail gets printed */
	        $thumbsHtml .= $this->_thumbnailService->getHtml($this->_thumbnailTemplate, $videos[$x], $player);     
	    }
	    
	    $tpl->setVariable("THUMBS", $thumbsHtml);
	        
	    /* Spit out the top/bottom pagination if we have any videos */
	    if ($vidLimit > 0) {
	           $this->_parsePaginationHTML($totalResults, $tpl);
	    }
        
        return $tpl->get();
    }
    
    /**
     * Handles the parsing of pagination links ("next" and "prev")
     * 
     * @param int                     $vidCount The grand total video count
     * @param HTML_Template_IT        $tpl      The HTML template to write to
     * 
     * @return void
     */
    private function _parsePaginationHTML($vidCount, 
        HTML_Template_IT &$tpl)
    {
         $pagination = $this->_paginationService->getHtml($vidCount);
        $tpl->setVariable('TOPPAGINATION', $pagination);
        $tpl->setVariable('BOTPAGINATION', $pagination);
    }
    
    private function _getFeed()
    {
        /* get the video feed */
        $url = $this->_urlBuilder->buildGalleryUrl($this->_tpom);
        $xml = "";
        if ($this->_tpom->get(TubePressAdvancedOptions::CACHE_ENABLED)) {
            if ($this->_cache->has($url)) {
                $xml = $this->_cache->get($url);
            } else {
                $xml = $this->_feedRetrievalService->fetch($url);
                $this->_cache->save($url, $xml);
            }
        } else {
            $xml = $this->_feedRetrievalService->fetch($url);
        }
        return $xml;
    }
//    
//    protected function setCommonInterfaces($tpom, TubePressMessageService $messageService)
//    {
//        $thumbService = new SimpleTubePressThumbnailService();
//        $thumbService->setOptionsManager($tpom);
//        $thumbService->setMessageService($messageService);
//        
//        $queryStringService = new SimpleTubePressQueryStringService();
//        
//        $urlBuilderService = new SimpleTubePressUrlBuilder();
//        $urlBuilderService->setOptionsManager($tpom);
//        $urlBuilderService->setQueryStringService($queryStringService);
//        
//        $paginationService = new TubePressPaginationService_DiggStyle();
//        $paginationService->setMessageService($messageService);
//        $paginationService->setOptionsManager($tpom);
//        $paginationService->setQueryStringService($queryStringService);
//        
//        $this->setCacheService(             new SimpleTubePressCacheService());
//        $this->setFeedInspectionService( new SimpleTubePressFeedInspectionService());
//        $this->setFeedRetrievalService(     new TubePressFeedRetrievalService_HTTP_Request2());
//        $this->setOptionsManager(         $tpom);
//        $this->setPaginationService(     $paginationService);
//        $this->setThumbnailService(         $thumbService);
//        $this->setUrlBuilderService(     $urlBuilderService);
//        $this->setVideoFactory(             new SimpleTubePressVideoFactory());
//    }
    
    public function setCacheService(TubePressCacheService $cache) 
    {                           
    	$this->_cache = $cache; 
    }
    
    public function setGalleryTemplate($templateFile) 
    {										  
    	$this->_galleryTemplate		= $templateFile; 
    }
    
    public function setFeedInspectionService(TubePressFeedInspectionService $feedInspector) 
    { 
    	$this->_feedInspectionService = $feedInspector; 
    }
    
    public function setFeedRetrievalService(TubePressFeedRetrievalService $feedRetriever) 
    {   
    	$this->_feedRetrievalService  = $feedRetriever; 
    }
    
    public function setMessageService(TubePressMessageService $messageService) 
    {              
    	$this->_messageService        = $messageService; 
    }
    
    public function setOptionsManager(TubePressOptionsManager $tpom) 
    {                        
    	$this->_tpom                  = $tpom; 
    }
    
    public function setPaginationService(TubePressPaginationService $paginator) 
    {             
    	$this->_paginationService     = $paginator; 
    }
    
    public function setThumbnailService(TubePressThumbnailService $thumbService) 
    {            
    	$this->_thumbnailService      = $thumbService; 
    }
    
    public function setThumbnailTemplate($templateFile) 
    {									  
    	$this->_thumbnailTemplate		= $templateFile; 
    }
    
    public function setUrlBuilderService(TubePressUrlBuilder $urlBuilder) 
    {                   
    	$this->_urlBuilder            = $urlBuilder; 
    }
    
    public function setVideoFactory(TubePressVideoFactory $factory) 
    {
    	$this->_videoFactory          = $factory; 
    }
	
}