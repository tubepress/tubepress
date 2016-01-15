<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_http_impl_puzzle_PuzzleHttpClient extends tubepress_http_impl_AbstractHttpClient implements puzzle_event_SubscriberInterface
{
    /**
     * @var puzzle_ClientInterface
     */
    private $_delegate;

    public function __construct(tubepress_api_event_EventDispatcherInterface $eventDispatcher,
                                puzzle_Client                                $delegate,
                                tubepress_api_log_LoggerInterface            $logger)
    {
        parent::__construct($eventDispatcher, $logger);

        if (!function_exists('puzzle_request')) {

            require TUBEPRESS_ROOT . '/vendor/puzzlehttp/puzzle/src/main/php/puzzle/functions.php';
        }

        if (!function_exists('puzzle_stream_create')) {

            require TUBEPRESS_ROOT . '/vendor/puzzlehttp/streams/src/main/php/puzzle/stream/functions.php';
        }

        $this->_delegate = $delegate;
        $this->_delegate->setDefaultOption('verify', TUBEPRESS_ROOT . '/vendor/puzzlehttp/puzzle/src/main/php/puzzle/cacert.pem');
        $delegate->getEmitter()->attach($this);
    }

    /**
     * Create and return a new {@see tubepress_api_http_message_RequestInterface} object.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well.
     *
     * @param string                                      $method  HTTP method
     * @param string|array|tubepress_api_url_UrlInterface $url     URL
     * @param array                                       $options Array of request options to apply.
     *
     * @return tubepress_api_http_message_RequestInterface
     *
     * @api
     * @since 4.0.0
     */
    public function createRequest($method, $url = null, array $options = array())
    {
        $puzzleRequest = $this->_delegate->createRequest($method, "$url", $options);

        return new tubepress_http_impl_puzzle_PuzzleBasedRequest($puzzleRequest);
    }

    /**
     * Get default request options of the client.
     *
     * @param string|null $keyOrPath The Path to a particular default request
     *     option to retrieve or pass null to retrieve all default request
     *     options. The syntax uses "/" to denote a path through nested PHP
     *     arrays. For example, "headers/content-type".
     *
     * @return mixed
     *
     * @api
     * @since 4.0.0
     */
    public function getDefaultOption($keyOrPath = null)
    {
        return $this->_delegate->getDefaultOption($keyOrPath);
    }

    /**
     * Sends a single request
     *
     * @param tubepress_api_http_message_RequestInterface $request Request to send
     *
     * @return tubepress_api_http_message_ResponseInterface
     * @throws LogicException When the underlying adapter does not populate a response
     * @throws tubepress_api_http_exception_RequestException When an error is encountered
     */
    protected function doSend(tubepress_api_http_message_RequestInterface $request)
    {
        $tubePressBody = $request->getBody();
        $puzzleBody    = null;

        if ($tubePressBody) {

            if ($tubePressBody instanceof tubepress_http_impl_puzzle_streams_PuzzleBasedStream) {

                $puzzleBody = $tubePressBody->getUnderlyingPuzzleStream();

            } else {

                $puzzleBody = new tubepress_http_impl_puzzle_streams_PuzzleStream($tubePressBody);
            }
        }

        $requestConfig            = $request->getConfig();
        $requestConfig['emitter'] = $this->_delegate->getEmitter();

        $puzzleRequest = new puzzle_message_Request(

            $request->getMethod(),
            $request->getUrl()->toString(),
            $request->getHeaders(),
            $puzzleBody,
            $requestConfig
        );

        $puzzleResponse = null;

        try {

            $puzzleResponse = $this->_delegate->send($puzzleRequest);

        } catch (puzzle_exception_RequestException $e) {

            throw new tubepress_http_impl_puzzle_RequestException($e);
        }

        return new tubepress_http_impl_puzzle_PuzzleBasedResponse($puzzleResponse);
    }

    /**
     * Set a default request option on the client so that any request created
     * by the client will use the provided default value unless overridden
     * explicitly when creating a request.
     *
     * @param string|null $keyOrPath The Path to a particular configuration
     *     value to set. The syntax uses a path notation that allows you to
     *     specify nested configuration values (e.g., 'headers/content-type').
     * @param mixed $value Default request option value to set
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function setDefaultOption($keyOrPath, $value)
    {
        $this->_delegate->setDefaultOption($keyOrPath, $value);
    }

    public function onHeaders(puzzle_event_HeadersEvent $headersEvent)
    {
        $puzzleRequest     = $headersEvent->getRequest();
        $puzzleResponse    = $headersEvent->getResponse();
        $tubePressRequest  = new tubepress_http_impl_puzzle_PuzzleBasedRequest($puzzleRequest);
        $tubePressResponse = new tubepress_http_impl_puzzle_PuzzleBasedResponse($puzzleResponse);
        $eventDispatcher   = $this->getEventDispatcher();

        $event = $eventDispatcher->newEventInstance($tubePressResponse, array(
            'request' => $tubePressRequest
        ));

        $eventDispatcher->dispatch(tubepress_api_http_Events::EVENT_HTTP_RESPONSE_HEADERS, $event);
    }

    public function onError(puzzle_event_ErrorEvent $errorEvent)
    {
        $puzzleException    = $errorEvent->getException();
        $tubePressException = new tubepress_http_impl_puzzle_RequestException($puzzleException);
        $eventDispatcher    = $this->getEventDispatcher();

        $event = $eventDispatcher->newEventInstance($tubePressException, array(

            'response' => null
        ));

        $eventDispatcher->dispatch(tubepress_api_http_Events::EVENT_HTTP_ERROR, $event);

        $response = $event->getArgument('response');

        if ($response && $response instanceof tubepress_api_http_message_ResponseInterface) {

            $puzzleResponse = new puzzle_message_Response(

                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()
            );

            $errorEvent->intercept($puzzleResponse);
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The returned array keys MUST map to an event name. Each array value
     * MUST be an array in which the first element is the name of a function
     * on the EventSubscriber. The second element in the array is optional, and
     * if specified, designates the event priority.
     *
     * For example:
     *
     *  - ['eventName' => ['methodName']]
     *  - ['eventName' => ['methodName', $priority]]
     *
     * @return array
     */
    public function getEvents()
    {
        return array(

            'headers' => array('onHeaders'),
            'error'   => array('onError')
        );
    }

    /**
     * THIS IS HERE FOR TESTING ONLY.
     */
    public function ___doSend(tubepress_api_http_message_RequestInterface $request)
    {
        return $this->doSend($request);
    }
}