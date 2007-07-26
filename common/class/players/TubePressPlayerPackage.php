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
class TubePressPlayerPackage extends TubePressDataPackage
{	
	function getHeadContents($optionsArray) {
		if (!array_key_exists(TP_OPT_MODE, $optionsArray)) {
    		return "";
    	}
    
    	if (!is_a($optionsArray[TP_OPT_MODE], "TubePressStringOpt")) {
        	return "";
    	}
    	
    	/* find out which mode we're supposed to be in */
    	$modeName = $optionsArray->getValue([TP_OPT_MODE]);
    	if (PEAR::isError($modeName)) {
    		return "";
    	}
    	
    	/* get the mode object that represents it */
    	$pack = new TubePressPlayerPackage();
    	$modeObj = $pack->_get(TP_OPT_MODE);
		if (PEAR::isError($modeObj)) {
    		return "";
    	}
    	
    	$jsLibs = $modeObj->getJS();
    	$cssLibs = $modeObj->getCSS();
    	$extraJS = $modeObj->getExtraJS();
    	
    	$content = "";
    	
    	foreach ($jsLibs as $jsLib) {
    		$content .= "<script type=\"text/javascript\" src=\"" . $jsLib "\"></script>";
    	}
    	
    	foreach ($cssLibs as $cssLib) {
    		$content .= "<link rel=\"stylesheet\" href=\"" . $cssLib . "\"" .
            	" type=\"text/css\" />"
    	}
            	
        if ($extraJS != "") {
        	$content .= "<script type=\"text/javascript\">" . $extraJS . "</script>";
        }
        return $content;
	}

    function TubePressPlayerPackage() {
    
    	global $tubepress_base_url;
    
    	$this->_dataArray = array(
    	
    		TP_PLAYIN_NORMAL => new TubePressPlayer(_tpMsg("PLAYIN_NORMAL_TITLE"),
    			"", ""),
    			
    		TP_PLAYIN_NW => new TubePressPlayer(_tpMsg("PLAYIN_NW_TITLE"),
    			"", ""),
    			
    		TP_PLAYIN_YT => new TubePressPlayer(_tpMsg("PLAYIN_YT_TITLE"),
    			"", ""),
    		
    		TP_PLAYIN_POPUP => new TubePressPlayer(_tpMsg("PLAYIN_POPUP_TITLE"),
    			"", ""),
    			
    		$lwURL = $tubepress_base_url . "/lib/lightWindow/";
    		$lwJS = array($lwURL . "javascript/prototype.js",
    			$lwURL . "javascript/effects.js",
    			$lwURL . "javascript/lightWindow.js");
    		$lwCSS = array("css/lightWindow.css");
    		TP_PLAYIN_LWINDOW => new TubePressMode(_tpMsg("PLAYIN_LW_TITLE"),
    			$lwCSS, $lwJS),
    			
    		$gbURL = $tubepress_base_url . "/lib/greybox/";
    		$gbJS = array($gbURL . "AJS.js",
    			$gbURL . "AJS_fx.js",
    			$gbURL . "gb_scripts.js");
    		$bgCSS = array("gb_styles.css");
    		$extra = "var GB_ROOT_DIR = \"" . $url . "\/\"";
    		TP_PLAYIN_GREYBOX => new TubePressMode(_tpMsg("PLAYIN_TB_TITLE"),
    			"", "", $extra)
    	);
    }
}
?>
