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
        $this->_mockHttpClient                  = $this->mock(tubepress_core_http_api_HttpClientInterface::_);
        $this->_mockExecutionContext            = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);
        $this->_mockUrlFactory = $this->mock(tubepress_core_url_api_UrlFactoryInterface::_);
        $this->_mockLogger = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE)->andReturn(20);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_youtube_api_Constants::OPTION_FILTER)->andReturn('moderate');
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_youtube_api_Constants::OPTION_EMBEDDABLE_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_youtube_api_Constants::OPTION_DEV_KEY)->andReturn('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');

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
        $this->assertTrue($this->_sut->recognizesItemId('SJxBZgC29ts'));
        $this->assertTrue($this->_sut->recognizesItemId('S5yDS-mFRy4'));
        $this->assertTrue($this->_sut->recognizesItemId('KcXjhikIz6o'));
        $this->assertTrue($this->_sut->recognizesItemId('T8KJGtMGMSY'));
        $this->assertFalse($this->_sut->recognizesItemId('339494949'));
        $this->assertFalse($this->_sut->recognizesItemId('S5yDS-mFRy]'));
        $this->assertFalse($this->_sut->recognizesItemId('KcXjhikIz'));
        $this->assertFalse($this->_sut->recognizesItemId('T8K..tMGMSY'));
    }

    public function testSources()
    {
        $this->assertEquals(

            array(

                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
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
        return file_get_contents(TUBEPRESS_ROOT . '/tests/resources/fixtures/addons/youtube/youtube-gallery.xml');
    }

    public function testSingleVideoUrl()
    {

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockQuery->shouldReceive('set')->once()->with('v', 2);
        $mockQuery->shouldReceive('set')->once()->with('key', 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos/dfsdkjerufd')->andReturn($mockUrl);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForSingle('dfsdkjerufd'));
    }

    public function testexecuteUserMode()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            'userValue' => '3hough'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);

        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testexecutePopular()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
            'youtubeMostPopularValue' => 'today'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/standardfeeds/most_popular?time=today')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testexecutePlaylist()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => 'relevance',
            'playlistValue' => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testexecuteFavorites()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
            'favoritesValue' => 'mrdeathgod'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testexecuteTagWithDoubleQuotes()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE      => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_TAG_VALUE => '"stewart daily" -show',
            tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=%22stewart+daily%22+-show')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));

    }

    public function testexecuteTagWithExclusion()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE      => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_TAG_VALUE => 'stewart daily -show',
            tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+-show')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testexecuteTagWithPipes()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE      => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_TAG_VALUE => 'stewart|daily|show',
            tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart%7Cdaily%7Cshow')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testexecuteTag()
    {

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
            'tagValue' => 'stewart daily show',
            tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER => '',
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testexecuteTagWithUser()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
            tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER => '3hough',
            'tagValue' => 'stewart daily show'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&author=3hough')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testNewestSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_NEWEST
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'published');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testNewestSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_NEWEST,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'published');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testViewsSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_VIEW_COUNT
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));

    }

    public function testViewsSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_VIEW_COUNT,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testRelevanceSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_RELEVANCE
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'relevance');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));

    }

    public function testRelevanceSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_RELEVANCE,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testRatingSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_RATING
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'rating');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testRatingSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_RATING,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));

    }

    public function testPositionSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_POSITION
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));

    }

    public function testPositionSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_POSITION,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'position');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));

    }

    public function testCommentsSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_COMMENT_COUNT
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testCommentsSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_COMMENT_COUNT,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'commentCount');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testDurationSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_DURATION
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testDurationSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_DURATION,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'duration');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testRevPositionSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_REV_POSITION
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));

    }

    public function testRevPositionSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_REV_POSITION,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'reversedPosition');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testTitleSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE => '3hough',
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_TITLE
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
    }

    public function testTitleSortOrderPlaylist()
    {

        $this->expectOptions(array(

            tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
            tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY => tubepress_youtube_api_Constants::ORDER_BY_TITLE,
            tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'title');

        $this->assertSame($mockUrl, $this->_sut->buildUrlForPage(1));
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
}
