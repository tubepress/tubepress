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
class tubepress_plugins_youtube_impl_provider_YouTubeProviderTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_youtube_impl_provider_YouTubeProvider
     */
    private $_sut;

    private $_mockUrlBuilder;

    private $_mockFeedFetcher;

    private $_mockExecutionContext;

    private $_mockEventDispatcher;

    private $_mockHttpRequestParameterService;

    public function setUp()
    {
        $this->_mockUrlBuilder  = Mockery::mock(tubepress_spi_provider_UrlBuilder::_);
        $this->_mockFeedFetcher = Mockery::mock(tubepress_spi_feed_FeedFetcher::_);
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setFeedFetcher($this->_mockFeedFetcher);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);

        $this->_sut = new tubepress_plugins_youtube_impl_provider_YouTubeProvider($this->_mockUrlBuilder);
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

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            ),

            $this->_sut->getGallerySourceNames()
        );
    }

    function testMultipleVideos()
    {
        $this->_mockUrlBuilder->shouldReceive('buildGalleryUrl')->once()->with(36)->andReturn('abc');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->times(16)->with(tubepress_api_const_options_names_Meta::DESC_LIMIT)->andReturn(3);
        $this->_mockExecutionContext->shouldReceive('get')->times(16)->with(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->times(16)->with(tubepress_api_const_options_names_Meta::RELATIVE_DATES)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->times(16)->with(tubepress_api_const_options_names_Meta::DATEFORMAT)->andReturn('c');

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', true)->andReturn($this->galleryXml());

        $this->_mockEventDispatcher->shouldReceive('dispatch')->times(16)->with(

            tubepress_api_const_event_CoreEventNames::VIDEO_CONSTRUCTION,
            Mockery::on(function ($arg) {

                return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() instanceof tubepress_api_video_Video;
            })
        );

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            Mockery::on(function ($arg) {

                return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() instanceof tubepress_api_video_VideoGalleryPage;
            })
        );

        $result = $this->_sut->fetchVideoGalleryPage(36);

        $this->assertTrue($result instanceof tubepress_api_video_VideoGalleryPage);
    }

    function testFetchSingleVideo()
    {
        $this->_mockUrlBuilder->shouldReceive('buildSingleVideoUrl')->once()->with('SJxBZgC29ts')->andReturn('abc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::DESC_LIMIT)->andReturn(3);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS)->andReturn(false);
        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', true)->andReturn($this->singleVideoXml());

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_CoreEventNames::VIDEO_CONSTRUCTION,
            Mockery::on(function ($arg) {

                return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() instanceof tubepress_api_video_Video;
            })
        );

        $result = $this->_sut->fetchSingleVideo('SJxBZgC29ts');

        $this->assertTrue($result instanceof tubepress_api_video_Video);
    }

    function singleVideoXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/youtube-single-video.xml');
    }

    function galleryXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/youtube-gallery.xml');
    }
}
