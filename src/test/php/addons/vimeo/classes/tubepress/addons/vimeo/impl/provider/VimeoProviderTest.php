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
class tubepress_addons_vimeo_impl_provider_VimeoProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService
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
        $this->_mockUrlBuilder                  = $this->createMockSingletonService(tubepress_spi_provider_UrlBuilder::_);
        $this->_mockFeedFetcher                 = $this->createMockSingletonService(tubepress_spi_feed_FeedFetcher::_);
        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_sut = new tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService($this->_mockUrlBuilder);
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

                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY
            ),

            $this->_sut->getGallerySourceNames()
        );
    }

    public function testMultipleVideos()
    {
        $this->_mockUrlBuilder->shouldReceive('buildGalleryUrl')->once()->with(36)->andReturn('abc');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', true)->andReturn($this->galleryXml());

        $this->_mockEventDispatcher->shouldReceive('publish')->times(16)->with(

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
        $this->_mockUrlBuilder->shouldReceive('buildSingleVideoUrl')->once()->with('333383838')->andReturn('abc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', true)->andReturn($this->singleVideoXml());

        $this->_mockEventDispatcher->shouldReceive('publish')->once()->with(

            tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION,
            ehough_mockery_Mockery::on(function ($arg) {

                return $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() instanceof tubepress_api_video_Video;
            })
        );

        $result = $this->_sut->fetchSingleVideo('333383838');

        $this->assertTrue($result instanceof tubepress_api_video_Video);
    }

    public function singleVideoXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/vimeo-single-video.txt');

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s',array('tubepress_addons_vimeo_impl_provider_VimeoProviderTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    public function galleryXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/vimeo-gallery.txt');

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_addons_vimeo_impl_provider_VimeoProviderTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    public function _callbackStrlen($matches)
    {
        $toReturn = "s:" . strlen($matches[2]) . ":\"" . $matches[2] . "\";";

        return $toReturn;
    }
}
