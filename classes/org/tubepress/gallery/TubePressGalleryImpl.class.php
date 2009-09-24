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
    'org_tubepress_options_category_Display',
    'org_tubepress_player_Player',
    'org_tubepress_util_StringUtils',
    'org_tubepress_message_MessageService',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_pagination_PaginationService',
    'org_tubepress_thumbnail_ThumbnailService',
    'org_tubepress_template_Template',
    'org_tubepress_gallery_TubePressGallery',
    'org_tubepress_options_category_Template',
    'org_tubepress_video_feed_provider_Provider',
    'org_tubepress_video_feed_FeedResult'));

/**
 * TubePress gallery
 */
class org_tubepress_gallery_TubePressGalleryImpl implements org_tubepress_gallery_TubePressGallery, org_tubepress_ioc_ContainerAware
{
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
    private $_videoProvider;
    
    public function __construct()
    {
        $this->_logPrefix = "Gallery";
    }
    
    public function getHtml($galleryId) 
    {
        try {
            return $this->_getHtml($galleryId);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * Generates the content of this gallery
     * 
     * @return The HTML content for this gallery
     */
    private final function _getHtml($galleryId)
    {
	$requestedGalleryId = $this->_optionsManager->get(org_tubepress_options_category_Advanced::GALLERY_ID);
	if (isset($requestedGalleryId)) {
		$galleryId = $requestedGalleryId;
	}

        /* first grab the videos */
        $feedResult = $this->_videoProvider->getFeedResult();
        
        /* build the player */
        $playerName = $this->_optionsManager->get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);
        $player     = $this->_iocContainer->safeGet($playerName . "-player", org_tubepress_player_Player::NORMAL . "-player");

        /* apply the custom template if we need to */
        $this->_applyCustomTemplateIfNeeded();
        
        /* generate HTML */
        $galleryHtml = $this->_loopOverThumbs($feedResult, $player, $galleryId);
        
	/* Ajax pagination? */
	if ($this->_optionsManager->get(org_tubepress_options_category_Display::AJAX_PAGINATION)) {
		$this->_template->setVariable('GALLERYID', $galleryId);
		$this->_template->setVariable('URL_ENCODED_SHORTCODE', urlencode($this->_optionsManager->getShortcode()));		
		$this->_template->parse('ajaxPagination');
	}

        /* apply vars to the template */
        $this->_template->setVariable('GALLERY_ID', $galleryId);
        $this->_template->setVariable("THUMBS", $galleryHtml);

        /* we're done. tie up */
        return $this->_template->getHtml();
    }
    
    private function _applyCustomTemplateIfNeeded()
    {
        $customTemplate = $this->_optionsManager->get(org_tubepress_options_category_Template::TEMPLATE);
            
        if ($customTemplate != "") {
            $this->_log->log($this->_logPrefix, sprintf("Using custom template at %s", $customTemplate));
            $this->_template->setFile($customTemplate);
        }
    }
    
    private function _loopOverThumbs(org_tubepress_video_feed_FeedResult $feedResult, $player, $galleryId)
    {
        $thumbsHtml = "";
        $videos = $feedResult->getVideoArray();
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
                $this->_template->setVariable("PRE_GALLERY_PLAYER_HTML", $player->getPreGalleryHtml($this->_getPreGalleryVideo($videos, $x), $galleryId));
            }
                    
            /* get the HTML for this thumbnail */
            $thumbsHtml .= $this->_thumbnailService->getHtml($videos[$x], $galleryId);
            
            $printedCount++;
        }
        
        /* Spit out the top/bottom pagination if we have any videos */
        if ($printedCount > 0) {
            $this->_parsePaginationHTML($feedResult->getEffectiveTotalResultCount());
        }
        
        return $thumbsHtml;
    }
    
    private function _getPreGalleryVideo($videos, $index)
    {
        $customVideoId = $this->_queryStringService->getCustomVideo($_GET);
        if ($customVideoId != "") {
            return $this->_videoProvider->getSingleVideo($customVideoId);
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
    
    public function setVideoProvider(org_tubepress_video_feed_provider_Provider $provider)
    {
        $this->_videoProvider = $provider;
    }

}
