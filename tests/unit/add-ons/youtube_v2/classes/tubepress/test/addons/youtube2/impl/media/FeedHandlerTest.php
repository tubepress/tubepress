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
 * @covers tubepress_youtube2_impl_media_FeedHandler
 */
class tubepress_test_youtube2_impl_media_FeedHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube2_impl_media_FeedHandler
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockContext    = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockUrlFactory = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockLogger     = $this->mock(tubepress_platform_api_log_LoggerInterface::_);

        $this->_mockContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE)->andReturn(20);
        $this->_mockContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_youtube2_api_Constants::OPTION_FILTER)->andReturn('moderate');
        $this->_mockContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_youtube2_api_Constants::OPTION_EMBEDDABLE_ONLY)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_youtube2_api_Constants::OPTION_DEV_KEY)->andReturn('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');

        $this->_sut = new tubepress_youtube2_impl_media_FeedHandler(

            $this->_mockLogger,
            $this->_mockContext,
            $this->_mockUrlFactory
        );
    }

    public function singleVideoXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/addons/youtube_v2/youtube-single-video.xml');
    }

    public function galleryXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/tests/unit/add-ons/youtube_v2/resources/youtube/youtube-gallery.xml');
    }

    public function testGetTotalResultCount()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $galleryXml = file_get_contents(TUBEPRESS_ROOT . "/tests/unit/add-ons/youtube_v2/resources/youtube/youtube-gallery.xml");

        $this->_sut->onAnalysisStart($galleryXml);

        $actualCurrentResultCount = $this->_sut->getTotalResultCount();

        $this->assertEquals(153, $actualCurrentResultCount);

        $this->_sut->onAnalysisComplete();
    }

    public function testGetNewItemEventArguments()
    {
        $mockMediaItem = $this->mock('tubepress_app_api_media_MediaItem');

        $actual   = $this->_sut->getNewItemEventArguments($mockMediaItem, 4);
        $expected = array(
            'domDocument'    => null,
            'xpath'          => null,
            'zeroBasedIndex' => 4
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getDataBasicAnalysis
     */
    public function testBasicAnalysis($source, $expectedCurrentResultCount, $firstItemId)
    {
        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $galleryXml = file_get_contents(TUBEPRESS_ROOT . "/tests/unit/add-ons/youtube_v2/resources/youtube/youtube-$source.xml");

        $this->_sut->onAnalysisStart($galleryXml);

        $actualCurrentResultCount = $this->_sut->getCurrentResultCount();
        $actualFirstItemId        = $this->_sut->getIdForItemAtIndex(0);

        $this->assertEquals($expectedCurrentResultCount, $actualCurrentResultCount);
        $this->assertEquals($firstItemId, $actualFirstItemId);

        $this->_sut->onAnalysisComplete();
    }

    public function getDataBasicAnalysis()
    {
        return array(

            array('gallery', 16, 'Ek0SgwWmF9w'),
            array('single-video', 1, '0rXmuhWrlj4'),
        );
    }

    public function testSingleVideoUrl()
    {
        $mockUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_platform_api_url_QueryInterface');
        $mockQuery->shouldReceive('set')->once()->with('v', 2);
        $mockQuery->shouldReceive('set')->once()->with('key', 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos/dfsdkjerufd')->andReturn($mockUrl);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForItem('dfsdkjerufd'));
    }

    /**
     * @dataProvider getDataSimpleUrlBuilding
     */
    public function testSimpleUrlBuilding($orderBy, $expectedOptions, $expectedUrlAsString, $expectedOrder = 'viewCount')
    {
        $this->_mockContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_app_api_options_Names::FEED_ORDER_BY)->andReturn($orderBy);

        $this->expectOptions($expectedOptions);

        $mockUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_platform_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with($expectedUrlAsString)->andReturn($mockUrl);

        $this->_standardPostProcessingStuff($mockQuery, $expectedOrder);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function getDataSimpleUrlBuilding()
    {
        return array(

            array('viewCount', array(
                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                'userValue' => '3hough'
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads'),

            array('viewCount', array(
                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
                'youtubeMostPopularValue' => 'today'
            ), 'http://gdata.youtube.com/feeds/api/standardfeeds/most_popular?time=today'),

            array('viewCount', array(
                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => 'relevance',
                'playlistValue' => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35'),

            array('viewCount', array(
                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
                'favoritesValue' => 'mrdeathgod'
            ), 'http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites'),

            array('viewCount', array(
                tubepress_app_api_options_Names::GALLERY_SOURCE      => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_TAG_VALUE => '"stewart daily" -show',
                tubepress_app_api_options_Names::SEARCH_ONLY_USER => '',
            ), 'http://gdata.youtube.com/feeds/api/videos?q=%22stewart+daily%22+-show'),

            array('viewCount', array(
                tubepress_app_api_options_Names::GALLERY_SOURCE      => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_TAG_VALUE => 'stewart daily -show',
                tubepress_app_api_options_Names::SEARCH_ONLY_USER => '',
            ), 'http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+-show'),

            array('viewCount', array(
                tubepress_app_api_options_Names::GALLERY_SOURCE      => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_TAG_VALUE => 'stewart|daily|show',
                tubepress_app_api_options_Names::SEARCH_ONLY_USER => '',
            ), 'http://gdata.youtube.com/feeds/api/videos?q=stewart%7Cdaily%7Cshow'),

            array('viewCount', array(
                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                'tagValue' => 'stewart daily show',
                tubepress_app_api_options_Names::SEARCH_ONLY_USER => '',
            ), 'http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show'),

            array('viewCount', array(
                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_app_api_options_Names::SEARCH_ONLY_USER => '3hough',
                'tagValue' => 'stewart daily show'
            ), 'http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&author=3hough'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_NEWEST, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_NEWEST
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads', 'published'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_NEWEST, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_NEWEST,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35', 'published'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_VIEW_COUNT, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_VIEW_COUNT
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_VIEW_COUNT, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_VIEW_COUNT,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_RELEVANCE, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_RELEVANCE
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads', 'relevance'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_RELEVANCE, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_RELEVANCE,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35', 'NONE'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_RATING, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_RATING
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads', 'rating'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_RATING, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_RATING,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35', 'NONE'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_POSITION, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_POSITION
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads', 'NONE'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_POSITION, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_POSITION,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35', 'position'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_COMMENT_COUNT, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_COMMENT_COUNT
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads', 'NONE'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_COMMENT_COUNT, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_COMMENT_COUNT,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35', 'commentCount'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_DURATION, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_DURATION
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads', 'NONE'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_DURATION, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_DURATION,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35', 'duration'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_REV_POSITION, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_REV_POSITION
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads', 'NONE'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_REV_POSITION, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_REV_POSITION,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35', 'reversedPosition'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_TITLE, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_TITLE
            ), 'http://gdata.youtube.com/feeds/api/users/3hough/uploads', 'NONE'),

            array(tubepress_youtube2_api_Constants::ORDER_BY_TITLE, array(

                tubepress_app_api_options_Names::GALLERY_SOURCE => tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_app_api_options_Names::FEED_ORDER_BY => tubepress_youtube2_api_Constants::ORDER_BY_TITLE,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
            ), 'http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35', 'title'),
        );
    }

    private function _standardPostProcessingStuff(ehough_mockery_mockery_MockInterface $mockQuery, $order = 'viewCount')
    {
        $mockQuery->shouldReceive('set')->once()->with('v', 2);
        $mockQuery->shouldReceive('set')->once()->with('start-index', 1);
        $mockQuery->shouldReceive('set')->once()->with('max-results', 20);

        if ($order !== 'NONE') {

            $mockQuery->shouldReceive('set')->once()->with('orderby', $order);
        }

        $mockQuery->shouldReceive('set')->once()->with('safeSearch', 'moderate');
        $mockQuery->shouldReceive('set')->once()->with('format', 5);
        $mockQuery->shouldReceive('set')->once()->with('key', 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');


        return "&max-results=20&orderby=$order&safeSearch=moderate&format=5";
    }

    private function expectOptions($arr) {

        foreach ($arr as $key => $value) {

            $this->_mockContext->shouldReceive('get')->with($key)->andReturn($value);
        }
    }
}
