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

class_exists("TubePressPlayer")
    || require("TubePressPlayer.php");

/**
 * A TubePress "mode", such as favorites, popular, playlist, etc
 */
class TubePressPlayerPackage extends TubePressDataPackage
{	
    function TubePressPlayerPackage()
    {
        $this->_validTypes = array("TubePressPlayer");
        $this->_dataArray = TubePressPlayerPackage::getDefaultPackage();
    }
    
    function getHeadContents($box) {

        $mode = $box->options->get(TP_OPT_MODE);
        if (PEAR::isError($mode)) {
            return "";
        }

        $modeName = $mode->getValue();
        if (PEAR::isError($modeName)) {
            return "";
        }

        /* get the mode object that represents it */
        $modeObj = $box->players->get(TP_OPT_MODE);
		if (PEAR::isError($modeObj)) {
    		return "";
    	}
    	
    	$jsLibs = $modeObj->getJS();
    	$cssLibs = $modeObj->getCSS();
    	$extraJS = $modeObj->getExtraJS();
    	
    	$content = "";
    	
    	foreach ($jsLibs as $jsLib) {
    		$content .= "<script type=\"text/javascript\" src=\"" . $jsLib . "\"></script>";
    	}
    	
    	foreach ($cssLibs as $cssLib) {
    		$content .= "<link rel=\"stylesheet\" href=\"" . $cssLib . "\"" .
            	" type=\"text/css\" />";
    	}
            	
        if ($extraJS != "") {
        	$content .= "<script type=\"text/javascript\">" . $extraJS . "</script>";
        }
        return $content;
	}

    function getDefaultPackage() {
    
    	global $tubepress_base_url;

    	$gbURL = $tubepress_base_url . "/lib/greybox/";
    	$gbJS = array($gbURL . "AJS.js",
    	$gbURL . "AJS_fx.js",
    	$gbURL . "gb_scripts.js");
    	$gbCSS = array("gb_styles.css");
    	$extra = "var GB_ROOT_DIR = \"" . $tubepress_base_url . "/\"";

    	$lwURL = $tubepress_base_url . "/lib/lightWindow/";
    	$lwJS = array($lwURL . "javascript/prototype.js",
    	$lwURL . "javascript/effects.js",
    	$lwURL . "javascript/lightWindow.js");
    	$lwCSS = array("css/lightWindow.css");
    		
    	return array(
    	
    		TP_PLAYIN_NORMAL => new TubePressPlayer(_tpMsg("PLAYIN_NORMAL_TITLE"),
    			" ", " "),
    			
    		TP_PLAYIN_NW => new TubePressPlayer(_tpMsg("PLAYIN_NW_TITLE"),
    			" ", " "),
    			
    		TP_PLAYIN_YT => new TubePressPlayer(_tpMsg("PLAYIN_YT_TITLE"),
    			" ", " "),
    		
    		TP_PLAYIN_POPUP => new TubePressPlayer(_tpMsg("PLAYIN_POPUP_TITLE"),
    			" ", " "),
    			
    		TP_PLAYIN_LWINDOW => new TubePressPlayer(_tpMsg("PLAYIN_LW_TITLE"),
    			$lwCSS, $lwJS),
    			
    		
    		TP_PLAYIN_GREYBOX => new TubePressPlayer(_tpMsg("PLAYIN_TB_TITLE"),
    			$gbCSS, $gbJS, $extra)
    	);
    }
}
?>
