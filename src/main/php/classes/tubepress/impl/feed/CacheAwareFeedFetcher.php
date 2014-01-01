<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
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
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Cache Aware Feed Fetcher');
    }

    /**
     * Fetches the feed from the remote provider
     *
     * @param string  $url      The URL to fetch.
     *
     * @return mixed The raw feed from the provider, or null if there was a problem.
     */
    public final function fetch($url)
    {
        $context        = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $cacheEnabled   = $context->get(tubepress_api_const_options_names_Cache::CACHE_ENABLED);
        $isDebugEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($cacheEnabled) {

            $data = $this->_getFromCache($url, $context, $isDebugEnabled);

        } else {

            if ($isDebugEnabled) {

                $this->_logger->debug(sprintf('Skip cache check for <a href="%s">URL</a>', $url));
            }

            $data = $this->_getFromNetwork($url);
        }

        if ($isDebugEnabled) {

            $this->_logger->debug(sprintf('Raw result for <a href="%s">URL</a> is in the HTML source for this page. <span style="display:none">%s</span>',
                $url, htmlspecialchars($data)));
        }

        return $data;
    }

    private function _getFromNetwork($url)
    {
        $u               = new ehough_curly_Url($url);
        $request         = new ehough_shortstop_api_HttpRequest(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET, $u);
        $client          = tubepress_impl_patterns_sl_ServiceLocator::getHttpClient();
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $response = $client->execute($request);

        $event = new tubepress_spi_event_EventBase($response->getEntity()->getContent(), array(

            'request'  => $request,
            'response' => $response
        ));
        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::HTTP_RESPONSE, $event);

        return $event->getSubject();
    }

    private function _getFromCache($url, tubepress_spi_context_ExecutionContext $context, $isDebugEnabled)
    {
        /**
         * @var $cache ehough_stash_interfaces_PoolInterface
         */
        $cache = tubepress_impl_patterns_sl_ServiceLocator::getCacheService();

        if ($isDebugEnabled) {

            $this->_logger->debug(sprintf('First asking cache for <a href="%s">URL</a>', $url));
        }

        $cacheKey = $this->_urlToCacheKey($url);
        $result   = $cache->getItem($cacheKey);

        if ($result && !$result->isMiss()) {

            if ($isDebugEnabled) {

                $this->_logger->debug(sprintf('Cache has <a href="%s">URL</a>. Sweet.', $url));
            }

        } else {

            if ($isDebugEnabled) {

                $this->_logger->debug(sprintf('Cache does not have <a href="%s">URL</a>. We\'ll have to get it from the network.', $url));
            }

            $data = $this->_getFromNetwork($url);

            $storedSuccessfully = $result->set($data, $context->get(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS));

            if (!$storedSuccessfully) {

                if ($isDebugEnabled) {

                    $this->_logger->debug('Unable to store data to cache');
                }

                return $data;
            }
        }

        return $result->get();
    }

    private function _urlToCacheKey($url)
    {
        return str_replace('/', '~', $url);
    }
}
