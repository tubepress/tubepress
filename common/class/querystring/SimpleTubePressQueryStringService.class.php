<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
 * Simple implementation of TubePressQueryStringService
 */
class SimpleTubePressQueryStringService implements TubePressQueryStringService
{
    /**
     * Returns what's in the address bar
     * 
     * @return string What's in the address bar
     */
    public function getFullUrl($serverVars)
    {
        $pageURL = 'http';
        if ($serverVars["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($serverVars["SERVER_PORT"] != "80") {
             $pageURL .= $serverVars["SERVER_NAME"].":".
                 $serverVars["SERVER_PORT"].$serverVars["REQUEST_URI"];
        } else {
             $pageURL .= $serverVars["SERVER_NAME"].$serverVars["REQUEST_URI"];
        }
        return $pageURL;
    }
    
    /**
     * Try to figure out what page we're on by looking at the query string
     * Defaults to '1' if there's any doubt
     * 
     * @return int The page number
     */
    public function getPageNum($getVars)
    {
        $pageNum = ((isset($getVars["tubepress_page"])) ?
            $getVars["tubepress_page"] : 1);
        
        if (!is_numeric($pageNum) || ($pageNum < 1)) {
            $pageNum = 1;
        }
        return $pageNum;
    }
    
    public function getCustomVideo($getVars)
    {
        return isset($getVars["tubepress_video"]) ?
            $getVars["tubepress_video"] : "";
    }
}
?>
