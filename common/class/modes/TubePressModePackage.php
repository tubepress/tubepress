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

class_exists("TubePressMode")
    || require("TubePressMode.php");

/**
 * Manages all of the gallery modes that we know about
 */
class TubePressModePackage extends TubePressDataPackage
{
	/**
	 * Default constructor
	 */
    function TubePressModePackage() {
    	$this->_dataArray = TubePressModePackage::getDefaultPackage();
    }
    
    /**
     * Returns an array of all the modes we know about
     */
    function getDefaultPackage()
    {
        return array(
    	
    		TP_MODE_USER => new TubePressMode(_tpMsg("MODE_USER_TITLE"),
    			" ", "3hough"),
    			
    		TP_MODE_FAV => new TubePressMode(_tpMsg("MODE_FAV_TITLE"),
    			_tpMsg("MODE_FAV_DESC"), "mrdeathgod"),
    			
    		TP_MODE_PLST => new TubePressMode(_tpMsg("MODE_PLST_TITLE"),
    			_tpMsg("MODE_PLST_DESC"), "D2B04665B213AE35"),
    		
    		TP_MODE_TAG => new TubePressMode(_tpMsg("MODE_TAG_TITLE"),
    			" ", "stewart daily show"),
    			
    		TP_MODE_FEATURED => new TubePressMode(_tpMsg("MODE_FEAT_TITLE"),
    			" ", " "),
    			
    		TP_MODE_POPULAR => new TubePressMode(_tpMsg("MODE_POPULAR_TITLE"),
    			" ", "today"),
    		
    		TP_MODE_TOPRATED => new TubePressMode("Top rated videos from...",
    		    " ", "today"),
    		
    		TP_MODE_MOBILE => new TubePressMode("Videos for mobile phones",
    		    " ", " ")
    	);
    }
    
    /**
     * Ugly but fast
     */
    function getNames() {
        return array(TP_MODE_USER, TP_MODE_FAV, TP_MODE_PLST, TP_MODE_TAG,
            TP_MODE_FEATURED, TP_MODE_POPULAR, TP_MODE_TOPRATED, TP_MODE_MOBILE);
    }
    
    /**
     * We can only hold modes!
     */
    function getValidTypes() {
        return array("TubePressMode");
    }
    
    
    function setValue($modeName, $modeValue) {
    	if (is_array_key($modeName, TubePressModePackage::getNames())) {
    		$this->_allModes[$modeName]->setValue($modeValue);
    	} else {
    		PEAR::raiseError($modeName . " is not a valid mode");
    	}
    }
}
?>
