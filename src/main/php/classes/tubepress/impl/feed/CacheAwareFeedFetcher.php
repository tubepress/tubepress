<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Base functionality for feed retrieval services.
 */
class tubepress_impl_feed_CacheAwareFeedFetcher implements tubepress_spi_feed_FeedFetcher
{
    /**
     * @var ehough_epilog_api_ILogger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Cache Aware Feed Fetcher');
    }

    /**
     * Fetches the feed from the remote provider
     *
     * @param string  $url      The URL to fetch.
     * @param boolean $useCache Whether or not to use the network cache.
     *
     * @return mixed The raw feed from the provider, or null if there was a problem.
     */
    public final function fetch($url, $useCache)
    {
        $result = '';
        if ($useCache) {

            $cache = tubepress_impl_patterns_sl_ServiceLocator::getCacheService();

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug(sprintf('First asking cache for <a href="%s">URL</a>', $url));
            }

            $result = $cache->get($url);

            if ($result !== false) {

                if ($this->_logger->isDebugEnabled()) {

                    $this->_logger->debug(sprintf('Cache has <a href="%s">URL</a>. Sweet.', $url));
                }

            } else {

                if ($this->_logger->isDebugEnabled()) {

                    $this->_logger->debug(sprintf('Cache does not have <a href="%s">URL</a>. We\'ll have to get it from the network.', $url));
                }

                $result = $this->_getFromNetwork($url);

                $cache->save($url, $result);
            }

        } else {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug(sprintf('Skip cache check for <a href="%s">URL</a>', $url));
            }

            $result = $this->_getFromNetwork($url);
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Raw result for <a href="%s">URL</a> is in the HTML source for this page. <span style="display:none">%s</span>',
                $url, htmlspecialchars($result)));
        }

        return $result;
    }

    private function _getFromNetwork($url)
    {
        $u               = new ehough_curly_Url($url);
        $request         = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, $u);
        $client          = tubepress_impl_patterns_sl_ServiceLocator::getHttpClient();
        $responseHandler = tubepress_impl_patterns_sl_ServiceLocator::getHttpResponseHandler();

        return $client->executeAndHandleResponse($request, $responseHandler);
    }
}
