<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_plugins_vimeo_impl_provider_VimeoProviderTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_vimeo_impl_provider_VimeoPluggableVideoProviderService
     */
    private $_sut;

    private $_mockUrlBuilder;

    private $_mockFeedFetcher;

    private $_mockExecutionContext;

    private $_mockEventDispatcher;

    private $_mockHttpRequestParameterService;

    public function onSetup()
    {
        $this->_mockUrlBuilder                  = $this->createMockSingletonService(tubepress_spi_provider_UrlBuilder::_);
        $this->_mockFeedFetcher                 = $this->createMockSingletonService(tubepress_spi_feed_FeedFetcher::_);
        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService('ehough_tickertape_api_IEventDispatcher');
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_sut = new tubepress_plugins_vimeo_impl_provider_VimeoPluggableVideoProviderService($this->_mockUrlBuilder);
    }

    public function testGetName()
    {
        $this->assertEquals('vimeo', $this->_sut->getName());
    }

    public function testRecognizesVideoId()
    {
        $this->assertTrue($this->_sut->recognizesVideoId('11111111'));
        $this->assertFalse($this->_sut->recognizesVideoId('11111111d'));
        $this->assertFalse($this->_sut->recognizesVideoId('dddddddd'));
    }

    public function testSources()
    {
        $this->assertEquals(

            array(

                tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
                tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
                tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
                tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
                tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
                tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
                tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
                tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY
            ),

            $this->_sut->getGallerySourceNames()
        );
    }

    function testMultipleVideos()
    {
        $this->_mockUrlBuilder->shouldReceive('buildGalleryUrl')->once()->with(36)->andReturn('abc');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);

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
        $this->_mockUrlBuilder->shouldReceive('buildSingleVideoUrl')->once()->with('333383838')->andReturn('abc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', true)->andReturn($this->singleVideoXml());

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_CoreEventNames::VIDEO_CONSTRUCTION,
            Mockery::on(function ($arg) {

                return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() instanceof tubepress_api_video_Video;
            })
        );

        $result = $this->_sut->fetchSingleVideo('333383838');

        $this->assertTrue($result instanceof tubepress_api_video_Video);
    }

    function singleVideoXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/vimeo-single-video.txt');

        $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );

        return $out;
    }

    function galleryXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/vimeo-gallery.txt');

        $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );

        return $out;
    }
}
