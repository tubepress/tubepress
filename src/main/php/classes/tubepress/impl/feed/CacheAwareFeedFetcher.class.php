<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_api_cache_Cache',
    'org_tubepress_api_feed_FeedFetcher',
    'org_tubepress_api_http_HttpClient',
    'org_tubepress_api_http_HttpRequest',
    'org_tubepress_api_url_Url',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
));

/**
 * Base functionality for feed retrieval services.
 */
class org_tubepress_impl_feed_CacheAwareFeedFetcher implements org_tubepress_api_feed_FeedFetcher
{
    private static $_logPrefix = 'Cache Aware Feed Fetcher';

    /**
     * Fetches the feed from a remote provider
     *
     * @param string  $url      The URL to fetch.
     * @param boolean $useCache Whether or not to use the network cache.
     *
     * @return unknown The raw feed from the provider
     */
    public function fetch($url, $useCache)
    {
        global $tubepress_base_url;

        $ioc   = org_tubepress_impl_ioc_IocContainer::getInstance();
        $cache = $ioc->get(org_tubepress_api_cache_Cache::_);

        $result = '';
        if ($useCache) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'First asking cache for <a href="%s">URL</a>', $url);

            $result = $cache->get($url);

            if ($result !== false) {

                org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Cache has <a href="%s">URL</a>. Sweet.', $url);

            } else {

                org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Cache does not have <a href="%s">URL</a>. We\'ll have to get it from the network.', $url);

                $result = $this->_getFromNetwork($url, $ioc);

                $cache->save($url, $result);
            }

        } else {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Skip cache check for <a href="%s">URL</a>', $url);
            $result = $this->_getFromNetwork($url, $ioc);
        }

        org_tubepress_impl_log_Log::log(self::$_logPrefix,
        	'Raw result for <a href="%s">URL</a> is in the HTML source for this page. <span style="display:none">%s</span>',
            $url, htmlspecialchars($result));

        return $result;
    }

    private function _getFromNetwork($url, $ioc)
    {
        $u               = new org_tubepress_api_url_Url($url);
        $request         = new org_tubepress_api_http_HttpRequest(org_tubepress_api_http_HttpRequest::HTTP_METHOD_GET, $u);
        $client          = $ioc->get(org_tubepress_api_http_HttpClient::_);
        $responseHandler = $ioc->get(org_tubepress_api_http_HttpResponseHandler::_);

        return $client->executeAndHandleResponse($request, $responseHandler);
    }
}
