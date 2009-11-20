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
tubepress_load_classes(array('org_tubepress_player_Player',
    'org_tubepress_ioc_ContainerAware',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_template_Template',
    'org_tubepress_ioc_IocService',
    'org_tubepress_browser_BrowserDetector'));

/**
 * A TubePress "player", such as lightWindow, GreyBox, popup window, etc
 */
abstract class org_tubepress_player_AbstractPlayer implements org_tubepress_player_Player, org_tubepress_ioc_ContainerAware
{   
    private $_optionsManager;
    private $_iocContainer;
    private $_template;
    private $_browserDetector;
    
    public function getPreGalleryHtml(org_tubepress_video_Video $vid, $galleryId)
    {
        $browser = $this->_browserDetector->detectBrowser($_SERVER);
        if ($browser === org_tubepress_browser_BrowserDetector::IPHONE || $browser === org_tubepress_browser_BrowserDetector::IPOD) {
            return '';
        }
        return $this->doGetPreGalleryHtml($vid, $galleryId);
    }
    
    public function setContainer(org_tubepress_ioc_IocService $container) { $this->_iocContainer = $container; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $optionsManager) { $this->_optionsManager = $optionsManager; }
    public function setTemplate(org_tubepress_template_Template $template) { $this->_template = $template; }
    public function setBrowserDetector(org_tubepress_browser_BrowserDetector $detector) { $this->_browserDetector = $detector; }
    
    protected function getContainer() { return $this->_iocContainer; }
    protected function getOptionsManager() { return $this->_optionsManager; }
    protected function getTemplate() { return $this->_template; }
    
    protected abstract function doGetPreGalleryHtml(org_tubepress_video_Video $vid, $galleryId);
}

