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
class tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommandTest extends tubepress_test_TubePressUnitTest
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

    public function onSetup()
    {
        $this->_sut                  = new tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder();
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher  = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(20);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_addons_youtube_api_const_options_names_Feed::FILTER)->andReturn('moderate');
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_addons_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY)->andReturn('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
    }

    public function testSingleVideoUrl()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_SINGLE);

        $this->assertEquals(

            "http://gdata.youtube.com/feeds/api/videos/dfsdkjerufd?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg",
            $this->_sut->buildSingleVideoUrl('dfsdkjerufd')
        );
    }

    public function testexecuteUserMode()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
           'userValue' => '3hough'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/uploads?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteTopRated()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
           'top_ratedValue' => 'today'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?time=today&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testexecutePopular()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
           'youtubeMostPopularValue' => 'today'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_popular?time=today&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
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
        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&orderby=viewCount&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteMostResponded()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteMostRecent()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteTopFavorites()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_favorites?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteMostDiscussed()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteFavorites()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
           'favoritesValue' => 'mrdeathgod'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
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
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=%22stewart+daily%22+-show&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
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
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+-show&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
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
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart%7Cdaily%7Cshow&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
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
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
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
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&author=3hough&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testexecuteFeatured()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testNewestSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::NEWEST
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff('published'),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testNewestSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('published'),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testViewsSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::VIEW_COUNT
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testViewsSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testRelevanceSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RELEVANCE
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff('relevance'),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testRelevanceSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    public function testRatingSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RATING
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff('rating'),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testRatingSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RATING,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    public function testPositionSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::POSITION
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    public function testPositionSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::POSITION,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('position'),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testCommentsSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    public function testCommentsSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('commentCount'),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testDurationSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::DURATION
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    public function testDurationSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::DURATION,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('duration'),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testRevPositionSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::REV_POSITION
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    public function testRevPositionSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::REV_POSITION,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('reversedPosition'),
            $this->_sut->buildGalleryUrl(1));
    }

    public function testTitleSortOrderNonPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::TITLE
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    public function testTitleSortOrderPlaylist()
    {
        $this->_setupEventDispatcher(tubepress_addons_youtube_api_const_YouTubeEventNames::URL_GALLERY);

        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::TITLE,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('title'),
            $this->_sut->buildGalleryUrl(1));
    }

    private function _standardPostProcessingStuff($order = 'viewCount')
    {
        return "v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&orderby=$order&safeSearch=moderate&format=5";
    }

    private function expectOptions($arr) {

        foreach ($arr as $key => $value) {

            $this->_mockExecutionContext->shouldReceive('get')->with($key)->andReturn($value);
        }
    }

    private function _setupEventDispatcher($evenName)
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with($evenName, ehough_mockery_Mockery::on(function ($event) {

            return $event->getSubject() instanceof ehough_curly_Url;
        }));
    }
}



