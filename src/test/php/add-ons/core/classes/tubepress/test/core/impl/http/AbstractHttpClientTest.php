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
 * @covers tubepress_core_impl_http_AbstractHttpClient<extended>
 */
class tubepress_test_core_impl_http_AbstractHttpClientTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_core_api_event_EventDispatcherInterface::_);
    }

    public function testSlowSend()
    {
        $client = new tubepress_test_core_impl_http_AbstractHttpClientTest__noErrorClient($this->_mockEventDispatcher);
        $mockRequest = $this->mock('tubepress_core_api_http_RequestInterface');

        $mockBeforeEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockBeforeEvent->shouldReceive('hasArgument')->once()->with('response')->andReturn(true);
        $mockBeforeEvent->shouldReceive('getArgument')->once()->with('response')->andReturn(null);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockRequest, array('response' => null))->andReturn($mockBeforeEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::HTTP_REQUEST, $mockBeforeEvent);

        $mockResponse = $this->mock('tubepress_core_api_http_ResponseInterface');

        $mockAfterEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockAfterEvent->shouldReceive('getSubject')->once()->andReturn($mockResponse);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::type('tubepress_core_api_http_ResponseInterface'), array('request' => $mockRequest))->andReturn($mockAfterEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::HTTP_RESPONSE, $mockAfterEvent);

        $result = $client->send($mockRequest);

        $this->assertInstanceOf('tubepress_core_api_http_ResponseInterface', $result);
    }

    public function testQuickSend()
    {
        $client = new tubepress_test_core_impl_http_AbstractHttpClientTest__noErrorClient($this->_mockEventDispatcher);
        $mockRequest = $this->mock('tubepress_core_api_http_RequestInterface');

        $mockResponse = $this->mock('tubepress_core_api_http_ResponseInterface');

        $mockBeforeEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockBeforeEvent->shouldReceive('hasArgument')->once()->with('response')->andReturn(true);
        $mockBeforeEvent->shouldReceive('getArgument')->twice()->with('response')->andReturn($mockResponse);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockRequest, array('response' => null))->andReturn($mockBeforeEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::HTTP_REQUEST, $mockBeforeEvent);

        $mockAfterEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockAfterEvent->shouldReceive('getSubject')->once()->andReturn($mockResponse);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockResponse, array('request' => $mockRequest))->andReturn($mockAfterEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::HTTP_RESPONSE, $mockAfterEvent);

        $result = $client->send($mockRequest);

        $this->assertSame($mockResponse, $result);
    }
}

if (!class_exists('tubepress_test_core_impl_http_AbstractHttpClientTest__client')) {

    class tubepress_test_core_impl_http_AbstractHttpClientTest__noErrorClient extends tubepress_core_impl_http_AbstractHttpClient
    {
        /**
         * Sends a single request
         *
         * @param tubepress_core_api_http_RequestInterface $request Request to send
         *
         * @return tubepress_core_api_http_ResponseInterface
         * @throws LogicException When the underlying implementation does not populate a response
         * @throws tubepress_core_api_http_RequestException When an error is encountered
         */
        protected function doSend(tubepress_core_api_http_RequestInterface $request)
        {
            return ehough_mockery_Mockery::mock('tubepress_core_api_http_ResponseInterface');
        }

        public function createRequest($method, $url = null, array $options = array())
        {
            return ehough_mockery_Mockery::mock('tubepress_core_api_http_RequestInterface');
        }

        public function getDefaultOption($keyOrPath = null) {}
        public function setDefaultOption($keyOrPath, $value) {}
    }
}