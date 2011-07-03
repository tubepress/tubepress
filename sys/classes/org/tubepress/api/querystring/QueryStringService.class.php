<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
 * Handles some tasks related to the query string
 */
interface org_tubepress_api_querystring_QueryStringService
{
    /**
     * Try to get the custom video ID from the query string
     *
     * @param array $getVars The PHP $_GET array
     *
     * @return string The custom video ID, or '' if not set or if there was a problem.
    */
    function getCustomVideo($getVars);

    /**
     * Returns what's in the address bar
     * 
     * @param array $serverVars The PHP $_SERVER array
     *
     * @return string What's in the address bar
     */
    function getFullUrl($serverVars);

    /**
     * Try to figure out what page we're on by looking at the query string
     * Defaults to '1' if there's any doubt
     * 
     * @param array $getVars The PHP $_GET array
     *
     * @return int The page number, or 1 if there was a problem.
     */
    function getPageNum($getVars);

    /**
     * Try to get the shortcode from the query string
     *
     * @param array $getVars The PHP $_GET array
     *
     * @return string The shortcode, or '' if not set or if there was a problem.
     */
    function getShortcode($getVars);

    function getSearchTerms($getVars);
}
