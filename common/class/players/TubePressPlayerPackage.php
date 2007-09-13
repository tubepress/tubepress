<?php
/**
 * TubePressPlayerPackage.php
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
    
class_exists("TPGreyBoxPlayer") || require("TPGreyBoxPlayer.php");
class_exists("TPlightWindow") || require("TPlightWindowPlayer.php");
class_exists("TPNewWindowPlayer") || require("TPNewWindowPlayer.php");
class_exists("TPNormalPlayer") || require("TPNormalPlayer.php");
class_exists("TPPopupPlayer") || require("TPPopupPlayer.php");
class_exists("TPYouTubePlayer") || require("TPYouTubePlayer.php");

/**
 * A TubePress "mode", such as favorites, popular, playlist, etc
 */
class TubePressPlayerPackage extends TubePressDataPackage
{	
	/**
	 * Default constructor
	 */
    function TubePressPlayerPackage()
    {
        $this->_dataArray = TubePressPlayerPackage::getDefaultPackage();
    }
    
    /**
     * Figures out which player the user wants, and spits out the JS and CSS
     * needed by that player
     */
    function getHeadContents($box) {

    	/* first get the player object */
        $playerOpt = $box->options->get(TP_OPT_PLAYIN);
        if (PEAR::isError($playerOpt)) {
            return "";
        }

        /* now get the player name */
        $playerName = $playerOpt->getValue();
        if (PEAR::isError($playerName)) {
            return "";
        }

        /* get the mode object that represents it */
        $playerObj = $box->players->get($playerName);
		if (PEAR::isError($playerObj)) {
    		return "";
    	}
    	
    	$jsLibs = $playerObj->getJS();
    	$cssLibs = $playerObj->getCSS();
    	$extraJS = $playerObj->getExtraJS();
    	
    	$content = "";

    	if ($extraJS != "") {
        	$content .= "<script type=\"text/javascript\">" . $extraJS . "</script>";
        }
    	
    	foreach ($jsLibs as $jsLib) {
    		$content .= "<script type=\"text/javascript\" src=\"" . $jsLib . "\"></script>";
    	}
    	
    	foreach ($cssLibs as $cssLib) {
    		$content .= "<link rel=\"stylesheet\" href=\"" . $cssLib . "\"" .
            	" type=\"text/css\" />";
    	}
            	
        return $content;
	}

	/**
	 * Returns all of our players that we know about
	 */
    function getDefaultPackage() {
        
    	return array(
    	
    		TP_PLAYIN_NORMAL => new TPNormalPlayer(),
    		TP_PLAYIN_NW => new TPNewWindowPlayer(),
    		TP_PLAYIN_YT => new TPYouTubePlayer(),
    		TP_PLAYIN_POPUP => new TPPopupPlayer(),
    		TP_PLAYIN_LWINDOW => new TPlightWindowPlayer(),
    		TP_PLAYIN_GREYBOX => new TPGreyBoxPlayer()
    	);
    }
    
    /**
     * Ugly, but fast
     */
    function getNames()
    {
        return array(TP_PLAYIN_NORMAL, TP_PLAYIN_NW, TP_PLAYIN_YT,
            TP_PLAYIN_POPUP, TP_PLAYIN_LWINDOW, TP_PLAYIN_GREYBOX);
    }
    
    /**
     * Which types of items can we hold?
     */
    function getValidTypes()
    {
        return array("TPGreyBoxPlayer", "TPlightWindowPlayer",
            "TPNewWindowPlayer", "TPNormalPlayer", "TPPopupPlayer", "TPYouTubePlayer");
    }
}
?>
