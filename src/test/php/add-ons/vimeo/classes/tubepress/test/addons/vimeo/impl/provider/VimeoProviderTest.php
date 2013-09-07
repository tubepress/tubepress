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

/**
 * @covers tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService
 */
class tubepress_test_addons_vimeo_impl_provider_VimeoProviderTest extends tubepress_test_TubePressUnitTest
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

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', true)->andReturn($this->_gallerySerializedPhp());

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
        $this->_mockUrlBuilder->shouldReceive('buildSingleVideoUrl')->once()->with('333383838')->andReturn('abc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(true);

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', true)->andReturn($this->_singleVideoSerializedPhp());

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION,
            ehough_mockery_Mockery::on(function ($arg) {

                return $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() instanceof tubepress_api_video_Video;
            })
        );

        $result = $this->_sut->fetchSingleVideo('333383838');

        $this->assertTrue($result instanceof tubepress_api_video_Video);
    }

    public function testMalformedData()
    {
        $this->setExpectedException('RuntimeException', 'Unable to unserialize PHP from Vimeo');

        $this->_mockUrlBuilder->shouldReceive('buildSingleVideoUrl')->once()->with('333383838')->andReturn('abc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(false);

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', false)->andReturn('"xxxx');

        $this->_sut->fetchSingleVideo('333383838');
    }

    public function testVimeoExplicitError()
    {
        $this->setExpectedException('RuntimeException', 'Vimeo responded to TubePress with an error: Invalid consumer key');

        $this->_mockUrlBuilder->shouldReceive('buildSingleVideoUrl')->once()->with('333383838')->andReturn('abc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_ENABLED)->andReturn(false);

        $this->_mockFeedFetcher->shouldReceive('fetch')->once()->with('abc', false)->andReturn($this->_errorSerializedPhp());

        $this->_sut->fetchSingleVideo('333383838');
    }

    private function _errorSerializedPhp()
    {
        return $this->_sanitizedSerialized('vimeo-error.txt');
    }

    private function _singleVideoSerializedPhp()
    {
        return $this->_sanitizedSerialized('vimeo-single-video.txt');
    }

    private function _gallerySerializedPhp()
    {
        return $this->_sanitizedSerialized('vimeo-gallery.txt');
    }

    private function _sanitizedSerialized($filename)
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/' . $filename);

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_test_addons_vimeo_impl_provider_VimeoProviderTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    public function _callbackStrlen($matches)
    {
        $toReturn = "s:" . strlen($matches[2]) . ":\"" . $matches[2] . "\";";

        return $toReturn;
    }
}
