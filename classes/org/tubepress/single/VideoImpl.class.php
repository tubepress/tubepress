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
tubepress_load_classes(array('org_tubepress_single_Video',
    'org_tubepress_video_feed_provider_Provider',
    'org_tubepress_options_category_Embedded',
    'org_tubepress_ioc_ContainerAware',
    'org_tubepress_embedded_EmbeddedPlayerService',
    'org_tubepress_template_Template',
    'org_tubepress_ioc_IocService',
    'org_tubepress_options_reference_OptionsReference',
    'org_tubepress_message_MessageService',
    'org_tubepress_log_Log'));

/**
 * Handles requests for a single video (for embedding)
 */
class org_tubepress_single_VideoImpl implements org_tubepress_single_Video, org_tubepress_ioc_ContainerAware
{
    private $_provider;
    private $_template;
    private $_container;
    private $_tpom;
    private $_optionsReference;
    private $_messageService;
    private $_log;
    private $_logPrefix;
    private $_templateDir;
    
    public function __construct()
    {
        $this->_logPrefix = 'Single Video';
        
        /* SET THE TEMPLATE DIRECTORY HERE. DON'T FORGET THE TRAILING SLASH ;)  */
        $this->_templateDir = dirname(__FILE__) . '/../../../../ui/single_video/html_templates/';
    }
    
    public function getSingleVideoHtml($videoId)
    {
        try {
            return $this->_getSingleVideoHtml($videoId);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function _getSingleVideoHtml($videoId)
    {
        /* grab the video from the provider */
        $this->_log->log($this->_logPrefix, 'Asking provider for video with ID %s', $videoId);
        $video = $this->_provider->getSingleVideo($videoId);
        
        $this->_prepTemplate($video);
        
        /* staples - that was easy */
        return $this->_template->toString();
    }
    
    private function _prepTemplate($video)
    {
        $customTemplate = $this->_tpom->get(org_tubepress_options_category_Template::TEMPLATE);
            
        if ($customTemplate != '') {
            $template = realpath($this->_templateDir . $customTemplate);
            $this->_log->log($this->_logPrefix, 'Using custom template at %s', $template);
            $this->_template->setPath($template);
        }
        
        $metaNames = $this->_optionsReference->getOptionNamesForCategory(org_tubepress_options_Category::META);
        $shouldShow = array();
        $labels = array();
        foreach ($metaNames as $metaName) {
            $shouldShow[$metaName] = $this->_tpom->get($metaName);
            $labels[$metaName] = $this->_messageService->_('video-' . $metaName);            
        }
        $this->_template->setVariable(org_tubepress_template_Template::META_SHOULD_SHOW, $shouldShow);
        $this->_template->setVariable(org_tubepress_template_Template::META_LABELS, $labels);
        
        /* apply it to the template */
        $eps = $this->_container->safeGet($this->_tpom->get(org_tubepress_options_category_Embedded::PLAYER_IMPL) . "-embedded", 
            org_tubepress_embedded_EmbeddedPlayerService::DDEFAULT . "-embedded");
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_SOURCE, $eps->toString($video->getId()));
        $this->_template->setVariable(org_tubepress_template_Template::VIDEO, $video);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_WIDTH, $this->_tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH));
        
        $this->_prepUrlPrefixes();
    }
    
    private function _prepUrlPrefixes()
    {
        $provider = $this->_tpom->calculateCurrentVideoProvider();
        if ($provider === org_tubepress_video_feed_provider_Provider::YOUTUBE) {
            $this->_template->setVariable(org_tubepress_template_Template::AUTHOR_URL_PREFIX, 'http://www.youtube.com/profile?user=');
            $this->_template->setVariable(org_tubepress_template_Template::VIDEO_SEARCH_PREFIX, 'http://www.youtube.com/results?search_query=');            
        } else {
            $this->_template->setVariable(org_tubepress_template_Template::AUTHOR_URL_PREFIX, 'http://vimeo.com/');
            $this->_template->setVariable(org_tubepress_template_Template::VIDEO_SEARCH_PREFIX, 'http://vimeo.com/videos/search:'); 
        }        
    }
    
    public function setVideoProvider(org_tubepress_video_feed_provider_Provider $provider) { $this->_provider = $provider; }
    public function setTemplate(org_tubepress_template_Template $template) { $this->_template = $template; }
    public function setContainer(org_tubepress_ioc_IocService $container) { $this->_container = $container; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $mgr) { $this->_tpom = $mgr; }
    public function setOptionsReference(org_tubepress_options_reference_OptionsReference $ref) { $this->_optionsReference = $ref; }
    public function setMessageService(org_tubepress_message_MessageService $messageService) {     $this->_messageService     = $messageService; }
    public function setLog(org_tubepress_log_Log $log) { $this->_log = $log; }
}

