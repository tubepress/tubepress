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
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_ioc_ContainerAware',
    'org_tubepress_options_category_Display',
    'org_tubepress_player_Player',
    'org_tubepress_util_StringUtils',
    'org_tubepress_message_MessageService',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_options_reference_OptionsReference',
    'org_tubepress_pagination_PaginationService',
    'org_tubepress_template_Template',
    'org_tubepress_gallery_TubePressGallery',
    'org_tubepress_options_category_Template',
    'org_tubepress_video_feed_provider_Provider',
    'org_tubepress_video_feed_FeedResult',
    'org_tubepress_browser_BrowserDetector'));

/**
 * TubePress gallery
 */
class org_tubepress_gallery_TubePressGalleryImpl implements org_tubepress_gallery_TubePressGallery, org_tubepress_ioc_ContainerAware
{
    private $_templateDir;
    
    private $_iocContainer;
    private $_browserDetector;
    private $_template;
    private $_log;
    private $_logPrefix;
    private $_messageService;
    private $_optionsManager;
    private $_optionsReference;
    private $_paginationService;
    private $_queryStringService;
    private $_thumbnailTemplate;    
    private $_videoProvider;
    
    public function __construct()
    {
        /* SET THE TEMPLATE DIRECTORY HERE. DON'T FORGET THE TRAILING SLASH ;)  */
        $this->_templateDir = dirname(__FILE__) . '/../../../../ui/gallery/html_templates/';
        
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
        /* first grab the videos */
        $this->_log->log($this->_logPrefix, 'Asking provider for videos');
        $feedResult = $this->_videoProvider->getFeedResult();
        
        /* prep template */
        $this->_applyCustomTemplateIfNeeded();
        $this->_template->reset();
        $this->_prepTemplate($feedResult, $galleryId);
        
        /* we're done. tie up */
        $this->_log->log($this->_logPrefix, 'Done assembling gallery %d', $galleryId);
        return $this->_template->toString();
    }
    
    private function _applyCustomTemplateIfNeeded()
    {
        $browser = $this->_browserDetector->detectBrowser($_SERVER);
        if ($browser === org_tubepress_browser_BrowserDetector::IPHONE || $browser === org_tubepress_browser_BrowserDetector::IPOD) {
            $template = realpath($this->_templateDir . 'iphone-ipod.tpl.php');
            $this->_log->log($this->_logPrefix, 'iPhone/iPod detected. Setting template to ', $template);
            $this->_template->setPath($template);
        }
        
        $customTemplate = $this->_optionsManager->get(org_tubepress_options_category_Template::TEMPLATE);
            
        if ($customTemplate != '') {
            $template = realpath($this->_templateDir . $customTemplate);
            $this->_log->log($this->_logPrefix, 'Using custom template at %s', $template);
            $this->_template->setPath($template);
        }
    }
    
    private function _prepTemplate(org_tubepress_video_feed_FeedResult $feedResult, $galleryId)
    {
        /* build the player */
        $playerName = $this->_optionsManager->get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);
        $player     = $this->_iocContainer->safeGet($playerName . "-player", org_tubepress_player_Player::NORMAL . "-player");
        $this->_log->log($this->_logPrefix, 'This gallery will use %s as the player', get_class($player));
        
        $videos = $feedResult->getVideoArray();
        
        if (is_array($videos) && sizeof($videos) > 0) {
        	$preGalleryVideo = $this->_getPreGalleryVideo($videos[0]);
        	if (!is_null($preGalleryVideo)) {
        	    $this->_template->setVariable(org_tubepress_template_Template::PRE_GALLERY, $player->getPreGalleryHtml($preGalleryVideo, $galleryId));
        	}
        	$this->_template->setVariable(org_tubepress_template_Template::VIDEO_ARRAY, $videos);
            $this->_parsePaginationHTML($feedResult->getEffectiveTotalResultCount());
        }
        
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_IMPL_NAME,  $this->_determineEmbeddedImplName());
        $this->_template->setVariable(org_tubepress_template_Template::GALLERY_ID,          $galleryId);
        $this->_template->setVariable(org_tubepress_template_Template::PLAYER_NAME,         $playerName);
        $this->_template->setVariable(org_tubepress_template_Template::THUMBNAIL_WIDTH,     $this->_optionsManager->get(org_tubepress_options_category_Display::THUMB_WIDTH));
        $this->_template->setVariable(org_tubepress_template_Template::THUMBNAIL_HEIGHT,    $this->_optionsManager->get(org_tubepress_options_category_Display::THUMB_HEIGHT));
        
        $this->_prepTemplateMetaElements();
        $this->_prepUrlPrefixes();
        
        /* Ajax pagination? */
        if ($this->_optionsManager->get(org_tubepress_options_category_Display::AJAX_PAGINATION)) {
            $this->_log->log($this->_logPrefix, 'Using Ajax pagination');
            $this->_template->setVariable(org_tubepress_template_Template::SHORTCODE, urlencode($this->_optionsManager->getShortcode()));        
        }
    }
    
    private function _prepUrlPrefixes()
    {
        $provider = $this->_optionsManager->calculateCurrentVideoProvider();
        if ($provider === org_tubepress_video_feed_provider_Provider::YOUTUBE) {
            $this->_template->setVariable(org_tubepress_template_Template::AUTHOR_URL_PREFIX, 'http://www.youtube.com/profile?user=');
            $this->_template->setVariable(org_tubepress_template_Template::VIDEO_SEARCH_PREFIX, 'http://www.youtube.com/results?search_query=');            
        } else {
            $this->_template->setVariable(org_tubepress_template_Template::AUTHOR_URL_PREFIX, 'http://vimeo.com/');
            $this->_template->setVariable(org_tubepress_template_Template::VIDEO_SEARCH_PREFIX, 'http://vimeo.com/videos/search:'); 
        }        
    }
    
    private function _determineEmbeddedImplName()
    {
        $stored = $this->_optionsManager->get(org_tubepress_options_category_Embedded::PLAYER_IMPL);
        if ($stored === org_tubepress_embedded_EmbeddedPlayerService::LONGTAIL) {
            return $stored;
        }
        return $this->_optionsManager->calculateCurrentVideoProvider();
    }
    
    private function _prepTemplateMetaElements()
    {
        $metaNames = $this->_optionsReference->getOptionNamesForCategory(org_tubepress_options_Category::META);
        $shouldShow = array();
        $labels = array();
        foreach ($metaNames as $metaName) {
            $shouldShow[$metaName] = $this->_optionsManager->get($metaName);
            $labels[$metaName] = $this->_messageService->_('video-' . $metaName);            
        }
        $this->_template->setVariable(org_tubepress_template_Template::META_SHOULD_SHOW, $shouldShow);
        $this->_template->setVariable(org_tubepress_template_Template::META_LABELS, $labels);
    }
    
    private function _getPreGalleryVideo($video)
    {
        $customVideoId = $this->_queryStringService->getCustomVideo($_GET);
        if ($customVideoId != "") {
            return $this->_videoProvider->getSingleVideo($customVideoId);
        }
        return $video;
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
            $this->_template->setVariable(org_tubepress_template_Template::PAGINATION_TOP, $pagination);
        }
        if ($this->_optionsManager->get(org_tubepress_options_category_Display::PAGINATE_BELOW)) {
            $this->_template->setVariable(org_tubepress_template_Template::PAGINATION_BOTTOM, $pagination);
        }
    }
    
    public static function printHeadElements($include_jQuery = false, $getVars)
    {
        global $tubepress_base_url;

        $jqueryInclude = '';
        if ($include_jQuery) {
            $jqueryInclude = "<script type=\"text/javascript\" src=\"$tubepress_base_url/ui/lib/jquery-1.3.2.min.js\"></script>";
        }
        
        $result = <<<GBS
    $jqueryInclude
    <script type="text/javascript">function getTubePressBaseUrl(){return "$tubepress_base_url";}</script>
    <script type="text/javascript" src="$tubepress_base_url/ui/lib/tubepress.js"></script>
    <link rel="stylesheet" href="$tubepress_base_url/ui/gallery/css/tubepress.css" type="text/css" />
GBS;
    
        if (isset($getVars['tubepress_page']) && $getVars['tubepress_page'] > 1) {
            $result .= '<meta name="robots" content="noindex, nofollow" />
    ';
        }
        return $result;
    }
    
    public function setBrowserDetector(org_tubepress_browser_BrowserDetector $bd) {               $this->_browserDetector    = $bd; }
    public function setContainer(org_tubepress_ioc_IocService $container) {                       $this->_iocContainer       = $container; }
    public function setTemplate(org_tubepress_template_Template $template) {                      $this->_template           =   $template; }
    public function setMessageService(org_tubepress_message_MessageService $messageService) {     $this->_messageService     = $messageService; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom) {       $this->_optionsManager     = $tpom; }
    public function setOptionsReference(org_tubepress_options_reference_OptionsReference $ref) {  $this->_optionsReference   = $ref; }
    public function setLog(org_tubepress_log_Log $log) {                                          $this->_log                = $log; }
    public function setPaginationService(org_tubepress_pagination_PaginationService $paginator) { $this->_paginationService  = $paginator; }
    public function setQueryStringService(org_tubepress_querystring_QueryStringService $qss) {    $this->_queryStringService = $qss; }
    public function setVideoProvider(org_tubepress_video_feed_provider_Provider $provider) {      $this->_videoProvider      = $provider; }
}
