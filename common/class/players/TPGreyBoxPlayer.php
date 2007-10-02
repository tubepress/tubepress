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
	/**
	 * Default constructor
	 */
	function TPGreyBoxPlayer() {
		
	    global $tubepress_base_url;
	    
	    $this->_title = _tpMsg("PLAYIN_TB_TITLE");
		
		$gbURL = $tubepress_base_url . "/lib/greybox/";
    	$gbJS = array($gbURL . "AJS.js",
    	$gbURL . "AJS_fx.js",
    	$gbURL . "gb_scripts.js");
    	$gbCSS = array($gbURL . "gb_styles.css");
    	$extra = "var GB_ROOT_DIR = \"" . $gbURL . "/\"";

		$this->_cssLibs = $gbCSS;
		$this->_jsLibs = $gbJS;
		$this->_extraJS = $extra;
	}
	
	/**
	 * Tells the gallery how to play the vids
	 */
	public function getPlayLink($vid, $options)
	{
	    global $tubepress_base_url;
	    
	    $widthOpt = $options->get(TP_OPT_VIDWIDTH);
	    $width = $widthOpt->getValue();
	    
	    $heightOpt = $options->get(TP_OPT_VIDHEIGHT);
	    $height = $heightOpt->getValue();
	    
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
