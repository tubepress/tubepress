<?php
/**
 * TPShadowBoxPlayer.php
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/**
 * Plays videos with GreyBox
 */
class TPShadowBoxPlayer extends TubePressPlayer
{
    public function __construct() {
        $this->setName(TubePressPlayer::shadowBox);
        $this->setTitle("with Shadowbox.js (experimental)");
   	
	    global $tubepress_base_url;

		$sbUrl = $tubepress_base_url . "/lib/shadowbox/";
    	$gbJS = array(
    		$sbUrl . "src/js/lib/yui-utilities.js",
    		$sbUrl . "src/js/adapter/shadowbox-yui.js",
    		$sbUrl . "src/js/shadowbox.js");
    	
    	
    	$sbCSS = array($sbUrl . "src/css/shadowbox.css");
    	$extra = "YAHOO.util.Event.onDOMReady(function() {var options = { assetURL: ";
    	$extra .= "'" . $sbUrl . "'";
		$extra .= "}; Shadowbox.init(options);});";
		
		$this->setCSSLibs($sbCSS);
		$this->setJSLibs($gbJS);
		$this->setPostLoadJs($extra);
	}
	
	/**
	 * Tells the gallery how to play the vids
	 */
	public function getPlayLink(TubePressVideo $vid, TubePressStorage_v157 $stored)
	{
	    global $tubepress_base_url;

	    $title = $vid->getTitle();
	    $height = $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedHeight);
	    $width = $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedWidth);
		$embed = new TubePressEmbeddedPlayer($vid, $stored);
		
	    $url = new Net_URL($tubepress_base_url . "/common/ui/popup.php");
	    $url->addQueryString("embed", $embed->toString());
	    $url->addQueryString("name", $title);
	   
	    return sprintf('href="%s" title="%s" ' .
            'rel="shadowbox;height=%s;width=%s"',
            $url->getURL(), $title, $height, $width);
	}
}
?>
