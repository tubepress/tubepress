<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_vimeo2_impl_media_FeedHandler
 */
class tubepress_test_vimeo2_impl_media_FeedHandlerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo2_impl_media_FeedHandler
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

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
        $this->_mockExecutionContext = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockUrlFactory       = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_sut = new tubepress_vimeo2_impl_media_FeedHandler(

            $this->_mockLogger,
            $this->_mockUrlFactory,
            $this->_mockExecutionContext
        );

        $this->_sut->__invoke();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoKeyGallery()
    {
        $this->expectOptions(array(
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => '',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $this->_sut->buildUrlForItem(3);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoKeySingleVideo()
    {
        $this->expectOptions(array(
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => '',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $this->_sut->buildUrlForItem('xyz');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoSecretGallery()
    {
        $this->expectOptions(array(
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => ''
        ));

        $this->_sut->buildUrlForPage(3);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoSecretSingleVideo()
    {
        $this->expectOptions(array(
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => ''
        ));

        $this->_sut->buildUrlForItem('xyz');
    }

    public function testSingleVideoUrl()
    {
        $this->expectOptions(array(
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getInfo&video_id=444333&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForItem('444333');

        $this->assertSame($mockUrl, $result);
    }

    public function testExecuteGroup()
    {
        $this->expectOptions(array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 20,
            tubepress_api_options_Names::GALLERY_SOURCE => tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_GROUP_VALUE => 'eric',
            tubepress_api_options_Names::FEED_ORDER_BY => 'random',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.groups.getVideos&group_id=eric&full_response=true&page=1&per_page=20&sort=random&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testExecuteCreditedTo()
    {
        $this->expectOptions(array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 20,
            tubepress_api_options_Names::GALLERY_SOURCE => tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CREDITED,
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_CREDITED_VALUE => 'eric',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_api_options_Names::FEED_ORDER_BY => 'random',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));


        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getAll&user_id=eric&full_response=true&page=1&per_page=20&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteAlbum()
    {
        $this->expectOptions(array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 20,
            tubepress_api_options_Names::GALLERY_SOURCE => tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_ALBUM_VALUE => 'eric',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));


        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.albums.getVideos&album_id=eric&full_response=true&page=1&per_page=20&format=php')->andReturn($mockUrl);


        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteChannel()
    {
        $this->expectOptions(array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 20,
            tubepress_api_options_Names::GALLERY_SOURCE => tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_CHANNEL_VALUE => 'eric',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.channels.getVideos&channel_id=eric&full_response=true&page=1&per_page=20&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }


    public function testexecuteSearch()
    {
        $this->expectOptions(array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 20,
            tubepress_api_options_Names::GALLERY_SOURCE => tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SEARCH_VALUE => 'eric hough',
            tubepress_api_options_Names::FEED_ORDER_BY => 'relevance',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret',
            tubepress_api_options_Names::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.search&query=eric+hough&full_response=true&page=1&per_page=20&sort=relevant&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteSearchWithUser()
    {
        $this->expectOptions(array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 20,
            tubepress_api_options_Names::GALLERY_SOURCE => tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SEARCH_VALUE => 'eric hough',
            tubepress_api_options_Names::FEED_ORDER_BY => 'relevance',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret',
            tubepress_api_options_Names::SEARCH_ONLY_USER => 'ehough'
        ));

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.search&query=eric+hough&user_id=ehough&full_response=true&page=1&per_page=20&sort=relevant&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteAppearsIn()
    {
        $this->expectOptions(array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 20,
            tubepress_api_options_Names::GALLERY_SOURCE => tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE => 'eric',
            tubepress_api_options_Names::FEED_ORDER_BY => 'oldest',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getAppearsIn&user_id=eric&full_response=true&page=1&per_page=20&sort=oldest&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteLikes()
    {
        $this->expectOptions(array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 20,
            tubepress_api_options_Names::GALLERY_SOURCE => tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_LIKES_VALUE => 'eric',
            tubepress_api_options_Names::FEED_ORDER_BY => 'rating',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getLikes&user_id=eric&full_response=true&page=1&per_page=20&sort=most_liked&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteUploadedBy()
    {
        $this->expectOptions(array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 20,
            tubepress_api_options_Names::GALLERY_SOURCE => tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE => 'eric',
            tubepress_api_options_Names::FEED_ORDER_BY => 'commentCount',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getUploaded&user_id=eric&full_response=true&page=1&per_page=20&sort=most_commented&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testGetTotalResultCount()
    {
        $rawFeed = $this->_gallerySerializedPhp();
        $this->_sut->onAnalysisStart($rawFeed);
        $actual = $this->_sut->getTotalResultCount($rawFeed);
        $this->assertEquals(526, $actual);
    }


    public function testVimeoError()
    {
        $this->setExpectedException('RuntimeException', 'Invalid consumer key');
        $rawFeed = $this->_errorSerializedPhp();
        $this->_sut->onAnalysisStart($rawFeed);
    }

    public function testCountElements()
    {
        $rawFeed = $this->_gallerySerializedPhp();
        $this->_sut->onAnalysisStart($rawFeed);
        $actual = $this->_sut->getCurrentResultCount($rawFeed);
        $this->_sut->onAnalysisComplete($rawFeed);
        $this->assertEquals(16, $actual);
    }

    private function expectOptions($arr) {

        foreach ($arr as $key => $value) {

            $this->_mockExecutionContext->shouldReceive('get')->with($key)->andReturn($value);
        }
    }


    private function _errorSerializedPhp()
    {
        return $this->_sanitizedSerialized('vimeo-error.txt');
    }

    private function _gallerySerializedPhp()
    {
        return $this->_sanitizedSerialized('vimeo-gallery.txt');
    }

    private function _sanitizedSerialized($filename)
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/tests/unit/add-ons/provider-vimeo-v2/resources/' . $filename);

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array(get_class($this), '_callbackStrlen'), $serial_str );

        return $out;
    }

    public function _callbackStrlen($matches)
    {
        $toReturn = "s:" . strlen($matches[2]) . ":\"" . $matches[2] . "\";";

        return $toReturn;
    }
}
