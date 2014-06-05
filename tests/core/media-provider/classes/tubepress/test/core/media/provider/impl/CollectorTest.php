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
 * @covers tubepress_core_media_provider_impl_Collector<extended>
 */
class tubepress_test_core_media_provider_impl_CollectorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_provider_impl_Collector
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {

        $this->_mockExecutionContext            = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_mockLogger                      = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_core_media_provider_impl_Collector(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockEventDispatcher,
            $this->_mockHttpRequestParameterService
        );
    }

    public function testGetSingle()
    {
        $this->_mockProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);

        $this->_mockProvider->shouldReceive('getName')->andReturn('provider-name');
        $this->_mockProvider->shouldReceive('recognizesItemId')->once()->with('xyz')->andReturn(true);
        $this->_mockProvider->shouldReceive('fetchSingle')->once()->with('xyz')->andReturn('123');

        $this->_sut->setMediaProviders(array($this->_mockProvider));

        $result = $this->_sut->collectSingle('xyz');

        $this->assertEquals('123', $result);
    }

    public function testGetSingleNoProvidersRecognize()
    {
        $this->_mockProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);

        $this->_mockProvider->shouldReceive('getName')->andReturn('provider-name');
        $this->_mockProvider->shouldReceive('recognizesItemId')->once()->with('xyz')->andReturn(false);
        $this->_sut->setMediaProviders(array($this->_mockProvider));

        $result = $this->_sut->collectSingle('xyz');

        $this->assertNull($result);
    }

    public function testGetSingleNoProviders()
    {
        $result = $this->_sut->collectSingle('xyz');

        $this->assertNull($result);
    }

    public function testProviderHandles()
    {
        $mockPage = new tubepress_core_media_provider_api_Page();

        $this->_mockProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);

        $this->_mockProvider->shouldReceive('getGallerySourceNames')->andReturn(array('x'));
        $this->_mockProvider->shouldReceive('fetchPage')->once()->with(97)->andReturn($mockPage);
        $this->_mockProvider->shouldReceive('getName')->andReturn('provider-name');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE)->andReturn('x');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(97);

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockPage);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockPage, array('pageNumber' => 97))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_media_provider_api_Constants::EVENT_NEW_MEDIA_PAGE, $mockEvent);

        $this->_sut->setMediaProviders(array($this->_mockProvider));

        $result = $this->_sut->collectPage();

        $this->assertSame($mockPage, $result);
    }

    public function testMultipleNoProvidersCouldHandle()
    {
        $this->_mockProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $this->_mockProvider->shouldReceive('getName')->andReturn('provider-name');
        $this->_mockProvider->shouldReceive('getGallerySourceNames')->andReturn(array());

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE)->andReturn('x');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(97);

        $mockPage = new tubepress_core_media_provider_api_Page();
        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockPage);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::on(function ($arg) {
            return $arg instanceof tubepress_core_media_provider_api_Page && count($arg->getItems()) === 0;
        }), array('pageNumber' => 97))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_media_provider_api_Constants::EVENT_NEW_MEDIA_PAGE, $mockEvent);

        $this->_sut->setMediaProviders(array($this->_mockProvider));

        $result = $this->_sut->collectPage();

        $this->assertTrue($result instanceof tubepress_core_media_provider_api_Page);
        $this->assertTrue($result->getTotalResultCount() === 0);
        $this->assertTrue(is_array($result->getItems()));
        $this->assertTrue(count($result->getItems()) === 0);
    }

    public function testMultipleNoProviders()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE)->andReturn('x');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(97);

        $mockPage = new tubepress_core_media_provider_api_Page();
        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockPage);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::on(function ($arg) {
            return $arg instanceof tubepress_core_media_provider_api_Page && count($arg->getItems()) === 0;
        }), array('pageNumber' => 97))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_media_provider_api_Constants::EVENT_NEW_MEDIA_PAGE, $mockEvent);

        $result = $this->_sut->collectPage();

        $this->assertTrue($result instanceof tubepress_core_media_provider_api_Page);
        $this->assertTrue($result->getTotalResultCount() === 0);
        $this->assertTrue(is_array($result->getItems()));
        $this->assertTrue(count($result->getItems()) === 0);
    }
}