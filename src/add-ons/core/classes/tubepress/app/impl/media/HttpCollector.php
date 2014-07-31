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
 */
class tubepress_app_impl_media_HttpCollector implements tubepress_app_api_media_HttpCollectorInterface
{
    /**
     * @var tubepress_lib_api_http_HttpClientInterface
     */
    private $_httpClient;

    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_platform_api_log_LoggerInterface        $logger,
                                tubepress_lib_api_event_EventDispatcherInterface  $eventDispatcher,
                                tubepress_lib_api_http_HttpClientInterface        $httpClient)
    {
        $this->_logger          = $logger;
        $this->_shouldLog       = $logger->isEnabled();
        $this->_httpClient      = $httpClient;
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function collectSingle($itemId, tubepress_app_api_media_HttpFeedHandlerInterface $feedHandler)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Fetching media item with ID <code>%s</code>', $itemId));
        }

        $mediaItemUrl = $feedHandler->buildUrlForItem($itemId);
        $mediaItemUrl = $this->_dispatchUrl($mediaItemUrl, tubepress_app_api_event_Events::MEDIA_ITEM_URL, array(
            'itemId' => $itemId
        ));

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('URL to fetch is <a href="%s">this</a>', $mediaItemUrl));
        }

        $this->_fetchFeedAndPrepareForAnalysis($mediaItemUrl, $feedHandler);

        $mediaItemArray = $this->_feedToMediaItemArray($feedHandler);
        $toReturn       = null;

        if (count($mediaItemArray) > 0) {

            return $mediaItemArray[0];
        }

        return null;
    }

    public function collectPage($currentPage, tubepress_app_api_media_HttpFeedHandlerInterface $feedHandler)
    {
        $toReturn = new tubepress_app_api_media_MediaPage();

        $url = $feedHandler->buildUrlForPage($currentPage);
        $url = $this->_dispatchUrl($url, tubepress_app_api_event_Events::MEDIA_PAGE_URL, array(
            'pageNumber' => $currentPage
        ));

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('URL to fetch is <code>%s</code>', $url));
        }

        $this->_fetchFeedAndPrepareForAnalysis($url, $feedHandler);

        $reportedTotalResultCount = $feedHandler->getTotalResultCount();

        /**
         * If no results, we can shortcut things here.
         */
        if ($reportedTotalResultCount < 1) {

            $feedHandler->onAnalysisComplete();
            return $this->_emptyPage($toReturn);
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Reported total result count is %d video(s)', $reportedTotalResultCount));
        }

        /* convert the feed to videos */
        $mediaItemArray = $this->_feedToMediaItemArray($feedHandler);

        if (count($mediaItemArray) == 0) {

            return $this->_emptyPage($toReturn);
        }

        $toReturn->setTotalResultCount($reportedTotalResultCount);
        $toReturn->setItems($mediaItemArray);

        return $toReturn;
    }

    private function _feedToMediaItemArray(tubepress_app_api_media_HttpFeedHandlerInterface $feedHandler)
    {
        $toReturn = array();
        $total    = $feedHandler->getCurrentResultCount();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Now attempting to build %d item(s) from raw feed', $total));
        }

        for ($index = 0; $index < $total; $index++) {

            $failureMessage = $feedHandler->getReasonUnableToUseItemAtIndex($index);

            if ($failureMessage !== null) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('Skipping item at index %d: %s', $index,
                        $failureMessage));
                }

                continue;
            }

            $mediaItemId = $feedHandler->getIdForItemAtIndex($index);
            $mediaItem   = new tubepress_app_api_media_MediaItem($mediaItemId);
            $eventArgs   = $feedHandler->getNewItemEventArguments($mediaItem, $index);
            $itemEvent   = $this->_eventDispatcher->newEventInstance($mediaItem, $eventArgs);

            $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::MEDIA_ITEM_HTTP_NEW, $itemEvent);

            array_push($toReturn, $itemEvent->getSubject());
        }

        $feedHandler->onAnalysisComplete();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Built %d items(s) from raw feed', sizeof($toReturn)));
        }

        return $toReturn;
    }

    private function _fetchFeedAndPrepareForAnalysis($url, tubepress_app_api_media_HttpFeedHandlerInterface $feedHandler)
    {
        $debugStream = null;
        $requestOpts = array();

        if ($this->_shouldLog) {

            $debugStream          = fopen('php://memory','r+');
            $requestOpts['debug'] = $debugStream;
        }

        try {

            $httpRequest = $this->_httpClient->createRequest('GET', $url, $requestOpts);

            /**
             * Allows the cache to recognize this as an API call.
             */
            $httpRequest->setConfig(array_merge($httpRequest->getConfig(), array('tubepress-remote-api-call' => true)));

            $httpResponse = $this->_httpClient->send($httpRequest);

        } catch (tubepress_lib_api_http_exception_RequestException $e) {

            $this->_flushDebugStream($debugStream, true);

            throw $e;
        }

        $this->_flushDebugStream($debugStream, false);

        $rawFeed = $httpResponse->getBody()->toString();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Raw result for <a href="%s">URL</a> is in the HTML source for this page. <span style="display:none">%s</span>',
                $url, htmlspecialchars($rawFeed)));
        }

        $feedHandler->onAnalysisStart($rawFeed);

        return $rawFeed;
    }

    private function _flushDebugStream($stream, $error)
    {
        if (!$stream || !$this->_shouldLog) {

            return;
        }

        rewind($stream);

        $contents = stream_get_contents($stream);
        $contents = explode("\n", $contents);

        foreach ($contents as $line) {

            if ($error) {

                $this->_logger->error($line);
                continue;
            }

            $this->_logger->debug($line);
        }
    }

    private function _emptyPage(tubepress_app_api_media_MediaPage $page)
    {
        $page->setTotalResultCount(0);
        $page->setItems(array());

        return $page;
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface
     */
    private function _dispatchUrl(tubepress_platform_api_url_UrlInterface $url, $eventName, array $additionalArgs = array())
    {
        $args = array_merge(array(
            'provider' => $this
        ), $additionalArgs);

        $event = $this->_eventDispatcher->newEventInstance($url, $args);

        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }
}