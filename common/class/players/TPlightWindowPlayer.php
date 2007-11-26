<?php
/**
 * TPlightWindowPlayer.php
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
 * Plays videos with lightWindow
 */
class TPlightWindowPlayer extends TubePressPlayer
{
    public function __construct() {
        $this->setName(TubePressPlayer::lightWindow);
        $this->setTitle("with lightWindow (experimental... enable it above)");

		global $tubepress_base_url;

		$lwURL = $tubepress_base_url . "/lib/lightWindow/";
    	
		$lwJS = array($lwURL . "javascript/prototype.js",
    	    $lwURL . "javascript/scriptaculous.js?load=effects",
    	    $lwURL . "javascript/lightwindow.js?shit=poop");
		
    	$this->setJSLibs($lwJS);
		$this->setCSSLibs(array($lwURL . "css/lightwindow.css"));
		$this->setExtraJS("var tubepressLWPath = \"" . $lwURL . "\"");
	}
	
	/**
	 * Tells the gallery how to play the videos
	 */
	function getPlayLink(TubePressVideo $vid, $height, $width)
	{
	    global $tubepress_base_url;
	    
	    $title = $vid->getTitle();
	    $id = $vid->getId();
	    
	    return sprintf('href="%s/common/popup.php?' .
            'name=%s&id=%s&w=%s&h=%s" class="lightwindow" title="%s" ' .
            'params="lightwindow_width=%s,lightwindow_height=%s"', 
            $tubepress_base_url, rawurlencode($title), $id,
            $width, $height, $title, 
            $width, $height);
	}
}
?>
