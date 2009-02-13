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
 * Base functionality for TubePressFeedRetrieval services
 *
 */
abstract class org_tubepress_gdata_retrieval_AbstractFeedRetrievalService implements org_tubepress_gdata_retrieval_FeedRetrievalService
{
    private $_cache;
    
    /**
     * Fetches the RSS from YouTube
     * 
     * @param org_tubepress_options_manager_OptionsManager $tpom The TubePress options manager
     * 
     * @return DOMDocument The raw RSS from YouTube
     */
    public function fetch($url, $useCache)
    {   
        $xml = new DOMDocument();
        if ($useCache) {
            if ($this->_cache->has($url)) {
                $cached = $this->_cache->get($url);
                $xml->loadXML($cached);
            } else {
                $xml = $this->_getFromNetwork($url);
                $this->_cache->save($url, $xml->saveXML());
            }
        } else {
            $xml = $this->_getFromNetwork($url);
        }
        return $xml;
    }
    
    private function _getFromNetwork($url)
    {
        $data = $this->_fetchFromNetwork($url);

        $data = trim($data);
        
        $doc = new DOMDocument();

       if (substr($data,0,1) != "<") {
            throw new Exception("YouTube returned non-xml: " . $data);
        }
        if ($doc->loadXML($data) === FALSE) {
            throw new Exception("YouTube returned invalid XML: " . $data);
        }
        
        return $doc;
    }
    
    protected abstract function _fetchFromNetwork($request);
    
    public function setCacheService(org_tubepress_cache_CacheService $cache)
    {
        $this->_cache = $cache;
    }
}