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
    'org_tubepress_message_MessageService'));

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
    
    public function getSingleVideoHtml($videoId)
    {
        /* grab the video from the provider */
        $video = $this->_provider->getSingleVideo($videoId);
        
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
            org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE . "-embedded");
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_SOURCE, $eps->toString($video->getId()));
        $this->_template->setVariable(org_tubepress_template_Template::VIDEO, $video);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_WIDTH, $this->_tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH));
        
        /* staples - that was easy */
        return $this->_template->toString();    
    }
    
    public function setProvider(org_tubepress_video_feed_provider_Provider $provider) { $this->_provider = $provider; }
    public function setTemplate(org_tubepress_template_Template $template) { $this->_template = $template; }
    public function setContainer(org_tubepress_ioc_IocService $container) { $this->_container = $container; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $mgr) { $this->_tpom = $mgr; }
    public function setOptionsReference(org_tubepress_options_reference_OptionsReference $ref) { $this->_optionsReference = $ref; }
    public function setMessageService(org_tubepress_message_MessageService $messageService) {     $this->_messageService     = $messageService; }
}

