<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Pulls out info from $_GET or $_POST.
 */
abstract class tubepress_lib_impl_http_AbstractHttpClient implements tubepress_lib_api_http_HttpClientInterface
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_platform_api_log_LoggerInterface       $logger)
    {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_logger          = $logger;
        $this->_shouldLog       = $logger->isEnabled();
    }

    /**
     * Send a GET request
     *
     * @param string|tubepress_platform_api_url_UrlInterface $url     URL
     * @param array                                 $options Array of request options to apply.
     *
     * @return tubepress_lib_api_http_message_ResponseInterface
     * @throws tubepress_lib_api_http_exception_RequestException When an error is encountered
     *
     * @api
     * @since 4.0.0
     */
    public function get($url = null, $options = array())
    {
        $request = $this->createRequest('GET', $url, $options);

        return $this->send($request);
    }

    /**
     * Sends a single request
     *
     * @param tubepress_lib_api_http_message_RequestInterface $request Request to send
     *
     * @return tubepress_lib_api_http_message_ResponseInterface
     * @throws LogicException When the underlying implementation does not populate a response
     * @throws tubepress_lib_api_http_exception_RequestException When an error is encountered
     *
     * @api
     * @since 4.0.0
     */
    public function send(tubepress_lib_api_http_message_RequestInterface $request)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Executing HTTP <code>%s</code> to <code>%s</code>', $request->getMethod(), $request->getUrl()));
            $this->_logger->debug('Request headers follow:');
            $this->_logHeaders($request);
        }

        $response = $this->_getQuickResponse($request);

        if (!$response) {

            $response = $this->doSend($request);
        }

        return $this->_dispatchResponseEvent($request, $response);
    }

    /**
     * @return tubepress_lib_api_event_EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->_eventDispatcher;
    }

    /**
     * Sends a single request
     *
     * @param tubepress_lib_api_http_message_RequestInterface $request Request to send
     *
     * @return tubepress_lib_api_http_message_ResponseInterface
     * @throws LogicException When the underlying implementation does not populate a response
     * @throws tubepress_lib_api_http_exception_RequestException When an error is encountered
     */
    protected abstract function doSend(tubepress_lib_api_http_message_RequestInterface $request);

    private function _dispatchResponseEvent(tubepress_lib_api_http_message_RequestInterface  $request,
                                            tubepress_lib_api_http_message_ResponseInterface $response)
    {
        $event = $this->_eventDispatcher->newEventInstance($response, array(
            'request' => $request
        ));

        $this->_eventDispatcher->dispatch(tubepress_lib_api_http_Events::EVENT_HTTP_RESPONSE, $event);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Response headers from <code>%s</code> to <code>%s</code> follow:', $request->getMethod(), $request->getUrl()));
            $this->_logHeaders($response);
            $this->_logger->debug(sprintf('Raw result for <code>%s</code> is in the HTML source for this page. <span style="display:none">%s</span>',
                $request->getUrl(), htmlspecialchars($response->getBody()->toString())));
        }

        return $event->getSubject();
    }

    /**
     * @param tubepress_lib_api_http_message_RequestInterface $request
     *
     * @return tubepress_lib_api_http_message_ResponseInterface|null
     */
    private function _getQuickResponse(tubepress_lib_api_http_message_RequestInterface $request)
    {
        $event = $this->_eventDispatcher->newEventInstance($request, array(
            'response' => null
        ));

        $this->_eventDispatcher->dispatch(tubepress_lib_api_http_Events::EVENT_HTTP_REQUEST, $event);

        if (!$event->hasArgument('response') || $event->getArgument('response') === null) {

            return null;
        }

        /**
         * @var $response tubepress_lib_api_http_message_ResponseInterface
         */
        $response = $event->getArgument('response');

        if (!($response instanceof tubepress_lib_api_http_message_ResponseInterface)) {

            return null;
        }

        return $response;
    }

    private function _logHeaders(tubepress_lib_api_http_message_MessageInterface $message)
    {
        $headers = $message->getHeaders();

        foreach ($headers as $name => $value) {

            if (is_array($value)) {

                $value = implode(', ', $value);
            }

            $this->_logger->debug(sprintf('<code>%s: %s</code>', $name, $value));
        }
    }
}