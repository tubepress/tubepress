<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommandTest extends TubePressUnitTest
{
    /**
     * @var tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    function onSetup()
    {
        $this->_sut = new tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder();
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(20);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_addons_youtube_api_const_options_names_Feed::FILTER)->andReturn('moderate');
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_addons_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY)->andReturn('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
    }

    function testSingleVideoUrl()
    {
        $this->assertEquals(

            "http://gdata.youtube.com/feeds/api/videos/dfsdkjerufd?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg",
            $this->_sut->buildSingleVideoUrl('dfsdkjerufd')
        );
    }

    function testexecuteUserMode()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
           'userValue' => '3hough'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/uploads?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTopRated()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
           'top_ratedValue' => 'today'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?time=today&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecutePopular()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
           'youtubeMostPopularValue' => 'today'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_popular?time=today&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecutePlaylist()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
           tubepress_api_const_options_names_Feed::ORDER_BY => 'relevance',
           'playlistValue' => 'D2B04665B213AE35'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&orderby=viewCount&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteMostResponded()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteMostRecent()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTopFavorites()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_favorites?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteMostDiscussed()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteFavorites()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
           'favoritesValue' => 'mrdeathgod'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTagWithDoubleQuotes()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE      => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => '"stewart daily" -show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=%22stewart+daily%22+-show&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTagWithExclusion()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE      => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => 'stewart daily -show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+-show&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTagWithPipes()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE      => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => 'stewart|daily|show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart%7Cdaily%7Cshow&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTag()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           'tagValue' => 'stewart daily show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTagWithUser()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
            tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '3hough',
            'tagValue' => 'stewart daily show'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&author=3hough&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteFeatured()
    {
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');

        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testNewestSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::NEWEST
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff('published'),
            $this->_sut->buildGalleryUrl(1));
    }

    function testNewestSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('published'),
            $this->_sut->buildGalleryUrl(1));
    }

    function testViewsSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::VIEW_COUNT
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testViewsSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testRelevanceSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RELEVANCE
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff('relevance'),
            $this->_sut->buildGalleryUrl(1));
    }

    function testRelevanceSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    function testRatingSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RATING
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff('rating'),
            $this->_sut->buildGalleryUrl(1));
    }

    function testRatingSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::RATING,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    function testPositionSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::POSITION
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    function testPositionSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::POSITION,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('position'),
            $this->_sut->buildGalleryUrl(1));
    }

    function testCommentsSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    function testCommentsSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('commentCount'),
            $this->_sut->buildGalleryUrl(1));
    }

    function testDurationSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::DURATION
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    function testDurationSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::DURATION,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('duration'),
            $this->_sut->buildGalleryUrl(1));
    }

    function testRevPositionSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::REV_POSITION
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    function testRevPositionSortOrderPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::REV_POSITION,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'D2B04665B213AE35'
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?" . $this->_standardPostProcessingStuff('reversedPosition'),
            $this->_sut->buildGalleryUrl(1));
    }

    function testTitleSortOrderNonPlaylist()
    {
        $this->expectOptions(array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_api_const_options_values_OrderByValue::TITLE
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    function testTitleSortOrderPlaylist()
    {
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
}



