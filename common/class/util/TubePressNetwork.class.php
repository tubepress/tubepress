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
        /* First generate the request URL */
        $request = TubePressGalleryUrl::get($tpom);
        
        $data = "";
        if ($tpom->get(TubePressAdvancedOptions::CACHE_ENABLED)) {
        	/* get a handle to the cache */
        	$cache = new Cache_Lite(array("cacheDir" => sys_get_temp_dir()));
        	
            /* cache miss? */
            if (!($data = $cache->get($request))) {
        	
        	    /* go out and grab the response */
                $data = TubePressNetwork::_fetchFromNetwork($request);
                /* and save it to the cache for next time */
          	    $cache->save($data, $request);
            }
        } else {
        	$data = TubePressNetwork::_fetchFromNetwork($request);
        }

        $doc = new DOMDocument();
    
        if ($doc->loadXML($data) === FALSE) {
        	throw new Exception("YouTube didn't return XML for this query.");
        }
        return $doc;
    }
    
    private static function _fetchFromNetwork($request) {
    	$data = "";
    	$req = new HTTP_Request($request);
    	$call = $req->sendRequest();
        if (!PEAR::isError($call)) {
            $data = $req->getResponseBody();
        } else {
        	throw new Exception($call->getMessage());
        }
        return $data;
    }
}