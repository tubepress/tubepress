<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_collector_DefaultVideoCollectorTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_collector_DefaultVideoCollector
     */
    private $_sut;

    private $_mockExecutionContext;

    private $_mockHttpRequestParameterService;

    private $_mockEventDispatcher;
    
    private $_mockProvider;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_collector_DefaultVideoCollector();

        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService('ehough_tickertape_api_IEventDispatcher');
    }

    public function testGetSingle()
    {
        $this->_mockProvider = $this->createMockPluggableService(tubepress_spi_provider_PluggableVideoProviderService::_);

        $this->_mockProvider->shouldReceive('getName')->andReturn('provider-name');
        $this->_mockProvider->shouldReceive('recognizesVideoId')->once()->with('xyz')->andReturn(true);
        $this->_mockProvider->shouldReceive('fetchSingleVideo')->once()->with('xyz')->andReturn('123');

        $result = $this->_sut->collectSingleVideo('xyz');

        $this->assertEquals('123', $result);
    }

    public function testGetSingleNoProvidersRecognize()
    {
        $this->_mockProvider = $this->createMockPluggableService(tubepress_spi_provider_PluggableVideoProviderService::_);

        $this->_mockProvider->shouldReceive('getName')->andReturn('provider-name');
        $this->_mockProvider->shouldReceive('recognizesVideoId')->once()->with('xyz')->andReturn(false);

        $result = $this->_sut->collectSingleVideo('xyz');

        $this->assertNull($result);
    }

    public function testGetSingleNoProviders()
    {
        $result = $this->_sut->collectSingleVideo('xyz');

        $this->assertNull($result);
    }

    public function testProviderHandles()
    {
        $mockPage = new tubepress_api_video_VideoGalleryPage();

        $this->_mockProvider = $this->createMockPluggableService(tubepress_spi_provider_PluggableVideoProviderService::_);

        $this->_mockProvider->shouldReceive('getGallerySourceNames')->andReturn(array('x'));
        $this->_mockProvider->shouldReceive('fetchVideoGalleryPage')->once()->with(97)->andReturn($mockPage);
        $this->_mockProvider->shouldReceive('getName')->andReturn('provider-name');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('x');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(97);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION, Mockery::on(function ($arg) use ($mockPage) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $mockPage;
        }));

        $result = $this->_sut->collectVideoGalleryPage();

        $this->assertSame($mockPage, $result);
    }

    public function testMultipleNoProvidersCouldHandle()
    {
        $this->_mockProvider = $this->createMockPluggableService(tubepress_spi_provider_PluggableVideoProviderService::_);
        $this->_mockProvider->shouldReceive('getName')->andReturn('provider-name');
        $this->_mockProvider->shouldReceive('getGallerySourceNames')->andReturn(array());

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('x');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(97);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION, Mockery::on(function ($arg) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() instanceof tubepress_api_video_VideoGalleryPage;
        }));

        $result = $this->_sut->collectVideoGalleryPage();

        $this->assertTrue($result instanceof tubepress_api_video_VideoGalleryPage);
        $this->assertTrue($result->getTotalResultCount() === 0);
        $this->assertTrue(is_array($result->getVideos()));
        $this->assertTrue(count($result->getVideos()) === 0);
    }

    public function testMultipleNoProviders()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('x');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(97);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION, Mockery::on(function ($arg) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() instanceof tubepress_api_video_VideoGalleryPage;
        }));

        $result = $this->_sut->collectVideoGalleryPage();

        $this->assertTrue($result instanceof tubepress_api_video_VideoGalleryPage);
        $this->assertTrue($result->getTotalResultCount() === 0);
        $this->assertTrue(is_array($result->getVideos()));
        $this->assertTrue(count($result->getVideos()) === 0);
    }
}