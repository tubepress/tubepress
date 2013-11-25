<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_feed_CacheAwareFeedFetcherTest extends tubepress_test_TubePressUnitTest
{
    private static $_fakeUrl = 'http://foo.bar/x/y/z/index.php?cat=dog#bird';

    /**
     * @var tubepress_impl_feed_CacheAwareFeedFetcher
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCache;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpClient;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockItem;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockCache            = $this->createMockSingletonService('ehough_stash_PoolInterface');
        $this->_mockHttpClient       = $this->createMockSingletonService('ehough_shortstop_api_HttpClientInterface');
        $this->_mockItem             = $this->createMockSingletonService('ehough_stash_ItemInterface');
        $this->_mockEventDispatcher  = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_sut = new tubepress_impl_feed_CacheAwareFeedFetcher();
    }

    public function testFetchCacheHit()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);
        $this->_mockCache->shouldReceive('getItem')->once()->with(self::$_fakeUrl)->andReturn($this->_mockItem);
        $this->_mockItem->shouldReceive('isValid')->once()->andReturn(true);
        $this->_mockItem->shouldReceive('get')->once()->andReturn('someValue');

        $this->assertEquals('someValue', $this->_sut->fetch(self::$_fakeUrl));
    }

    public function testFetchCacheMiss()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS)->andReturn(333);
        $this->_mockCache->shouldReceive('getItem')->once()->with(self::$_fakeUrl)->andReturn($this->_mockItem);
        $this->_mockItem->shouldReceive('isValid')->once()->andReturn(false);
        $this->_mockItem->shouldReceive('set')->once()->with('abc', 333)->andReturn(true);
        $this->_mockItem->shouldReceive('get')->once()->andReturn('someValue');
        $this->_setupHttpExecution();

        $this->assertEquals('someValue', $this->_sut->fetch(self::$_fakeUrl));
    }

    public function testFetchCacheDisabled()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(false);
        $this->_setupHttpExecution();

        $this->assertEquals('abc', $this->_sut->fetch(self::$_fakeUrl));
    }

    public function _callbackCacheMiss($request)
    {
        return $request instanceof ehough_shortstop_api_HttpRequest && "$request" === 'GET to ' . self::$_fakeUrl;
    }

    public function _callbackHttpResponse(tubepress_api_event_EventInterface $event)
    {
        $ok = $event->getSubject() === 'xyz' && $event->getArgument('request') instanceof ehough_shortstop_api_HttpRequest;

        $event->setSubject('abc');

        return $ok;
    }

    private function _setupHttpExecution()
    {
        $mockResponse = ehough_mockery_Mockery::mock();
        $mockEntity   = ehough_mockery_Mockery::mock();

        $mockResponse->shouldReceive('getEntity')->once()->andReturn($mockEntity);
        $mockEntity->shouldReceive('getContent')->once()->andReturn('xyz');

        $this->_mockHttpClient->shouldReceive('execute')->once()->with(ehough_mockery_Mockery::on(array($this, '_callbackCacheMiss')))->andReturn($mockResponse);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTTP_RESPONSE, ehough_mockery_Mockery::on(array($this, '_callbackHttpResponse')));
    }
}
