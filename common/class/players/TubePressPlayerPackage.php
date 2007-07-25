<?php
/**
 * TubePressModePackage.php
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
 * A TubePress "mode", such as favorites, popular, playlist, etc
 */
class TubePressPlayerPackage 
{
	var $_allPlayers;
    
	    /**
     * The valid ways to play each video (new window, popup, lightWindow, etc)
     */
    function getPlayerLocationNames()
    {
        return
            array(, TP_PLAYIN_NW, TP_PLAYIN_YT, 
                TP_PLAYIN_POPUP,TP_PLAYIN_LWINDOW,TP_PLAYIN_GREYBOX);
    }   
	
    function TubePressPlayerPackage() {
    	$this->_allModes = array(
    	
    		TP_PLAYIN_NORMAL => new TubePressPlayer(_tpMsg("PLAYIN_NORMAL_TITLE"),
    			"", ""),
    			
    		TP_PLAYIN_NW => new TubePressPlayer(_tpMsg("PLAYIN_NW_TITLE"),
    			"", ""),
    			
    		TP_PLAYIN_YT => new TubePressPlayer(_tpMsg("PLAYIN_YT_TITLE"),
    			"", ""),
    		
    		TP_PLAYIN_POPUP => new TubePressMode(_tpMsg("PLAYIN_POPUP_TITLE"),
    			"", ""),
    			
    		$lwURL = "/lib/lightWindow/";
    		$lwJS = array($lwURL . "javascript/prototype.js",
    			$lwURL . "javascript/effects.js",
    			$lwURL . "javascript/lightWindow.js");
    		$lwCSS = array("css/lightWindow.css");
    		TP_PLAYIN_LWINDOW => new TubePressMode(_tpMsg("PLAYIN_LW_TITLE"),
    			$lwCSS, $lwJS),
    			
    		$gbURL = "/lib/greybox/";
    		$gbJS = array($gbURL . "AJS.js",
    			$gbURL . "AJS_fx.js",
    			$gbURL . "gb_scripts.js");
    		$bgCSS = array("gb_styles.css");
    		TP_PLAYIN_GREYBOX => new TubePressMode(_tpMsg("PLAYIN_TB_TITLE"),
    			"", "")
    	);
    }
    
    function setValue($modeName, $modeValue) {
    	if (is_array_key) {
    		$this->_allModes[$modeName]->setValue($modeValue);
    	} else {
    		PEAR::raiseError($modeName . " is not a valid mode");
    	}
    }
}
?>
