<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommandTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder
     */
    private $_sut;

    private $_mockExecutionContext;

    function setUp()
    {
        $this->_sut = new tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder();
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);


        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(20);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_plugins_youtube_api_const_options_names_Feed::FILTER)->andReturn('moderate');
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_plugins_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_plugins_youtube_api_const_options_names_Feed::DEV_KEY)->andReturn('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
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
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
           'userValue' => '3hough'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/uploads?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTopRated()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
           'top_ratedValue' => 'today'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?time=today&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecutePopular()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
           'youtubeMostPopularValue' => 'today'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_popular?time=today&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecutePlaylist()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
           tubepress_api_const_options_names_Feed::ORDER_BY => 'relevance',
           'playlistValue' => 'D2B04665B213AE35'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&orderby=viewCount&safeSearch=moderate&format=5",
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteMostResponded()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteMostRecent()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTopFavorites()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_favorites?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteMostDiscussed()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteFavorites()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
           'favoritesValue' => 'mrdeathgod'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTagWithDoubleQuotes()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE      => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => '"stewart daily" -show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=%22stewart+daily%22+-show&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTagWithExclusion()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE      => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => 'stewart daily -show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+-show&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTagWithPipes()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE      => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => 'stewart|daily|show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart%7Cdaily%7Cshow&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTag()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           'tagValue' => 'stewart daily show',
        tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteTagWithUser()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
            tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '3hough',
            'tagValue' => 'stewart daily show'
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&author=3hough&" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    function testexecuteFeatured()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED
        ));

        $this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(),
            $this->_sut->buildGalleryUrl(1));
    }

    private function _standardPostProcessingStuff()
    {
        return "v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&orderby=viewCount&safeSearch=moderate&format=5";
    }

    private function expectOptions($arr) {

        foreach ($arr as $key => $value) {

            $this->_mockExecutionContext->shouldReceive('get')->with($key)->andReturn($value);
        }
    }
}



