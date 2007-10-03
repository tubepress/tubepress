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

defined(TP_OPTION_NAME)
    || require(dirname(__FILE__) . "/../../defines.php");
function_exists("_tpMsg")
    || require(dirname(__FILE__) . "/../../messages.php");

/**
 * Manages all of the gallery modes that we know about
 */
class TubePressModePackage extends TubePressDataPackage
{
    private $_validTypes = array("TubePressMode");
    
	/**
	 * Default constructor
	 */
    public function TubePressModePackage() {
    	$this->_dataArray = array(
    	
    		TubePressMode::user => new TubePressMode("Videos from this YouTube user",
    			" ", "3hough"),
    			
    		TubePressMode::favorites => new TubePressMode("This YouTube user's \"favorites\"",
    			"YouTube limits this mode to the latest 500 favorites",
    			"mrdeathgod"),
    			
    		TubePressMode::playlist => new TubePressMode("This playlist",
    			"Limited to 200 videos per playlist. Will usually look something like this:" .
                  " D2B04665B213AE35. Copy the playlist id from the end of the " .
                  "URL in your browser's address bar (while looking at a YouTube " .
                  "playlist). It comes right after the 'p='. For instance: " .
                  "http://youtube.com/my_playlists?p=D2B04665B213AE35", "D2B04665B213AE35"),
    		
    		TubePressMode::tag => new TubePressMode("YouTube search for",
    			"YouTube limits this mode to 1,000 results", "stewart daily show"),
    			
    		TubePressMode::featured => new TubePressMode("The latest \"featured\" videos " .
                  "on YouTube's homepage", " ", " "),
    			
    		TubePressMode::popular => new TubePressMode("Most-viewed videos from...",
    			" ", "today"),
    		
    		TubePressMode::top_rated => new TubePressMode("Top rated videos from...",
    		    " ", "today"),
    		
    		TubePressMode::mobile => new TubePressMode("Videos for mobile phones",
    		    " ", " ")
    	);
    }
}
?>
