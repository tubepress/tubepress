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
class tubepress_core_cache_impl_listeners_http_ApiCacheBeforeListener extends tubepress_core_cache_impl_listeners_http_AbstractApiCacheListener
{
    public function execute(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $httpRequest tubepress_core_http_api_message_RequestInterface
         */
        $httpRequest = $event->getSubject();
        $url         = $httpRequest->getUrl();
        $item        = $this->_getItem($url);

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

    /**
     * @param tubepress_core_url_api_UrlInterface $url
     *
     * @return ehough_stash_interfaces_ItemInterface
     */
    private function _getItem(tubepress_core_url_api_UrlInterface $url)
    {
        $isDebugEnabled = $this->getLogger()->isEnabled();

        if ($isDebugEnabled) {

            $this->getLogger()->debug(sprintf('Asking cache for <a href="%s">URL</a>', $url));
        }

        /**
         * @var $result ehough_stash_interfaces_ItemInterface
         */
        $result = $this->getItem($url);

        if ($isDebugEnabled) {

            if ($result->isMiss()) {

                $this->getLogger()->debug(sprintf('Cache miss for <a href="%s">URL</a>.', $url));

            } else {

                $this->getLogger()->debug(sprintf('Cache hit for <a href="%s">URL</a>.', $url));

                $this->getLogger()->debug(sprintf('Cached result for <a href="%s">URL</a> is in the HTML source for this page. <span style="display:none">%s</span>',
                    $url, htmlspecialchars($result->get())));
            }
        }

        return $result;
    }

    /**
     * @param tubepress_core_event_api_EventInterface $event
     *
     * @return tubepress_core_http_api_message_RequestInterface
     */
    protected function getRequestFromEvent(tubepress_core_event_api_EventInterface $event)
    {
        return $event->getSubject();
    }
}
