<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_youtube_impl_provider_YouTubeProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFeedFetcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    public function onSetup()
    {
        $this->_mockUrlBuilder  = $this->createMockSingletonService(tubepress_spi_provider_UrlBuilder::_);
        $this->_mockFeedFetcher = $this->createMockSingletonService(tubepress_spi_feed_FeedFetcher::_);
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher = $this->createMockSingletonService('ehough_tickertape_EventDispatcherInterface');
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_sut = new tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService($this->_mockUrlBuilder);
    }

    public function testGetName()
    {
        $this->assertEquals('youtube', $this->_sut->getName());
    }

    public function testRecognizesVideoId()
    {
        $this->assertTrue($this->_sut->recognizesVideoId('SJxBZgC29ts'));
        $this->assertTrue($this->_sut->recognizesVideoId('S5yDS-mFRy4'));
        $this->assertTrue($this->_sut->recognizesVideoId('KcXjhikIz6o'));
        $this->assertTrue($this->_sut->recognizesVideoId('T8KJGtMGMSY'));
        $this->assertFalse($this->_sut->recognizesVideoId('339494949'));
        $this->assertFalse($this->_sut->recognizesVideoId('S5yDS-mFRy]'));
        $this->assertFalse($this->_sut->recognizesVideoId('KcXjhikIz'));
        $this->assertFalse($this->_sut->recognizesVideoId('T8K..tMGMSY'));
    }

    public function testSources()
    {
        $this->assertEquals(

            array(

                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            ),

            $this->_sut->getGallerySourceNames()
        );
    }

    public function testMultipleVideos()
    {
        $this->_mockUrlBuilder->shouldReceive('buildGalleryUrl')->once()->with(36)->andReturn('abc');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', true)->andReturn($this->galleryXml());

        $this->_mockEventDispatcher->shouldReceive('dispatch')->times(16)->with(

            tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION,
            ehough_mockery_Mockery::on(function ($arg) {

                return $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() instanceof tubepress_api_video_Video;
            })
        );

        $result = $this->_sut->fetchVideoGalleryPage(36);

        $this->assertTrue($result instanceof tubepress_api_video_VideoGalleryPage);
    }

    public function testFetchSingleVideo()
    {
        $this->_mockUrlBuilder->shouldReceive('buildSingleVideoUrl')->once()->with('SJxBZgC29ts')->andReturn('abc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);
        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', true)->andReturn($this->singleVideoXml());

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION,
            ehough_mockery_Mockery::on(function ($arg) {

                return $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() instanceof tubepress_api_video_Video;
            })
        );

        $result = $this->_sut->fetchSingleVideo('SJxBZgC29ts');

        $this->assertTrue($result instanceof tubepress_api_video_Video);
    }

    public function singleVideoXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/youtube-single-video.xml');
    }

    public function galleryXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/youtube-gallery.xml');
    }
}
