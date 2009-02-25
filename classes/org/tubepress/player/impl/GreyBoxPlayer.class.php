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
 * Plays videos with GreyBox
 */
class org_tubepress_player_impl_GreyBoxPlayer extends org_tubepress_player_AbstractPlayer implements org_tubepress_ioc_ContainerAware
{
    private $_iocContainer;
    
    protected function getPreLoadJs()
    {
        return "var GB_ROOT_DIR = \"" . $this->_getGbBaseUrl() . "\"";
    }
    
    protected function getJSLibs()
    {
        $gbURL = $this->_getGbBaseUrl();
        return array($gbURL . "AJS.js",
            $gbURL . "AJS_fx.js",
            $gbURL . "gb_scripts.js");
    }
    
    protected function getCSSLibs()
    {
        return array($this->_getGbBaseUrl() . "gb_styles.css");
    }
    
    /**
     * Tells the gallery how to play videos in GreyBox
     *
     * @param org_tubepress_video_Video          $vid  The video to be played
     * @param org_tubepress_options_manager_OptionsManager $tpom The TubePress options manager
     * 
     * @return string The play link attributes
     */
    public function getPlayLink(org_tubepress_video_Video $vid, org_tubepress_options_manager_OptionsManager $tpom)
    {
        global $tubepress_base_url;

        $title  = $vid->getTitle();
        $height = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT);
        $width  = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH);
        $url = new net_php_pear_Net_URL2($tubepress_base_url . "/ui/players/popup.php");
        $url->setQueryVariable("id", $vid->getId());
        $eps = $this->_iocContainer->safeGet($tpom->get(org_tubepress_options_category_Embedded::PLAYER_IMPL) . "-embedded", org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE . "-embedded");
        $url->setQueryVariable("opts", $eps->packOptionsToString($vid, $tpom));
       
        return sprintf(<<<EOT
href="%s" title="%s" rel="gb_page_center[%s, %s]"
EOT
            , $url->getURL(true), $title, $width, $height);
    }
    
    private function _getGbBaseUrl()
    {
        global $tubepress_base_url;

        return $tubepress_base_url . "/ui/players/greybox/";
    }
    
    public function setContainer(org_tubepress_ioc_IocService $container)
    {
        $this->_iocContainer = $container;
    }
}
?>
