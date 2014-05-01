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
 * @covers tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder
 */
class tubepress_test_addons_youtube_impl_feed_urlbuilding_YouTubeUrlBuilderCommandTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder
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
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher  = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockUrlFactory = $this->createMockSingletonService(tubepress_api_url_UrlFactoryInterface::_);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(20);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_addons_youtube_api_const_options_names_Feed::FILTER)->andReturn('moderate');
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_addons_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY)->andReturn('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $this->_sut                  = new tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder($this->_mockUrlFactory);
    }

    public function testSingleVideoUrl()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_SINGLE);

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockQuery->shouldReceive('set')->once()->with('v', 2);
        $mockQuery->shouldReceive('set')->once()->with('key', 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos/dfsdkjerufd')->andReturn($mockUrl);

        $this->assertSame($mockUrl, $this->_sut->buildSingleVideoUrl('dfsdkjerufd'));
    }

    public function testexecuteUserMode()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
           'userValue' => '3hough'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);

        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testexecutePopular()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
           'youtubeMostPopularValue' => 'today'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/standardfeeds/most_popular?time=today')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testexecutePlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
           tubepress_api_const_options_names_Feed::ORDER_BY => 'relevance',
           'playlistValue' => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteFavorites()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
           'favoritesValue' => 'mrdeathgod'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteTagWithDoubleQuotes()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE      => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => '"stewart daily" -show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=%22stewart+daily%22+-show')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));

    }

    public function testexecuteTagWithExclusion()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE      => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => 'stewart daily -show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+-show')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteTagWithPipes()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE      => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => 'stewart|daily|show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart%7Cdaily%7Cshow')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteTag()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           'tagValue' => 'stewart daily show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteTagWithUser()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
            tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '3hough',
            'tagValue' => 'stewart daily show'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&author=3hough')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testNewestSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => '3hough',
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::NEWEST
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'published');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testNewestSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'published');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testViewsSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => '3hough',
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::VIEW_COUNT
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));

    }

    public function testViewsSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery);

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testRelevanceSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => '3hough',
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RELEVANCE
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'relevance');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));

    }

    public function testRelevanceSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testRatingSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => '3hough',
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RATING
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'rating');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testRatingSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RATING,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));

    }

    public function testPositionSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => '3hough',
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::POSITION
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));

    }

    public function testPositionSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::POSITION,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'position');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));

    }

    public function testCommentsSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => '3hough',
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testCommentsSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'commentCount');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testDurationSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => '3hough',
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::DURATION
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testDurationSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::DURATION,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'duration');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testRevPositionSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => '3hough',
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::REV_POSITION
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));

    }

    public function testRevPositionSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::REV_POSITION,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'reversedPosition');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testTitleSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => '3hough',
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::TITLE
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/users/3hough/uploads')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'NONE');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
    }

    public function testTitleSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::TITLE,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->times(3)->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35')->andReturn($mockUrl);
        $this->_standardPostProcessingStuff($mockQuery, 'title');

        $this->assertSame($mockUrl, $this->_sut->buildGalleryUrl(1));
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

    private function _setupEventDispatcher($evenName)
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with($evenName, ehough_mockery_Mockery::on(function ($event) {

            return $event->getSubject() instanceof tubepress_api_url_UrlInterface;
        }));
    }
}



