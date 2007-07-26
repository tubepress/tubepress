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
class TubePressModePackage extends TubePressDataPackage
{
    function TubePressModePackage() {
    	$this->_dataArray = array(
    	
    		TP_MODE_USER => new TubePressMode(_tpMsg("MODE_USER_TITLE"),
    			"", "3hough"),
    			
    		TP_MODE_FAV => new TubePressMode(_tpMsg("MODE_FAV_TITLE"),
    			_tpMsg("MODE_FAV_DESC"), "mrdeathgod"),
    			
    		TP_MODE_PLST => new TubePressMode(_tpMsg("MODE_PLST_TITLE"),
    			_tpMsg("MODE_PLST_DESC"), "D2B04665B213AE35"),
    		
    		TP_MODE_TAG => new TubePressMode(_tpMsg("MODE_TAG_TITLE"),
    			"", "stewart daily show"),
    			
    		TP_MODE_FEATURED => new TubePressMode(_tpMsg("MODE_FEAT_TITLE"),
    			""),
    			
    		TP_MODE_POPULAR => new TubePressMode(_tpMsg("MODE_POPULAR_TITLE"),
    			"", "day")
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
