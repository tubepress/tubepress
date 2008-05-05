<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * A bunch of utilities that are used throughout the app
 */
final class TubePressStatic
{    
    /**
     * Returns what's in the address bar (obviously, only http, not https)
     */
    public static final function fullURL()
    {
        $pageURL = 'http';
 		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 		$pageURL .= "://";
 		if ($_SERVER["SERVER_PORT"] != "80") {
  			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 		} else {
  			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 		}
 		return $pageURL;
    }
    
    /**
     * Try to figure out what page we're on by looking at the query string
     * Defaults to '1' if there's any doubt
     */
    public static final function getPageNum()
    {
        $pageNum = ((isset($_GET[TubePressGallery::PAGE_PARAM]))?
            $_GET[TubePressGallery::PAGE_PARAM] : 1);
            if (!is_numeric($pageNum)
                || ($pageNum < 1)) {
                $pageNum = 1;
            }
        return $pageNum;
    }
    
    public static final function debugEnvironment(TubePressStorage_v160 $options) {
    	echo "<ol><li>";
    	global $tubepress_base_url;
    	echo "<code>tubepress_base_url</code> is defined to be <code>" . $tubepress_base_url . "</code>";
    	echo "</li><li>Your options: <code>" . var_dump($options) . "</code>";
    	
    	echo "</li></ol>";
    }
    
    public static final function areWeDebugging(TubePressStorage_v160 $options) {
    	$enabled = $options->getCurrentValue(TubePressAdvancedOptions::debugEnabled);
    	return $enabled
    		&& isset($_GET['tubepress_debug'])
    		&& ($_GET['tubepress_debug'] == 'true');
    }
}
?>
