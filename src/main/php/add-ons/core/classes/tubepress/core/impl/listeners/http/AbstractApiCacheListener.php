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
 *
 */
abstract class tubepress_core_impl_listeners_http_AbstractApiCacheListener
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var ehough_stash_interfaces_PoolInterface
     */
    private $_apiCache;

    public function __construct(tubepress_api_log_LoggerInterface           $logger,
                                tubepress_core_api_options_ContextInterface $context,
                                ehough_stash_interfaces_PoolInterface       $apiCache)
    {
        $this->_logger   = $logger;
        $this->_context  = $context;
        $this->_apiCache = $apiCache;
    }

    public function onEvent(tubepress_core_api_event_EventInterface $event)
    {
        $cacheEnabled   = $this->_context->get(tubepress_core_api_const_options_Names::CACHE_ENABLED);
        $isDebugEnabled = $this->_logger->isEnabled();

        if (!$cacheEnabled) {

            if ($isDebugEnabled) {

                $this->_logger->debug('API cache is disabled');
            }

            return;
        }

        $request = $this->getRequestFromEvent($event);

        if (!$request->hasHeader('TubePress-Remote-API-Call')) {

            return;
        }

        $this->execute($event);
    }

    protected abstract function execute(tubepress_core_api_event_EventInterface $event);

    /**
     * @param tubepress_core_api_event_EventInterface $event
     *
     * @return tubepress_core_api_http_RequestInterface
     */
    protected abstract function getRequestFromEvent(tubepress_core_api_event_EventInterface $event);

    /**
     * @param tubepress_core_api_url_UrlInterface $url
     *
     * @return ehough_stash_interfaces_ItemInterface
     */
    protected function getItem(tubepress_core_api_url_UrlInterface $url)
    {
        $key = str_replace('/', '~', "$url");

        return $this->_apiCache->getItem($key);
    }

    /**
     * @return tubepress_core_api_options_ContextInterface
     */
    protected function getExecutionContext()
    {
        return $this->_context;
    }

    /**
     * @return ehough_stash_interfaces_PoolInterface
     */
    protected function getApiCache()
    {
        return $this->_apiCache;
    }

    /**
     * @return tubepress_api_log_LoggerInterface
     */
    protected function getLogger()
    {
        return $this->_logger;
    }
}
