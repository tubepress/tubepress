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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_querystring_QueryParamName',
    'org_tubepress_api_querystring_QueryStringService',
));

/**
 * Handles some tasks related to the query string
 */
class org_tubepress_impl_querystring_SimpleQueryStringService implements org_tubepress_api_querystring_QueryStringService
{
    /**
     * Try to get the custom video ID from the query string
     *
     * @param array $getVars The PHP $_GET array
     *
     * @return string The custom video ID, or '' if not set
    */
    public function getCustomVideo($getVars)
    {
        return self::_getQueryVar($getVars, org_tubepress_api_const_querystring_QueryParamName::VIDEO);
    }

    public function getSearchTerms($getVars)
    {
        return self::_getQueryVar($getVars, org_tubepress_api_const_querystring_QueryParamName::SEARCH_TERMS);
    }

    /**
     * Returns what's in the address bar
     * 
     * @param array $serverVars The PHP $_SERVER array
     *
     * @return string What's in the address bar
     */
    public function getFullUrl($serverVars)
    {
        $pageURL = 'http';
        if (isset($serverVars['HTTPS']) && $serverVars['HTTPS'] == 'on') {
            $pageURL .= 's';
        }
        $pageURL .= '://';
        if ($serverVars['SERVER_PORT'] != '80') {
             $pageURL .= $serverVars['SERVER_NAME'].':'.
                 $serverVars['SERVER_PORT'].$serverVars['REQUEST_URI'];
        } else {
             $pageURL .= $serverVars['SERVER_NAME'].$serverVars['REQUEST_URI'];
        }
        return $pageURL;
    }

    /**
     * Try to figure out what page we're on by looking at the query string
     * Defaults to '1' if there's any doubt
     * 
     * @param array $getVars The PHP $_GET array
     *
     * @return int The page number
     */
    public function getPageNum($getVars)
    {
        $key     = org_tubepress_api_const_querystring_QueryParamName::PAGE;
        $pageNum = ((isset($getVars[$key])) ?
            $getVars[$key] : 1);

        if (!is_numeric($pageNum) || ($pageNum < 1)) {
            $pageNum = 1;
        }
        return $pageNum;
    }

    /**
     * Try to get the shortcode from the query string
     *
     * @param array $getVars The PHP $_GET array
     *
     * @return string The shortcode, or '' if not set
     */
    public function getShortcode($getVars)
    {
        return self::_getQueryVar($getVars, org_tubepress_api_const_querystring_QueryParamName::SHORTCODE);
    }

    /**
     * Do a check for a get variable
     *
     * @param array  $getVars The PHP $_GET array
     * @param string $key     The name of the variable to check for
     *
     * @return string The value of the variable, or '' if it doesn't exist
     */
    private static function _getQueryVar($getVars, $key)
    {
        return isset($getVars[$key]) ? $getVars[$key] : '';
    }
}
