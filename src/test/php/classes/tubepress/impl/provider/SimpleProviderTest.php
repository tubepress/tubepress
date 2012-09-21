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
class org_tubepress_impl_provider_SimpleProviderTest extends TubePressUnitTest
{
    private $_sut;
    private $_fakeVideo;
    private $_mockHttpRequestParameterService;
    private $_mockProviderCalculator;
    private $_mockUrlBuilder;
    private $_mockFeedFetcher;
    private $_mockExecutionContext;
    private $_mockFeedInspector;
    private $_mockFactory;
    private $_mockEventDispatcher;
    
    function setup()
    {
        $this->_sut       = new tubepress_impl_provider_SimpleProvider();
        $this->_fakeVideo = new tubepress_api_video_Video();
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);
        $this->_mockUrlBuilder = Mockery::mock(tubepress_spi_feed_UrlBuilder::_);
        $this->_mockFeedFetcher = Mockery::mock(tubepress_spi_feed_FeedFetcher::_);
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockFeedInspector = Mockery::mock(tubepress_spi_feed_FeedInspector::_);
        $this->_mockFactory = Mockery::mock(tubepress_spi_factory_VideoFactory::_);
        $this->_mockEventDispatcher = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setUrlBuilder($this->_mockUrlBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setFeedFetcher($this->_mockFeedFetcher);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setFeedInspector($this->_mockFeedInspector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoFactory($this->_mockFactory);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetMultipleVideosFactoryBuildsNone()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('current-video-provider');

        $this->_mockUrlBuilder->shouldReceive('buildGalleryUrl')->once()->with(1)->andReturn('gallery-url');

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('gallery-url', false)->andReturn('fetch-result');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(false);

        $this->_mockFeedInspector->shouldReceive('getTotalResultCount')->once()->with('fetch-result')->andReturn(596);

        
        $this->_mockFactory->shouldReceive('feedToVideoArray')->once()->with('fetch-result')->andReturn(array());

        $this->assertEquals('final-result', $this->_sut->getMultipleVideos());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetMultipleVideosNoVids()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('current-video-provider');

        $this->_mockUrlBuilder->shouldReceive('buildGalleryUrl')->once()->with(1)->andReturn('gallery-url');

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('gallery-url', false)->andReturn('fetch-result');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(false);

        $this->_mockFeedInspector->shouldReceive('getTotalResultCount')->once()->with('fetch-result')->andReturn(0);

        $this->assertEquals('final-result', $this->_sut->getMultipleVideos());
    }

    public function testGetMultipleVideos()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->twice()->andReturn('current-video-provider');

        $this->_mockUrlBuilder->shouldReceive('buildGalleryUrl')->once()->with(1)->andReturn('gallery-url');

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('gallery-url', false)->andReturn('fetch-result');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(false);

        $this->_mockFeedInspector->shouldReceive('getTotalResultCount')->once()->with('fetch-result')->andReturn(596);

        $fakeVideoArray = array(5, 4, 3, 1);

        $this->_mockFactory->shouldReceive('feedToVideoArray')->once()->with('fetch-result')->andReturn($fakeVideoArray);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION, Mockery::on(function ($arg) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getArgument('providerName') === 'current-video-provider';

            $arg->getSubject()->setTotalResultCount(999);
            $arg->getSubject()->setVideos(array(1, 2, 4));

            return $good;
        }));

        $videoGalleryPage = $this->_sut->getMultipleVideos();

        $this->assertTrue($videoGalleryPage instanceof tubepress_api_video_VideoGalleryPage);
        $this->assertTrue($videoGalleryPage->getTotalResultCount() === 999);
        $this->assertEquals(array(1, 2, 4), $videoGalleryPage->getVideos());
    }

    public function testGetSingleVideoNotFound()
    {
        $this->_setupSingleVideoMocks(array());
        $this->assertNull($this->_sut->getSingleVideo('video-id'));
    }

    public function testGetSingleVideo()
    {
        $val = array($this->_fakeVideo);
        $this->_setupSingleVideoMocks($val);

        $this->_mockProviderCalculator->shouldReceive('calculateProviderOfVideoId')->with('video-id')->andReturn('video-provider');

        $video = $this->_fakeVideo;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION, Mockery::on(function ($arg) use ($video) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getArgument('providerName') === 'video-provider';

            $arg->getSubject()->setTotalResultCount(1);
            $arg->getSubject()->setVideos(array($video));

            return $good;
        }));

        $this->assertSame($this->_fakeVideo, $this->_sut->getSingleVideo('video-id'));
    }

    private function _setupSingleVideoMocks($factoryResult)
    {
        $this->_mockUrlBuilder->shouldReceive('buildSingleVideoUrl')->once()->with('video-id')->andReturn('video-url');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(false);

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('video-url', false)->andReturn('fake-feed');

        $this->_mockFactory->shouldReceive('feedToVideoArray')->once()->with('fake-feed')->andReturn($factoryResult);
    }
}