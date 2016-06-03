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

abstract class tubepress_http_impl_AbstractHttpClient implements tubepress_api_http_HttpClientInterface
{
    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(tubepress_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_api_log_LoggerInterface            $logger)
    {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_logger          = $logger;
        $this->_shouldLog       = $logger->isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function get($url = null, $options = array())
    {
        $request = $this->createRequest('GET', $url, $options);

        return $this->send($request);
    }

    /**
     * {@inheritdoc}
     */
    public function send(tubepress_api_http_message_RequestInterface $request)
    {
        $response = $this->_getQuickResponse($request);

        if (!$response) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('About to perform actual %s', $this->_stringifyRequest($request)));
                $this->_logDebug('Request headers follow:');
                $this->_logHeaders($request);
            }

            $response = $this->doSend($request);

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('%s has completed.', $this->_stringifyRequest($request)));
            }
        }

        return $this->_dispatchResponseEvent($request, $response);
    }

    /**
     * @return tubepress_api_event_EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->_eventDispatcher;
    }

    /**
     * Sends a single request.
     *
     * @param tubepress_api_http_message_RequestInterface $request Request to send
     *
     * @return tubepress_api_http_message_ResponseInterface
     *
     * @throws LogicException                                When the underlying implementation does not populate a
     *                                                       response
     * @throws tubepress_api_http_exception_RequestException When an error is encountered
     */
    abstract protected function doSend(tubepress_api_http_message_RequestInterface $request);

    private function _dispatchResponseEvent(tubepress_api_http_message_RequestInterface  $request,
                                            tubepress_api_http_message_ResponseInterface $response)
    {
        $event = $this->_eventDispatcher->newEventInstance($response, array(
            'request' => $request,
        ));

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Dispatching <code>%s</code> event for %s',
                tubepress_api_http_Events::EVENT_HTTP_RESPONSE,
                $this->_stringifyRequest($request)
            ));
        }

        $this->_eventDispatcher->dispatch(tubepress_api_http_Events::EVENT_HTTP_RESPONSE, $event);

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Response headers from %s follow:', $this->_stringifyRequest($request)));
            $this->_logHeaders($response);
            $this->_logDebug(sprintf('Raw result for %s is in the HTML source for this page. <span style="display:none">%s</span>',
                $this->_stringifyRequest($request), htmlspecialchars($response->getBody()->toString())));
        }

        return $event->getSubject();
    }

    /**
     * @param tubepress_api_http_message_RequestInterface $request
     *
     * @return tubepress_api_http_message_ResponseInterface|null
     */
    private function _getQuickResponse(tubepress_api_http_message_RequestInterface $request)
    {
        $event = $this->_eventDispatcher->newEventInstance($request, array(
            'response' => null,
        ));

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Dispatching <code>%s</code> event for %s',
                tubepress_api_http_Events::EVENT_HTTP_REQUEST,
                $this->_stringifyRequest($request)
            ));
        }

        $this->_eventDispatcher->dispatch(tubepress_api_http_Events::EVENT_HTTP_REQUEST, $event);

        if (!$event->hasArgument('response') || $event->getArgument('response') === null) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('No listeners created a response for %s. A network request will be made instead.',
                    $this->_stringifyRequest($request)));
            }

            return null;
        }

        /**
         * @var tubepress_api_http_message_ResponseInterface
         */
        $response = $event->getArgument('response');

        if (!($response instanceof tubepress_api_http_message_ResponseInterface)) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('A listener created a non-response for %s. A network request will be made instead.',
                    $this->_stringifyRequest($request)));
            }

            return null;
        }

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('A listener created a response for %s. No network request will be made.',
                $this->_stringifyRequest($request)));
        }

        return $response;
    }

    private function _logHeaders(tubepress_api_http_message_MessageInterface $message)
    {
        $headers = $message->getHeaders();

        foreach ($headers as $name => $value) {

            if (is_array($value)) {

                $value = implode(', ', $value);
            }

            if (strcasecmp($name, 'authorization') === 0) {

                $value = '-- not shown during logging --';
            }

            $this->_logger->debug(sprintf('&nbsp;&nbsp;&nbsp;<code>%s: %s</code>', $name, $value));
        }
    }

    private function _stringifyRequest(tubepress_api_http_message_RequestInterface $request)
    {
        return sprintf('<code>%s</code> to <code>%s</code>', $request->getMethod(), $request->getUrl());
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(HTTP Client) %s', $msg));
    }
}
