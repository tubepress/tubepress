<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_cache_api_impl_listeners_ApiCacheListener
{
    /**
     * @var string
     */
    const HTTP_HEADER_CACHE_HIT = 'TubePress-API-Cache-Hit';

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var \Stash\Interfaces\PoolInterface
     */
    private $_apiCache;

    public function __construct(tubepress_api_log_LoggerInterface      $logger,
                                tubepress_api_options_ContextInterface $context,
                                \Stash\Interfaces\PoolInterface        $apiCache)
    {
        $this->_logger   = $logger;
        $this->_context  = $context;
        $this->_apiCache = $apiCache;
    }

    public function onRequest(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var tubepress_api_http_message_RequestInterface
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

        $response = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(

            200,
            array(self::HTTP_HEADER_CACHE_HIT => 'true'),
            puzzle_stream_Stream::factory($item->get())
        ));

        $event->setArgument('response', $response);
        $event->stopPropagation();
    }

    public function onResponse(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var tubepress_api_http_message_RequestInterface
         */
        $httpRequest = $event->getArgument('request');

        if (!$this->_shouldExecute($httpRequest)) {

            return;
        }

        /*
         * @var tubepress_api_http_message_ResponseInterface
         */
        $httpResponse = $event->getSubject();

        if ($httpResponse->hasHeader(self::HTTP_HEADER_CACHE_HIT)) {

            return;
        }

        $url    = $httpRequest->getUrl();
        $body   = $httpResponse->getBody();
        $result = $this->_getItem($url);

        $this->_possiblyClearCache();

        $storedSuccessfully = $result->set($body->toString(), intval($this->_context->get(tubepress_api_options_Names::CACHE_LIFETIME_SECONDS)));

        if (!$storedSuccessfully) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->error('Unable to store data to cache');
            }
        }
    }

    private function _possiblyClearCache()
    {
        $cleaningFactor = $this->_context->get(tubepress_api_options_Names::CACHE_CLEANING_FACTOR);
        $cleaningFactor = intval($cleaningFactor);

        /*
         * Handle cleaning factor.
         */
        if ($cleaningFactor > 0 && rand(1, $cleaningFactor) === 1) {

            $this->_apiCache->flush();
        }
    }

    /**
     * @param tubepress_api_url_UrlInterface $url
     *
     * @return \Stash\Interfaces\ItemInterface
     */
    private function _getItem(tubepress_api_url_UrlInterface $url)
    {
        $key = str_replace('/', '~', "$url");

        return $this->_apiCache->getItem($key);
    }

    private function _shouldExecute(tubepress_api_http_message_RequestInterface $request)
    {
        $cacheEnabled   = $this->_context->get(tubepress_api_options_Names::CACHE_ENABLED);
        $isDebugEnabled = $this->_logger->isEnabled();

        if ($isDebugEnabled && !$cacheEnabled) {

            $this->_logDebug('Skip API cache for debugging.');

            return false;
        }

        $config = $request->getConfig();

        if (!isset($config['tubepress-remote-api-call'])) {

            return false;
        }

        return true;
    }

    /**
     * @param tubepress_api_url_UrlInterface $url
     *
     * @return \Stash\Interfaces\ItemInterface
     */
    private function _getCachedItem(tubepress_api_url_UrlInterface $url)
    {
        $isDebugEnabled = $this->_logger->isEnabled();

        if ($isDebugEnabled) {

            $this->_logDebug(sprintf('Asking cache for <code>%s</code>', $url));
        }

        /*
         * @var \Stash\Interfaces\ItemInterface
         */
        $result = $this->_getItem($url);

        if ($isDebugEnabled) {

            if ($result->isMiss()) {

                $this->_logDebug(sprintf('Cache miss for <code>%s</code>.', $url));

            } else {

                $this->_logDebug(sprintf('Cache hit for <code>%s</code>.', $url));
            }
        }

        return $result;
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(API Cache Listener) %s', $msg));
    }
}
