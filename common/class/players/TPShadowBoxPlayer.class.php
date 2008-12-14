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
 * Plays videos with GreyBox
 */
class TPShadowBoxPlayer extends TubePressPlayer
{
    /**
     * Constructor
     *
     * @return void
     */    
    public function __construct()
    {
        global $tubepress_base_url;

        $sbUrl = $tubepress_base_url . "/lib/shadowbox/";
        $gbJS  = array(
            $sbUrl . "src/js/lib/yui-utilities.js",
            $sbUrl . "src/js/adapter/shadowbox-yui.js",
            $sbUrl . "src/js/shadowbox.js");
        
        
        $sbCSS  = array($sbUrl . "src/css/shadowbox.css");
        $extra  = "YAHOO.util.Event.onDOMReady(function() " .
            "{var options = { assetURL: ";
        $extra .= "'" . $sbUrl . "'";
        $extra .= "}; Shadowbox.init(options);});";
        
        $this->setCSSLibs($sbCSS);
        $this->setJSLibs($gbJS);
        $this->setPostLoadJs($extra);
    }
    
    /**
     * Tells the gallery how to play videos in ShadowBox.js
     *
     * @param TubePressVideo          $vid  The video to be played
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * 
     * @return string The play link attributes
     */
    public function getPlayLink(TubePressVideo $vid, TubePressOptionsManager $tpom)
    {
        global $tubepress_base_url;

        $title  = $vid->getTitle();
        $height = $tpom->get(TubePressEmbeddedOptions::EMBEDDED_HEIGHT);
        $width  = $tpom->get(TubePressEmbeddedOptions::EMBEDDED_WIDTH);

        $embed = new TubePressEmbeddedPlayer($vid, $tpom);
        
        $url = new Net_URL2($tubepress_base_url . "/common/ui/popup.php");
        $url->setQueryVariable("embed", $embed->toString());
        $url->setQueryVariable("name", $title);
        
        //return "href='#' onclick='tubePress_popup(" .
        //    '"' .  . '",' . $height . ',' . $width . ')\''; 
        
        return sprintf('href="%s" title="%s" ' .
            'rel="shadowbox;height=%s;width=%s"',
		$url->getURL(true), $title, $height, $width); 
   }
   
    public function getPreGalleryHtml(TubePressVideo $vid, TubePressOptionsManager $tpom)
    {
    	return "";
    }
}
?>
