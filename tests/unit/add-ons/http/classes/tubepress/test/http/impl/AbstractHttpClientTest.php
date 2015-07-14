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
 * @covers tubepress_http_impl_AbstractHttpClient<extended>
 */
class tubepress_test_http_impl_AbstractHttpClientest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockLogger          = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
    }

    public function testSlowSend()
    {
        $mockResponse    = $this->mock('tubepress_api_http_message_ResponseInterface');
        $mockRequest     = $this->mock('tubepress_api_http_message_RequestInterface');
        $mockUrl         = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockAfterEvent  = $this->mock('tubepress_api_event_EventInterface');
        $mockBeforeEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockBodyStream  = $this->mock('tubepress_api_streams_StreamInterface');

        $client = new tubepress_test_lib_http_impl_AbstractHttpClientTest__noErrorClient($this->_mockEventDispatcher, $this->_mockLogger, $mockResponse);

        $mockRequest->shouldReceive('getMethod')->twice()->andReturn('SOME METHOD');
        $mockRequest->shouldReceive('getUrl')->times(3)->andReturn($mockUrl);
        $mockRequest->shouldReceive('getHeaders')->once()->andReturn(array('foo' => array('bar')));

        $mockUrl->shouldReceive('__toString')->times(3)->andReturn('url as string');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockRequest, array('response' => null))->andReturn($mockBeforeEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_http_Events::EVENT_HTTP_REQUEST, $mockBeforeEvent);

        $mockBeforeEvent->shouldReceive('hasArgument')->once()->with('response')->andReturn(true);
        $mockBeforeEvent->shouldReceive('getArgument')->once()->with('response')->andReturn(null);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockResponse, array('request' => $mockRequest))->andReturn($mockAfterEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_http_Events::EVENT_HTTP_RESPONSE, $mockAfterEvent);

        $mockResponse->shouldReceive('getHeaders')->once()->andReturn(array('bar' => array('xx')));
        $mockResponse->shouldReceive('getBody')->once()->andReturn($mockBodyStream);

        $mockBodyStream->shouldReceive('toString')->once()->andReturn('bla bla bl');

        $mockAfterEvent->shouldReceive('getSubject')->once()->andReturn($mockResponse);

        $result = $client->send($mockRequest);

        $this->assertSame($mockResponse, $result);
    }

    public function testQuickSend()
    {
        $mockResponse    = $this->mock('tubepress_api_http_message_ResponseInterface');
        $mockRequest     = $this->mock('tubepress_api_http_message_RequestInterface');
        $mockUrl         = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockAfterEvent  = $this->mock('tubepress_api_event_EventInterface');
        $mockBeforeEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockBodyStream  = $this->mock('tubepress_api_streams_StreamInterface');

        $client = new tubepress_test_lib_http_impl_AbstractHttpClientTest__noErrorClient($this->_mockEventDispatcher, $this->_mockLogger, $mockResponse);

        $mockRequest->shouldReceive('getMethod')->twice()->andReturn('SOME METHOD');
        $mockRequest->shouldReceive('getUrl')->times(3)->andReturn($mockUrl);
        $mockRequest->shouldReceive('getHeaders')->once()->andReturn(array('foo' => array('bar')));

        $mockUrl->shouldReceive('__toString')->times(3)->andReturn('url as string');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockRequest, array('response' => null))->andReturn($mockBeforeEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_http_Events::EVENT_HTTP_REQUEST, $mockBeforeEvent);

        $mockBeforeEvent->shouldReceive('hasArgument')->once()->with('response')->andReturn(true);
        $mockBeforeEvent->shouldReceive('getArgument')->twice()->with('response')->andReturn($mockResponse);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockResponse, array('request' => $mockRequest))->andReturn($mockAfterEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_http_Events::EVENT_HTTP_RESPONSE, $mockAfterEvent);

        $mockResponse->shouldReceive('getHeaders')->once()->andReturn(array('bar' => array('xx')));
        $mockResponse->shouldReceive('getBody')->once()->andReturn($mockBodyStream);

        $mockBodyStream->shouldReceive('toString')->once()->andReturn('bla bla bl');

        $mockAfterEvent->shouldReceive('getSubject')->once()->andReturn($mockResponse);

        $result = $client->send($mockRequest);

        $this->assertSame($mockResponse, $result);
    }
}

if (!class_exists('tubepress_test_lib_impl_http_AbstractHttpClientTest__client')) {

    class tubepress_test_lib_http_impl_AbstractHttpClientTest__noErrorClient extends tubepress_http_impl_AbstractHttpClient
    {
        private $_response;

        public function __construct(ehough_mockery_mockery_MockInterface $eventDispatcher,
                                    ehough_mockery_mockery_MockInterface $logger,
                                    ehough_mockery_mockery_MockInterface $mockResponse)
        {
            parent::__construct($eventDispatcher, $logger, $mockResponse);

            $this->_response = $mockResponse;
        }

        /**
         * Sends a single request
         *
         * @param tubepress_api_http_message_RequestInterface $request Request to send
         *
         * @return tubepress_api_http_message_ResponseInterface
         * @throws LogicException When the underlying implementation does not populate a response
         * @throws tubepress_api_http_exception_RequestException When an error is encountered
         */
        protected function doSend(tubepress_api_http_message_RequestInterface $request)
        {
            return $this->_response;
        }

        public function createRequest($method, $url = null, array $options = array())
        {
            return ehough_mockery_Mockery::mock('tubepress_api_http_message_RequestInterface');
        }

        public function getDefaultOption($keyOrPath = null) {}
        public function setDefaultOption($keyOrPath, $value) {}
    }
}