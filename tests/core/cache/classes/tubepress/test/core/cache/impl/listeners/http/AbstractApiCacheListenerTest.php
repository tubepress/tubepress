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
abstract class tubepress_test_core_cache_impl_listeners_http_AbstractApiCacheListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_cache_impl_listeners_http_AbstractApiCacheListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockApiCache;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRequest;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockResponse;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrl;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBody;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCacheItem;

    public function onSetup()
    {
        $this->_mockApiCache  = $this->mock('ehough_stash_interfaces_PoolInterface');
        $this->_mockLogger    = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockContext   = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockEvent     = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_mockRequest   = $this->mock('tubepress_core_http_api_message_RequestInterface');
        $this->_mockResponse  = $this->mock('tubepress_core_http_api_message_ResponseInterface');
        $this->_mockUrl       = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrl       = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockBody      = $this->mock('tubepress_core_stream_api_StreamInterface');
        $this->_mockCacheItem = $this->mock('ehough_stash_interfaces_ItemInterface');
        $this->_sut           = $this->buildSut();
    }

    /**
     * @return tubepress_core_cache_impl_listeners_http_AbstractApiCacheListener
     */
    protected abstract function buildSut();

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected abstract function getSubject();

    public function testNonApiCall()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_cache_api_Constants::ENABLED)->andReturn(true);
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockEvent->shouldReceive('getSubject')->andReturn($this->getSubject());
        $this->_mockEvent->shouldReceive('getArgument')->atLeast(1)->with('request')->andReturn($this->_mockRequest);
        $this->_mockRequest->shouldReceive('hasHeader')->once()->with('TubePress-Remote-API-Call')->andReturn(false);

        $this->doRunTest();
    }

    public function testCacheDisabled()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_cache_api_Constants::ENABLED)->andReturn(false);
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->once()->with('API cache is disabled');

        $this->doRunTest();
    }

    protected function setupForExecution()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_cache_api_Constants::ENABLED)->andReturn(true);
        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockEvent->shouldReceive('getArgument')->atLeast(1)->with('request')->andReturn($this->_mockRequest);
        $this->_mockEvent->shouldReceive('getSubject')->andReturn($this->getSubject());
        $this->_mockRequest->shouldReceive('hasHeader')->once()->with('TubePress-Remote-API-Call')->andReturn(true);
        $this->_mockRequest->shouldReceive('getUrl')->andReturn($this->_mockUrl);
        $this->_mockUrl->shouldReceive('__toString')->andReturn('<url>');
    }

    protected function doRunTest()
    {
        $this->_sut->onEvent($this->_mockEvent);
        $this->assertTrue(true);
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockCacheItem()
    {
        return $this->_mockCacheItem;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockBody()
    {
        return $this->_mockBody;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockUrl()
    {
        return $this->_mockUrl;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockApiCache()
    {
        return $this->_mockApiCache;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockContext()
    {
        return $this->_mockContext;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockEvent()
    {
        return $this->_mockEvent;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockLogger()
    {
        return $this->_mockLogger;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockRequest()
    {
        return $this->_mockRequest;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockResponse()
    {
        return $this->_mockResponse;
    }
}