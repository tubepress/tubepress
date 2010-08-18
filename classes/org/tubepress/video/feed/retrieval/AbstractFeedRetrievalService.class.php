<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes') || require dirname(__FILE__) . '/../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_video_feed_retrieval_FeedRetrievalService',
    'org_tubepress_cache_CacheService',
    'org_tubepress_log_Log',
    'org_tubepress_ioc_IocContainer'));

/**
 * Base functionality for feed retrieval services.
 */
abstract class org_tubepress_video_feed_retrieval_AbstractFeedRetrievalService implements org_tubepress_video_feed_retrieval_FeedRetrievalService
{
    /**
     * Fetches the feed from the remote provider
     *
     * @param org_tubepress_ioc_IocService $ioc      The IOC container.
     * @param string                       $url      The URL to fetch.
     * @param boolean                      $useCache Whether or not to use the network cache.
     *
     * @return unknown The raw feed from the provider
     */
    public function fetch($url, $useCache)
    {
        global $tubepress_base_url;

        $logPrefix = $this->getLogPrefix();
        $ioc       = org_tubepress_ioc_IocContainer::getInstance();
        $cache     = $ioc->get('org_tubepress_cache_CacheService');
        $testUrl   = "$tubepress_base_url/classes/org/tubepress/video/feed/retrieval/ConnectionTest.php";

        org_tubepress_log_Log::log($logPrefix, 'Connection test can be run at <tt><a href="%s">%s</a></tt>', $testUrl, $testUrl);

        $result = '';
        if ($useCache) {

            org_tubepress_log_Log::log($logPrefix, 'First asking cache for <tt>%s</tt>', $url);

            if ($cache->has($url)) {
                org_tubepress_log_Log::log($logPrefix, 'Cache has <tt>%s</tt>. Sweet.', $url);
                $result = $cache->get($url);
            } else {
                org_tubepress_log_Log::log($logPrefix, 'Cache does not have <tt>%s</tt>. We\'ll have to get it from the network.', $url);
                $result = $this->_getFromNetwork($url);
                $cache->save($url, $result);
            }
        } else {
            org_tubepress_log_Log::log($logPrefix, 'Skip cache check for <tt>%s</tt>', $url);
            $result = $this->_getFromNetwork($url);
        }
        return $result;
    }

    private function _getFromNetwork($url)
    {
        $data = $this->fetchFromNetwork($url);

        /* trim it just in case */
        $data = trim($data);

        return $data;
    }

    /**
     * Retrieve the data from the network.
     *
     * @param string $url The URL to fetch from.
     *
     * @return unknown The network data.
     */
    protected abstract function fetchFromNetwork($url);

    /**
     * Get the logging prefix.
     *
     * @return string The logging prefix.
     */
    protected abstract function getLogPrefix();
}
