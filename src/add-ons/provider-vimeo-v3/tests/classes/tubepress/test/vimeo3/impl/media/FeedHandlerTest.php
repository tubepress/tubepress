<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_vimeo3_impl_media_FeedHandler
 */
class tubepress_test_vimeo3_impl_media_FeedHandlerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo3_impl_media_FeedHandler
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockUrlFactory       = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_sut = new tubepress_vimeo3_impl_media_FeedHandler(

            $this->_mockLogger, $this->_mockUrlFactory, $this->_mockExecutionContext);

        $this->_sut->__invoke();
    }

    public function testSingleVideoUrl()
    {
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://api.vimeo.com')->andReturn($mockUrl);
        $mockUrl->shouldReceive('addPath')->once()->with('videos')->andReturn($mockUrl);
        $mockUrl->shouldReceive('addPath')->once()->with('444333')->andReturn($mockUrl);

        $result = $this->_sut->buildUrlForItem('444333');

        $this->assertSame($mockUrl, $result);
    }

    public function testGetTotalResultCount()
    {
        $rawFeed = $this->_getVideoListJson();

        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);

        $this->_sut->onAnalysisStart($rawFeed, $mockUrl);

        $actual = $this->_sut->getTotalResultCount();

        $this->assertEquals(9692, $actual);
    }

    public function testGetName()
    {
        $this->assertEquals('vimeo_v3', $this->_sut->getName());
    }

    /**
     * @dataProvider getDataGalleryUrl
     */
    public function testGetGalleryUrl($mode, $modeValueOptionName, $modeValue, array $pathSegments, $requestedSort, $sortParams)
    {
        $mockUrl   = $this->mock('tubepress_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_api_url_QueryInterface');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://api.vimeo.com')->andReturn($mockUrl);

        foreach ($pathSegments as $pathSegment) {

            $mockUrl->shouldReceive('addPath')->once()->with($pathSegment)->andReturn($mockUrl);
        }

        $mockUrl->shouldReceive('getQuery')->atLeast(1)->andReturn($mockQuery);

        $mockQuery->shouldReceive('set')->once()->with('page', 3)->andReturn($mockQuery);
        $mockQuery->shouldReceive('set')->once()->with('per_page', 44)->andReturn($mockQuery);

        foreach ($sortParams as $sortParam) {

            $mockQuery->shouldReceive('set')->once()->with($sortParam[0], $sortParam[1])->andReturn($mockQuery);
        }

        $this->_mockExecutionContext->shouldReceive('get')->twice()->with(tubepress_api_options_Names::GALLERY_SOURCE)->andReturn($mode);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with($modeValueOptionName)->andReturn($modeValue);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::FEED_RESULTS_PER_PAGE)->andReturn(44);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::FEED_ORDER_BY)->andReturn($requestedSort);

        if ($mode === tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH) {

            $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::SEARCH_ONLY_USER)->andReturnNull();
        }

        $actual = $this->_sut->buildUrlForPage(3);

        $this->assertSame($mockUrl, $actual);
    }

    public function getDataGalleryUrl()
    {
        return array(

            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_ALBUM_VALUE,
                'some-album',
                array('albums', 'some-album', 'videos'),
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST,
                array(array('sort', 'date'), array('direction', 'desc')),
            ),
            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE,
                'some-user-id',
                array('users', 'some-user-id', 'appearances'),
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST,
                array(array('sort', 'date'), array('direction', 'desc')),
            ),
            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CATEGORY,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_CATEGORY_VALUE,
                'some-category-id',
                array('categories', 'some-category-id', 'videos'),
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST,
                array(array('sort', 'date'), array('direction', 'desc')),
            ),
            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_CHANNEL_VALUE,
                'some-channel-id',
                array('channels', 'some-channel-id', 'videos'),
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST,
                array(array('sort', 'date'), array('direction', 'desc')),
            ),
            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_GROUP_VALUE,
                'some-group-id',
                array('groups', 'some-group-id', 'videos'),
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST,
                array(array('sort', 'date'), array('direction', 'desc')),
            ),
            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_TAG,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_TAG_VALUE,
                'some-tag-id',
                array('tags', 'some-tag-id', 'videos'),
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST,
                array(array('sort', 'created_time'), array('direction', 'desc')),
            ),
            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_LIKES_VALUE,
                'some-user-id',
                array('users', 'some-user-id', 'likes'),
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST,
                array(array('sort', 'date'), array('direction', 'desc')),
            ),
            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE,
                'some-user-id',
                array('users', 'some-user-id', 'videos'),
                tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST,
                array(array('sort', 'date'), array('direction', 'desc')),
            ),
            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE,
                'some-user-id',
                array('users', 'some-user-id', 'videos'),
                tubepress_vimeo3_api_Constants::ORDER_BY_DEFAULT,
                array(array('sort', 'default')),
            ),
            array(
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_SEARCH_VALUE,
                'search terms',
                array('videos'),
                tubepress_vimeo3_api_Constants::ORDER_BY_DEFAULT,
                array(array('query', 'search terms'), array('sort', 'relevant')),
            ),
        );
    }

    public function testVimeoError()
    {
        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);


        $this->setExpectedException('RuntimeException', 'The requested group could not be found');
        $rawFeed = $this->_getVimeoErrorJson();
        $this->_sut->onAnalysisStart($rawFeed, $mockUrl);
    }

    public function testCountElements()
    {
        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);

        $rawFeed = $this->_getVideoListJson();
        $this->_sut->onAnalysisStart($rawFeed, $mockUrl);
        $actual = $this->_sut->getCurrentResultCount();
        $this->_sut->onAnalysisComplete();
        $this->assertEquals(25, $actual);
    }

    public function testGetItemIdAtIndex()
    {
        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);

        $rawFeed = $this->_getVideoListJson();
        $this->_sut->onAnalysisStart($rawFeed, $mockUrl);
        $actual = $this->_sut->getIdForItemAtIndex(3);
        $this->_sut->onAnalysisComplete();
        $this->assertEquals(148268680, $actual);
    }

    public function testNewItemArgs()
    {
        $mockMediaItem = $this->mock('tubepress_api_media_MediaItem');
        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);

        $rawFeed = $this->_getVideoListJson();
        $this->_sut->onAnalysisStart($rawFeed, $mockUrl);
        $actual = $this->_sut->getNewItemEventArguments($mockMediaItem, 3);
        $this->_sut->onAnalysisComplete();
        $this->assertTrue(is_array($actual));
    }

    private function expectOptions($arr) {

        foreach ($arr as $key => $value) {

            $this->_mockExecutionContext->shouldReceive('get')->with($key)->andReturn($value);
        }
    }

    private function _getVimeoErrorJson()
    {
        return $this->_getFileContents('vimeo-error.json');
    }

    private function _getVideoListJson()
    {
        return $this->_getFileContents('video-list.json');
    }

    private function _getFileContents($filename)
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/add-ons/provider-vimeo-v3/tests/fixtures/' . $filename);
    }
}
