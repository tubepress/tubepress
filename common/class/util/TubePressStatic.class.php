<?php
/**
 * TubePressStatic.php
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
 * A bunch of "static" utilities that are used throughout the app
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
        $pageNum = ((isset($_GET[TubePressGallery::pageParameter]))?
            $_GET[TubePressGallery::pageParameter] : 1);
            if (!is_numeric($pageNum)
                || ($pageNum < 1)) {
                $pageNum = 1;
            }
        return $pageNum;
    }
}
?>
