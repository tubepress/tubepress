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
 * Represents an HTML-embeddable YouTube player
 *
 */
class TubePressNetwork
{
    
    /**
     * Fetches the RSS from YouTube (or from cache)
     * 
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * 
     * @return DOMDocument The raw RSS from YouTube
     */
    public static function getRss(TubePressOptionsManager $tpom)
    {
        /* Grab the video XML from YouTube */
        $request = TubePressGalleryUrl::get($tpom);
        TubePressNetwork::_urlPostProcessing($request, $tpom);
        
        if (TubePressDebug::areWeDebugging($tpom)) {
        	echo $request;
        }
        
        $cache = new Cache_Lite(array("cacheDir" => sys_get_temp_dir()));

        if (!($data = $cache->get($request))) {
            $req =& new HTTP_Request($request);
            if (!PEAR::isError($req->sendRequest())) {
                $data = $req->getResponseBody();
            }
            $cache->save($data, $request);
        }
        
        $doc = new DOMDocument();
        
        if (substr($data, 0, 5) != "<?xml") {
            return $doc;
        }
    
        $doc->loadXML($data);
        return $doc;
    }
    
    /**
     * Appends some global query parameters on the request
     * before we fire it off to YouTube
     *
     * @param string                  &$request The request to be manipulated
     * @param TubePressOptionsManager $tpom     The TubePress options manager
     * 
     * @return void
     */
    private static function _urlPostProcessing(&$request, 
        TubePressOptionsManager $tpom)
    {
        
        $perPage = $tpom->get(TubePressDisplayOptions::RESULTS_PER_PAGE);
        $filter  = $tpom->get(TubePressAdvancedOptions::FILTER);
        $order   = $tpom->get(TubePressDisplayOptions::ORDER_BY);
        $mode    = $tpom->get(TubePressGalleryOptions::MODE);
        
        $currentPage = TubePressQueryString::getPageNum();
        
        $start = ($currentPage * $perPage) - $perPage + 1;
        
        if ($start + $val > 1000) {
            $val = 1000 - $start;
        }
        
        $requestURL = new Net_URL($request);
        $requestURL->addQueryString("start-index", $start);
        $requestURL->addQueryString("max-results", $perPage);
        
        if ($filter) {
            $requestURL->addQueryString("racy", "exclude");
        } else {
            $requestURL->addQueryString("racy", "include");
        }
      
        if ($mode != TubePressGallery::PLAYLIST) {
            $requestURL->addQueryString("orderby", $order);
        }       
        
        $request = $requestURL->getURL();
    }
}