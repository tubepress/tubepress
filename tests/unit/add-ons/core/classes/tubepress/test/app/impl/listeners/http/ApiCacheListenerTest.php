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
 * @covers tubepress_app_impl_listeners_http_ApiCacheListener
 */
class tubepress_test_app_apicache_impl_listeners_http_ApiCacheListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_http_ApiCacheListener
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
        $this->_mockLogger    = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockContext   = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockEvent     = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockRequest   = $this->mock('tubepress_lib_api_http_message_RequestInterface');
        $this->_mockResponse  = $this->mock('tubepress_lib_api_http_message_ResponseInterface');
        $this->_mockUrl       = $this->mock('tubepress_platform_api_url_UrlInterface');
        $this->_mockUrl       = $this->mock('tubepress_platform_api_url_UrlInterface');
        $this->_mockBody      = $this->mock('tubepress_lib_api_streams_StreamInterface');
        $this->_mockCacheItem = $this->mock('ehough_stash_interfaces_ItemInterface');
        $this->_sut           = new tubepress_app_impl_listeners_http_ApiCacheListener(

            $this->_mockLogger,
            $this->_mockContext,
            $this->_mockApiCache
        );
    }

    /**
     * @dataProvider getDataNonApiCall
     */
    public function testNonApiCall($method, $request)
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::CACHE_ENABLED)->andReturn(true);
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_setupRequestFromEvent($request);
        $this->_mockRequest->shouldReceive('getConfig')->once()->andReturn(array());

        $this->_runEvent($method);
    }

    public function getDataNonApiCall()
    {
        return array(

            array('onRequest', true),
            array('onResponse', false),
        );
    }

    /**
     * @dataProvider getDataCacheDisabled
     */
    public function testCacheDisabled($method, $request)
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::CACHE_ENABLED)->andReturn(false);
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->once()->with('API cache is disabled');
        $this->_setupRequestFromEvent($request);
        $this->_runEvent($method);
    }
    
    public function getDataCacheDisabled()
    {
        return array(

            array('onRequest', true),
            array('onResponse', false)
        );
    }

    public function testRequestCacheHit()
    {
        $this->_setupForExecution(true);

        $this->_mockLogger->shouldReceive('debug')->once()->with('Asking cache for <a href="<url>">URL</a>');
        $this->_mockLogger->shouldReceive('debug')->once()->with('Cache hit for <a href="<url>">URL</a>.');
        $this->_mockApiCache->shouldReceive('getItem')->once()->with('<url>')->andReturn($this->_mockCacheItem);
        $this->_mockCacheItem->shouldReceive('get')->once()->andReturn('abc');
        $this->_mockCacheItem->shouldReceive('isMiss')->twice()->andReturn(false);

        $this->_mockEvent->shouldReceive('setArgument')->once()->with('response', ehough_mockery_Mockery::on(function ($response) {

            return $response instanceof tubepress_lib_api_http_message_ResponseInterface;
        }));
        $this->_mockEvent->shouldReceive('stopPropagation')->once();

        $this->_runEvent('onRequest');
    }

    public function testRequestCacheMiss()
    {
        $this->_setupForExecution(true);

        $this->_mockLogger->shouldReceive('debug')->once()->with('Asking cache for <a href="<url>">URL</a>');
        $this->_mockLogger->shouldReceive('debug')->once()->with('Cache miss for <a href="<url>">URL</a>.');
        $this->_mockApiCache->shouldReceive('getItem')->once()->with('<url>')->andReturn($this->_mockCacheItem);
        $this->_mockCacheItem->shouldReceive('isMiss')->twice()->andReturn(true);

        $this->_runEvent('onRequest');
    }

    public function testResponseBadSave()
    {
        $this->_setupForExecution(false);

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR)->andReturn(PHP_INT_MAX);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockResponse);
        $this->_mockResponse->shouldReceive('getBody')->once()->andReturn($this->_mockBody);
        $this->_mockBody->shouldReceive('toString')->once()->andReturn('abc');
        $this->_mockLogger->shouldReceive('error')->once()->with('Unable to store data to cache');
        $this->_mockApiCache->shouldReceive('getItem')->once()->with('<url>')->andReturn($this->_mockCacheItem);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS)->andReturn(44);
        $this->_mockCacheItem->shouldReceive('set')->once()->with('abc', 44)->andReturn(false);

        $this->_mockResponse->shouldReceive('hasHeader')->once()->with('TubePress-API-Cache-Hit')->andReturn(false);

        $this->_runEvent('onResponse');
    }

    public function testResponseGoodSave()
    {
        $this->_setupForExecution(false);

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR)->andReturn(1);
        $this->_mockApiCache->shouldReceive('flush')->once();
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockResponse);
        $this->_mockResponse->shouldReceive('getBody')->once()->andReturn($this->_mockBody);
        $this->_mockBody->shouldReceive('toString')->once()->andReturn('abc');
        $this->_mockApiCache->shouldReceive('getItem')->once()->with('<url>')->andReturn($this->_mockCacheItem);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS)->andReturn(44);
        $this->_mockCacheItem->shouldReceive('set')->once()->with('abc', 44)->andReturn(true);

        $this->_mockResponse->shouldReceive('hasHeader')->once()->with('TubePress-API-Cache-Hit')->andReturn(false);

        $this->_runEvent('onResponse');
    }

    public function testResponseCacheHit()
    {
        $this->_setupForExecution(false);
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockResponse);
        $this->_mockResponse->shouldReceive('hasHeader')->once()->with('TubePress-API-Cache-Hit')->andReturn(true);

        $this->_runEvent('onResponse');
    }

    private function _setupForExecution($request)
    {
        $this->_setupRequestFromEvent($request);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::CACHE_ENABLED)->andReturn(true);
        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockEvent->shouldReceive('getArgument')->atLeast(1)->with('request')->andReturn($this->_mockRequest);
        $this->_mockRequest->shouldReceive('getConfig')->once()->andReturn(array('tubepress-remote-api-call' => true));
        $this->_mockRequest->shouldReceive('getUrl')->andReturn($this->_mockUrl);
        $this->_mockUrl->shouldReceive('__toString')->andReturn('<url>');
    }

    private function _setupRequestFromEvent($request)
    {
        if ($request) {
            $this->_mockEvent->shouldReceive('getSubject')->andReturn($this->_mockRequest);
        } else {
            $this->_mockEvent->shouldReceive('getArgument')->once()->with('request')->andReturn($this->_mockRequest);
        }
    }

    private function _runEvent($method)
    {
        $this->_sut->$method($this->_mockEvent);
        $this->assertTrue(true);
    }
}