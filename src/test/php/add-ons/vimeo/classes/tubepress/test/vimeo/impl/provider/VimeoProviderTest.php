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
 * @covers tubepress_vimeo_impl_provider_VimeoVideoProvider
 */
class tubepress_test_vimeo_impl_provider_VimeoProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo_impl_provider_VimeoVideoProvider
     */
    private $_sut;

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
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockEventDispatcher  = $this->mock(tubepress_core_api_event_EventDispatcherInterface::_);
        $this->_mockUrlFactory       = $this->mock(tubepress_core_api_url_UrlFactoryInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_sut = new tubepress_vimeo_impl_provider_VimeoVideoProvider(

            $this->_mockLogger,
            $this->_mockUrlFactory,
            $this->_mockExecutionContext,
            $this->_mockEventDispatcher
        );
    }

    public function testGetName()
    {
        $this->assertEquals('vimeo', $this->_sut->getName());
    }

    public function testRecognizesVideoId()
    {
        $this->assertTrue($this->_sut->singleElementRecognizesId('11111111'));
        $this->assertFalse($this->_sut->singleElementRecognizesId('11111111d'));
        $this->assertFalse($this->_sut->singleElementRecognizesId('dddddddd'));
    }

    public function testSources()
    {
        $this->assertEquals(

            array(

                tubepress_vimeo_api_const_options_Values::VIMEO_ALBUM,
                tubepress_vimeo_api_const_options_Values::VIMEO_APPEARS_IN,
                tubepress_vimeo_api_const_options_Values::VIMEO_CHANNEL,
                tubepress_vimeo_api_const_options_Values::VIMEO_CREDITED,
                tubepress_vimeo_api_const_options_Values::VIMEO_GROUP,
                tubepress_vimeo_api_const_options_Values::VIMEO_LIKES,
                tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH,
                tubepress_vimeo_api_const_options_Values::VIMEO_UPLOADEDBY
            ),

            $this->_sut->getGallerySourceNames()
        );
    }

    public function testGetfriendlyName()
    {
        $this->assertEquals('Vimeo', $this->_sut->getFriendlyName());
    }

    public function testGetAdditionalMetaNames()
    {
        $expected = array(

            tubepress_vimeo_api_const_options_Names::LIKES
        );

        $actual = $this->_sut->getAdditionalMetaNames();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoKeyGallery()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => '',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));

        $this->_sut->urlBuildForSingle(3);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoKeySingleVideo()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => '',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));

        $this->_sut->urlBuildForSingle('xyz');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoSecretGallery()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => ''
        ));

        $this->_sut->urlBuildForGallery(3);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoSecretSingleVideo()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => ''
        ));

        $this->_sut->urlBuildForSingle('xyz');
    }

    public function testSingleVideoUrl()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getInfo&video_id=444333&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_SINGLE, $mockUrl);

        $result = $this->_sut->urlBuildForSingle('444333');

        $this->assertSame($mockUrl, $result);
    }

    public function testExecuteGroup()
    {
        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 20,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_vimeo_api_const_options_Values::VIMEO_GROUP,
            tubepress_vimeo_api_const_options_Names::VIMEO_GROUP_VALUE => 'eric',
            tubepress_core_api_const_options_Names::ORDER_BY => 'random',
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.groups.getVideos&group_id=eric&full_response=true&page=1&per_page=20&sort=random&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY, $mockUrl);

        $result = $this->_sut->urlBuildForGallery(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testExecuteCreditedTo()
    {
        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 20,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_vimeo_api_const_options_Values::VIMEO_CREDITED,
            tubepress_vimeo_api_const_options_Names::VIMEO_CREDITED_VALUE => 'eric',
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_core_api_const_options_Names::ORDER_BY => 'random',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));


        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getAll&user_id=eric&full_response=true&page=1&per_page=20&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY, $mockUrl);
        $result = $this->_sut->urlBuildForGallery(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteAlbum()
    {
        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 20,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_vimeo_api_const_options_Values::VIMEO_ALBUM,
            tubepress_vimeo_api_const_options_Names::VIMEO_ALBUM_VALUE => 'eric',
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));


        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.albums.getVideos&album_id=eric&full_response=true&page=1&per_page=20&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY, $mockUrl);

        $result = $this->_sut->urlBuildForGallery(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteChannel()
    {
        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 20,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_vimeo_api_const_options_Values::VIMEO_CHANNEL,
            tubepress_vimeo_api_const_options_Names::VIMEO_CHANNEL_VALUE => 'eric',
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.channels.getVideos&channel_id=eric&full_response=true&page=1&per_page=20&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY, $mockUrl);
        $result = $this->_sut->urlBuildForGallery(1);

        $this->assertSame($mockUrl, $result);
    }


    public function testexecuteSearch()
    {
        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 20,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH,
            tubepress_vimeo_api_const_options_Names::VIMEO_SEARCH_VALUE => 'eric hough',
            tubepress_core_api_const_options_Names::ORDER_BY => 'relevance',
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret',
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.search&query=eric+hough&full_response=true&page=1&per_page=20&sort=relevant&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY, $mockUrl);
        $result = $this->_sut->urlBuildForGallery(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteSearchWithUser()
    {
        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 20,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH,
            tubepress_vimeo_api_const_options_Names::VIMEO_SEARCH_VALUE => 'eric hough',
            tubepress_core_api_const_options_Names::ORDER_BY => 'relevance',
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret',
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => 'ehough'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.search&query=eric+hough&user_id=ehough&full_response=true&page=1&per_page=20&sort=relevant&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY, $mockUrl);
        $result = $this->_sut->urlBuildForGallery(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteAppearsIn()
    {
        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 20,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_vimeo_api_const_options_Values::VIMEO_APPEARS_IN,
            tubepress_vimeo_api_const_options_Names::VIMEO_APPEARS_IN_VALUE => 'eric',
            tubepress_core_api_const_options_Names::ORDER_BY => 'oldest',
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getAppearsIn&user_id=eric&full_response=true&page=1&per_page=20&sort=oldest&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY, $mockUrl);
        $result = $this->_sut->urlBuildForGallery(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteLikes()
    {
        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 20,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_vimeo_api_const_options_Values::VIMEO_LIKES,
            tubepress_vimeo_api_const_options_Names::VIMEO_LIKES_VALUE => 'eric',
            tubepress_core_api_const_options_Names::ORDER_BY => 'rating',
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getLikes&user_id=eric&full_response=true&page=1&per_page=20&sort=most_liked&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY, $mockUrl);
        $result = $this->_sut->urlBuildForGallery(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteUploadedBy()
    {
        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 20,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_vimeo_api_const_options_Values::VIMEO_UPLOADEDBY,
            tubepress_vimeo_api_const_options_Names::VIMEO_UPLOADEDBY_VALUE => 'eric',
            tubepress_core_api_const_options_Names::ORDER_BY => 'commentCount',
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getUploaded&user_id=eric&full_response=true&page=1&per_page=20&sort=most_commented&format=php')->andReturn($mockUrl);

        $this->_setupEventDispatcher(tubepress_vimeo_api_const_VimeoEventNames::URL_GALLERY, $mockUrl);
        $result = $this->_sut->urlBuildForGallery(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testGetTotalResultCount()
    {
        $rawFeed = $this->_gallerySerializedPhp();
        $this->_sut->freePrepareForAnalysis($rawFeed);
        $actual = $this->_sut->feedGetTotalResultCount($rawFeed);
        $this->assertEquals(526, $actual);
    }


    public function testVimeoError()
    {
        $this->setExpectedException('RuntimeException', 'Vimeo responded to TubePress with an error: Invalid consumer key');
        $rawFeed = $this->_errorSerializedPhp();
        $this->_sut->freePrepareForAnalysis($rawFeed);
    }

    public function testCountElements()
    {
        $rawFeed = $this->_gallerySerializedPhp();
        $this->_sut->freePrepareForAnalysis($rawFeed);
        $actual = $this->_sut->feedCountElements($rawFeed);
        $this->_sut->feedOnAnalysisComplete($rawFeed);
        $this->assertEquals(16, $actual);
    }

    private function expectOptions($arr) {

        foreach ($arr as $key => $value) {

            $this->_mockExecutionContext->shouldReceive('get')->with($key)->andReturn($value);
        }
    }

    private function _setupEventDispatcher($evenName, $subject)
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($subject);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($subject)->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with($evenName, $mockEvent);
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
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/addons/vimeo/' . $filename);

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_test_vimeo_impl_provider_VimeoProviderTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    public function _callbackStrlen($matches)
    {
        $toReturn = "s:" . strlen($matches[2]) . ":\"" . $matches[2] . "\";";

        return $toReturn;
    }
}
