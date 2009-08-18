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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_gdata_retrieval_FeedRetrievalService',
    'org_tubepress_cache_CacheService',
    'org_tubepress_log_Log'));

/**
 * Base functionality for TubePressFeedRetrieval services
 *
 */
abstract class org_tubepress_gdata_retrieval_AbstractFeedRetrievalService implements org_tubepress_gdata_retrieval_FeedRetrievalService
{
    private $_cache;
    protected $_log;
    protected $_logPrefix;
    
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
            
            $this->_log->log($this->_logPrefix, sprintf("First asking cache for %s", $url));
            
            if ($this->_cache->has($url)) {
                
                $this->_log->log($this->_logPrefix, sprintf("Cache has %s. Sweet.", $url));
                
                $cached = $this->_cache->get($url);
                $xml->loadXML($cached);
            } else {
                
                $this->_log->log($this->_logPrefix, sprintf("Cache does not have %s. We'll have to"
                	. " get it from the network.", $url));
                
                $xml = $this->_getFromNetwork($url);
                $this->_cache->save($url, $xml->saveXML());
            }
        } else {
            $this->_log->log($this->_logPrefix, sprintf("Skip cache check for %s", $url));
            $xml = $this->_getFromNetwork($url);
        }
        return $xml;
    }
    
    public function setCacheService(org_tubepress_cache_CacheService $cache)
    {
        $this->_cache = $cache;
    }
    
    public function setLog(org_tubepress_log_Log $log)
    {
        $this->_log = $log;
    }
    
    private function _getFromNetwork($url)
    {
        $data = $this->_fetchFromNetwork($url);

        /* trim it just in case */
        $data = trim($data);
        
        $doc = new DOMDocument();

        if ($doc->loadXML($data) === FALSE) {
            throw new Exception($data);
        }
        
        return $doc;
    }
    
    protected abstract function _fetchFromNetwork($request);
}