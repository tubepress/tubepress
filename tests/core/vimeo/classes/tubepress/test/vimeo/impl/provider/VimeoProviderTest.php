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
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockItemSorter;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockUrlFactory       = $this->mock(tubepress_core_url_api_UrlFactoryInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockItemSorter       = $this->mock(tubepress_core_media_provider_api_ItemSorterInterface::_);

        $this->_sut = new tubepress_vimeo_impl_provider_VimeoVideoProvider(

            $this->_mockLogger,
            $this->_mockUrlFactory,
            $this->_mockExecutionContext,
            $this->_mockItemSorter
        );
    }

    public function testGetName()
    {
        $this->assertEquals('vimeo', $this->_sut->getName());
    }

    public function testRecognizesVideoId()
    {
        $this->assertTrue($this->_sut->recognizesItemId('11111111'));
        $this->assertFalse($this->_sut->recognizesItemId('11111111d'));
        $this->assertFalse($this->_sut->recognizesItemId('dddddddd'));
    }

    public function testSources()
    {
        $this->assertEquals(

            array(

                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CREDITED,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
                tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY
            ),

            $this->_sut->getGallerySourceNames()
        );
    }

    public function testGetfriendlyName()
    {
        $this->assertEquals('Vimeo', $this->_sut->getDisplayName());
    }

    public function testGetAdditionalMetaNames()
    {
        $expected = array(

            tubepress_core_media_item_api_Constants::OPTION_TITLE       => tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE,
            tubepress_core_media_item_api_Constants::OPTION_LENGTH      => tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_FORMATTED,
            tubepress_core_media_item_api_Constants::OPTION_AUTHOR      => tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
            tubepress_core_media_item_api_Constants::OPTION_KEYWORDS    => tubepress_core_media_item_api_Constants::ATTRIBUTE_KEYWORDS_FORMATTED,
            tubepress_core_media_item_api_Constants::OPTION_URL         => tubepress_core_media_item_api_Constants::ATTRIBUTE_HOME_URL,
            tubepress_core_media_item_api_Constants::OPTION_CATEGORY    => tubepress_core_media_item_api_Constants::ATTRIBUTE_CATEGORY_DISPLAY_NAME,

            tubepress_vimeo_api_Constants::OPTION_LIKES                 => tubepress_core_media_item_api_Constants::ATTRIBUTE_LIKES_COUNT,

            tubepress_core_media_item_api_Constants::OPTION_ID          => tubepress_core_media_item_api_Constants::ATTRIBUTE_ID,
            tubepress_core_media_item_api_Constants::OPTION_VIEWS       => tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT,
            tubepress_core_media_item_api_Constants::OPTION_UPLOADED    => tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
            tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION => tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION,
        );

        $actual = $this->_sut->getMapOfMetaOptionNamesToAttributeDisplayNames();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoKeyGallery()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => '',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $this->_sut->buildUrlForSingle(3);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoKeySingleVideo()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => '',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $this->_sut->buildUrlForSingle('xyz');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoSecretGallery()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => ''
        ));

        $this->_sut->buildUrlForPage(3);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoVimeoSecretSingleVideo()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => ''
        ));

        $this->_sut->buildUrlForSingle('xyz');
    }

    public function testSingleVideoUrl()
    {
        $this->expectOptions(array(
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getInfo&video_id=444333&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForSingle('444333');

        $this->assertSame($mockUrl, $result);
    }

    public function testExecuteGroup()
    {
        $this->expectOptions(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE => tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
            tubepress_vimeo_api_Constants::OPTION_VIMEO_GROUP_VALUE => 'eric',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => 'random',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.groups.getItems&group_id=eric&full_response=true&page=1&per_page=20&sort=random&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testExecuteCreditedTo()
    {
        $this->expectOptions(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE => tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CREDITED,
            tubepress_vimeo_api_Constants::OPTION_VIMEO_CREDITED_VALUE => 'eric',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => 'random',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));


        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getAll&user_id=eric&full_response=true&page=1&per_page=20&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteAlbum()
    {
        $this->expectOptions(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE => tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
            tubepress_vimeo_api_Constants::OPTION_VIMEO_ALBUM_VALUE => 'eric',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));


        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.albums.getItems&album_id=eric&full_response=true&page=1&per_page=20&format=php')->andReturn($mockUrl);


        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteChannel()
    {
        $this->expectOptions(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE => tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
            tubepress_vimeo_api_Constants::OPTION_VIMEO_CHANNEL_VALUE => 'eric',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.channels.getItems&channel_id=eric&full_response=true&page=1&per_page=20&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }


    public function testexecuteSearch()
    {
        $this->expectOptions(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE => tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SEARCH_VALUE => 'eric hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => 'relevance',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret',
            tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.search&query=eric+hough&full_response=true&page=1&per_page=20&sort=relevant&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteSearchWithUser()
    {
        $this->expectOptions(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE => tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SEARCH_VALUE => 'eric hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => 'relevance',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret',
            tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER => 'ehough'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.search&query=eric+hough&user_id=ehough&full_response=true&page=1&per_page=20&sort=relevant&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteAppearsIn()
    {
        $this->expectOptions(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE => tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
            tubepress_vimeo_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE => 'eric',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => 'oldest',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getAppearsIn&user_id=eric&full_response=true&page=1&per_page=20&sort=oldest&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteLikes()
    {
        $this->expectOptions(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE => tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
            tubepress_vimeo_api_Constants::OPTION_VIMEO_LIKES_VALUE => 'eric',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => 'rating',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://vimeo.com/api/rest/v2?method=vimeo.videos.getLikes&user_id=eric&full_response=true&page=1&per_page=20&sort=most_liked&format=php')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForPage(1);

        $this->assertSame($mockUrl, $result);
    }

    public function testexecuteUploadedBy()
    {
        $this->expectOptions(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE => tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
            tubepress_vimeo_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE => 'eric',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => 'commentCount',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY => 'vimeokey',
            tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'vimeosecret'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
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
        $this->setExpectedException('RuntimeException', 'Vimeo responded to TubePress with an error: Invalid consumer key');
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
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/tests/core/vimeo/resources/vimeo/' . $filename);

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_test_vimeo_impl_provider_VimeoProviderTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    public function _callbackStrlen($matches)
    {
        $toReturn = "s:" . strlen($matches[2]) . ":\"" . $matches[2] . "\";";

        return $toReturn;
    }
}
