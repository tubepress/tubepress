<?php
/**
 * TPGreyBoxPlayer.php
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
class TPGreyBoxPlayer extends TubePressPlayer
{
    public function __construct() {
        $this->setName(TubePressPlayer::lightWindow);
        $this->setTitle("with GreyBox (experimental... enable it above)");
   	
	    global $tubepress_base_url;

		$gbURL = $tubepress_base_url . "/lib/greybox/";
    	$gbJS = array($gbURL . "AJS.js",
    	    $gbURL . "AJS_fx.js",
    	    $gbURL . "gb_scripts.js");
    	
    	$gbCSS = array($gbURL . "gb_styles.css");
    	$extra = "var GB_ROOT_DIR = \"" . $gbURL . "/\"";

		$this->setCSSLibs($gbCSS);
		$this->setJSLibs($gbJS);
		$this->setExtraJS($extra);
	}
	
	/**
	 * Tells the gallery how to play the vids
	 */
	public function getPlayLink(TubePressVideo $vid, $height, $width)
	{
	    global $tubepress_base_url;

	    $title = $vid->getTitle();
	    $id = $vid->getId();
	    
	    return sprintf(
            'href="%s/common/popup.php?h=%s&amp;w=%s' .
            '&amp;id=%s&amp;name=%s" title="%s" ' .
            'rel="gb_page_center[%s, %s]"',
            $tubepress_base_url, $height, $width,
            $id, rawurlencode($title), $title, 
            $width, $height);
	}
}
?>
