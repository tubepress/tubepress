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
class tubepress_core_cache_impl_listeners_http_ApiCacheAfterListener extends tubepress_core_cache_impl_listeners_http_AbstractApiCacheListener
{
    protected function execute(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $httpRequest tubepress_core_http_api_message_RequestInterface
         */
        $httpRequest = $event->getArgument('request');

        /**
         * @var $httpResponse tubepress_core_http_api_message_ResponseInterface
         */
        $httpResponse = $event->getSubject();

        if ($httpResponse->hasHeader('TubePress-API-Cache-Hit')) {

            return;
        }

        $url  = $httpRequest->getUrl();
        $body = $httpResponse->getBody();

        if ($this->getLogger()->isEnabled()) {

            $this->getLogger()->debug(sprintf('Raw result for <a href="%s">URL</a> is in the HTML source for this page. <span style="display:none">%s</span>',
                $url, htmlspecialchars($body->toString())));
        }

        $result = $this->getItem($url);

        $storedSuccessfully = $result->set($body->toString(), $this->getExecutionContext()->get(tubepress_core_cache_api_Constants::LIFETIME_SECONDS));

        if (!$storedSuccessfully) {

            if ($this->getLogger()->isEnabled()) {

                $this->getLogger()->error('Unable to store data to cache');
            }
        }
    }

    /**
     * @param tubepress_core_event_api_EventInterface $event
     *
     * @return tubepress_core_http_api_message_RequestInterface
     */
    protected function getRequestFromEvent(tubepress_core_event_api_EventInterface $event)
    {
        return $event->getArgument('request');
    }
}
