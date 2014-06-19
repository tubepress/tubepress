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

class tubepress_core_cache_impl_listeners_http_ApiCacheListener
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var ehough_stash_interfaces_PoolInterface
     */
    private $_apiCache;

    public function __construct(tubepress_api_log_LoggerInterface           $logger,
                                tubepress_core_options_api_ContextInterface $context,
                                ehough_stash_interfaces_PoolInterface       $apiCache)
    {
        $this->_logger   = $logger;
        $this->_context  = $context;
        $this->_apiCache = $apiCache;
    }

    public function onRequest(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $httpRequest tubepress_core_http_api_message_RequestInterface
         */
        $httpRequest = $event->getSubject();
        
        if (!$this->_shouldExecute($httpRequest)) {
            
            return;
        }
        
        $url  = $httpRequest->getUrl();
        $item = $this->_getCachedItem($url);

        if ($item->isMiss()) {

            return;
        }

        $response = new tubepress_core_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(

            200,
            array('TubePress-API-Cache-Hit' => 'true'),
            puzzle_stream_Stream::factory($item->get())
        ));

        $event->setArgument('response', $response);
        $event->stopPropagation();
    }

    public function onResponse(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $httpRequest tubepress_core_http_api_message_RequestInterface
         */
        $httpRequest = $event->getArgument('request');

        if (!$this->_shouldExecute($httpRequest)) {

            return;
        }

        /**
         * @var $httpResponse tubepress_core_http_api_message_ResponseInterface
         */
        $httpResponse = $event->getSubject();

        if ($httpResponse->hasHeader('TubePress-API-Cache-Hit')) {

            return;
        }

        $url    = $httpRequest->getUrl();
        $body   = $httpResponse->getBody();
        $result = $this->_getItem($url);

        $this->_possiblyClearCache();

        $storedSuccessfully = $result->set($body->toString(), intval($this->_context->get(tubepress_core_cache_api_Constants::LIFETIME_SECONDS)));

        if (!$storedSuccessfully) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->error('Unable to store data to cache');
            }
        }
    }

    private function _possiblyClearCache()
    {
        $cleaningFactor = $this->_context->get(tubepress_core_cache_api_Constants::CLEANING_FACTOR);
        $cleaningFactor = intval($cleaningFactor);

        /**
         * Handle cleaning factor.
         */
        if ($cleaningFactor > 0 && rand(1, $cleaningFactor) === 1) {

            $this->_apiCache->flush();
        }
    }

    /**
     * @param tubepress_core_url_api_UrlInterface $url
     *
     * @return ehough_stash_interfaces_ItemInterface
     */
    private function _getItem(tubepress_core_url_api_UrlInterface $url)
    {
        $key = str_replace('/', '~', "$url");

        return $this->_apiCache->getItem($key);
    }

    private function _shouldExecute(tubepress_core_http_api_message_RequestInterface $request)
    {
        $cacheEnabled   = $this->_context->get(tubepress_core_cache_api_Constants::ENABLED);
        $isDebugEnabled = $this->_logger->isEnabled();

        if (!$cacheEnabled) {

            if ($isDebugEnabled) {

                $this->_logger->debug('API cache is disabled');
            }

            return false;
        }

        $config = $request->getConfig();

        if (!isset($config['tubepress-remote-api-call'])) {

            return false;
        }

        return true;
    }

    /**
     * @param tubepress_core_url_api_UrlInterface $url
     *
     * @return ehough_stash_interfaces_ItemInterface
     */
    private function _getCachedItem(tubepress_core_url_api_UrlInterface $url)
    {
        $isDebugEnabled = $this->_logger->isEnabled();

        if ($isDebugEnabled) {

            $this->_logger->debug(sprintf('Asking cache for <a href="%s">URL</a>', $url));
        }

        /**
         * @var $result ehough_stash_interfaces_ItemInterface
         */
        $result = $this->_getItem($url);

        if ($isDebugEnabled) {

            if ($result->isMiss()) {

                $this->_logger->debug(sprintf('Cache miss for <a href="%s">URL</a>.', $url));

            } else {

                $this->_logger->debug(sprintf('Cache hit for <a href="%s">URL</a>.', $url));
            }
        }

        return $result;
    }
}
