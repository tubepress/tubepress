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
class org_tubepress_impl_collector_DefaultVideoCollectorTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_collector_DefaultVideoCollector
     */
    private $_sut;

    private $_mockExecutionContext;

    private $_mockServiceCollectionsRegistry;

    private $_mockHttpRequestParameterService;

    private $_mockEventDispatcher;

    public function setUp()
    {
        $this->_sut = new tubepress_impl_collector_DefaultVideoCollector();

        $this->_mockServiceCollectionsRegistry  = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);
        $this->_mockExecutionContext            = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockEventDispatcher             = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
    }

    public function testGetSingle()
    {
        $mockProvider = Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);

        $mockProvider->shouldReceive('getName')->andReturn('provider-name');
        $mockProvider->shouldReceive('recognizesVideoId')->once()->with('xyz')->andReturn(true);
        $mockProvider->shouldReceive('fetchSingleVideo')->once()->with('xyz')->andReturn('123');

        $mockProviders = array($mockProvider);

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_provider_PluggableVideoProviderService::_)->andReturn($mockProviders);

        $result = $this->_sut->collectSingleVideo('xyz');

        $this->assertEquals('123', $result);
    }

    public function testGetSingleNoProvidersRecognize()
    {
        $mockProvider = Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockProvider->shouldReceive('getName')->andReturn('provider-name');

        $mockProvider->shouldReceive('recognizesVideoId')->once()->with('xyz')->andReturn(false);

        $mockProviders = array($mockProvider);

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_provider_PluggableVideoProviderService::_)->andReturn($mockProviders);

        $result = $this->_sut->collectSingleVideo('xyz');

        $this->assertNull($result);
    }

    public function testGetSingleNoProviders()
    {
        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_provider_PluggableVideoProviderService::_)->andReturn(array());

        $result = $this->_sut->collectSingleVideo('xyz');

        $this->assertNull($result);
    }

    public function testProviderHandles()
    {
        $mockPage = new tubepress_api_video_VideoGalleryPage();

        $mockProvider = Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);

        $mockProvider->shouldReceive('getGallerySourceNames')->andReturn(array('x'));
        $mockProvider->shouldReceive('fetchVideoGalleryPage')->once()->with(97)->andReturn($mockPage);
        $mockProvider->shouldReceive('getName')->andReturn('provider-name');

        $mockProviders = array($mockProvider);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('x');

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_provider_PluggableVideoProviderService::_)->andReturn($mockProviders);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(97);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION, Mockery::on(function ($arg) use ($mockPage) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $mockPage;
        }));

        $result = $this->_sut->collectVideoGalleryPage();

        $this->assertSame($mockPage, $result);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testMultipleNoProvidersCouldHandle()
    {
        $mockProvider = Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockProvider->shouldReceive('getName')->andReturn('provider-name');

        $mockProvider->shouldReceive('getGallerySourceNames')->andReturn(array());

        $mockProviders = array($mockProvider);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('x');

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_provider_PluggableVideoProviderService::_)->andReturn($mockProviders);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(97);

        $result = $this->_sut->collectVideoGalleryPage();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testMultipleNoProviders()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('x');

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_provider_PluggableVideoProviderService::_)->andReturn(array());

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(97);

        $result = $this->_sut->collectVideoGalleryPage();
    }
}