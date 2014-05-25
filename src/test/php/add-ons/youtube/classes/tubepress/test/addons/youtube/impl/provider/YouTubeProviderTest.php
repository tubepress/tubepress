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
class tubepress_test_youtube_impl_provider_YouTubeProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube_impl_provider_YouTubeVideoProvider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpClient;

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
        $this->_mockHttpClient                  = $this->mock(tubepress_core_api_http_HttpClientInterface::_);
        $this->_mockExecutionContext            = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_core_api_event_EventDispatcherInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_api_http_RequestParametersInterface::_);
        $this->_mockUrlFactory = $this->mock(tubepress_core_api_url_UrlFactoryInterface::_);
        $this->_mockLogger = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::RESULTS_PER_PAGE)->andReturn(20);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_youtube_api_const_options_Names::FILTER)->andReturn('moderate');
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_youtube_api_const_options_Names::EMBEDDABLE_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_youtube_api_const_options_Names::DEV_KEY)->andReturn('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');

        $this->_sut = new tubepress_youtube_impl_provider_YouTubeVideoProvider(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockUrlFactory,
            $this->_mockEventDispatcher
        );
    }

    public function testGetName()
    {
        $this->assertEquals('youtube', $this->_sut->getName());
    }

    public function testRecognizesVideoId()
    {
        $this->assertTrue($this->_sut->singleElementRecognizesId('SJxBZgC29ts'));
        $this->assertTrue($this->_sut->singleElementRecognizesId('S5yDS-mFRy4'));
        $this->assertTrue($this->_sut->singleElementRecognizesId('KcXjhikIz6o'));
        $this->assertTrue($this->_sut->singleElementRecognizesId('T8KJGtMGMSY'));
        $this->assertFalse($this->_sut->singleElementRecognizesId('339494949'));
        $this->assertFalse($this->_sut->singleElementRecognizesId('S5yDS-mFRy]'));
        $this->assertFalse($this->_sut->singleElementRecognizesId('KcXjhikIz'));
        $this->assertFalse($this->_sut->singleElementRecognizesId('T8K..tMGMSY'));
    }

    public function testSources()
    {
        $this->assertEquals(

            array(

                tubepress_youtube_api_const_options_Values::YOUTUBE_MOST_POPULAR,
                tubepress_youtube_api_const_options_Values::YOUTUBE_RELATED,
                tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
                tubepress_youtube_api_const_options_Values::YOUTUBE_FAVORITES,
                tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH,
                tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            ),

            $this->_sut->getGallerySourceNames()
        );
    }

   

    public function singleVideoXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/addons/youtube/youtube-single-video.xml');
    }

    public function galleryXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/addons/youtube/youtube-gallery.xml');
    }

    public function testSingleVideoUrl()
    {

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockQuery->shouldReceive('set')->once()->with('v', 2);
        $mockQuery->shouldReceive('set')->once()->with('key', 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos/dfsdkjerufd')->andReturn($mockUrl);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_SINGLE);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForSingle('dfsdkjerufd'));
    }

    public function testexecuteUserMode()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            'userValue' => '3hough'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);

        $this->_standardPostProcessingStuff($mockQuery);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testexecutePopular()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_MOST_POPULAR,
            'youtubeMostPopularValue' => 'today'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/standardfeeds/most_popular?time=today')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testexecutePlaylist()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => 'relevance',
            'playlistValue' => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testexecuteFavorites()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_FAVORITES,
            'favoritesValue' => 'mrdeathgod'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testexecuteTagWithDoubleQuotes()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE      => tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH,
            tubepress_youtube_api_const_options_Names::YOUTUBE_TAG_VALUE => '"stewart daily" -show',
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=%22stewart+daily%22+-show')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));

    }

    public function testexecuteTagWithExclusion()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE      => tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH,
            tubepress_youtube_api_const_options_Names::YOUTUBE_TAG_VALUE => 'stewart daily -show',
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+-show')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testexecuteTagWithPipes()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE      => tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH,
            tubepress_youtube_api_const_options_Names::YOUTUBE_TAG_VALUE => 'stewart|daily|show',
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart%7Cdaily%7Cshow')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testexecuteTag()
    {

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH,
            'tagValue' => 'stewart daily show',
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testexecuteTagWithUser()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH,
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => '3hough',
            'tagValue' => 'stewart daily show'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&author=3hough')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testNewestSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_NEWEST
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'published');
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testNewestSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_NEWEST,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'published');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testViewsSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_VIEW_COUNT
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));

    }

    public function testViewsSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_VIEW_COUNT,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testRelevanceSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_RELEVANCE
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'relevance');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));

    }

    public function testRelevanceSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_RELEVANCE,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testRatingSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_RATING
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'rating');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testRatingSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_RATING,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');
        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));

    }

    public function testPositionSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_POSITION
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));

    }

    public function testPositionSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_POSITION,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'position');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));

    }

    public function testCommentsSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_COMMENT_COUNT
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testCommentsSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_COMMENT_COUNT,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'commentCount');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testDurationSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_DURATION
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testDurationSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_DURATION,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'duration');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testRevPositionSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_REV_POSITION
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));

    }

    public function testRevPositionSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_REV_POSITION,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'reversedPosition');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testTitleSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_TITLE
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);


        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
    }

    public function testTitleSortOrderPlaylist()
    {

        $this->expectOptions(array(

            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_core_api_const_options_Names::ORDER_BY => tubepress_core_api_const_options_ValidValues::ORDER_BY_TITLE,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'title');

        $this->_setupEventDispatcher($mockUrl, tubepress_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->assertSame($mockUrl, $this->_sut->urlBuildForGallery(1));
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

            $this->_mockExecutionContext->shouldReceive('get')->with($key)->andReturn($value);
        }
    }

    private function _setupEventDispatcher($subject, $eventName)
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->andReturn($subject);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($subject)->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with($eventName, $mockEvent);
    }
}
