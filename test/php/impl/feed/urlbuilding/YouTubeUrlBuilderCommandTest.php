<?php

require_once BASE . '/sys/classes/org/tubepress/impl/feed/urlbuilding/YouTubeUrlBuilderCommand.class.php';

class org_tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommandTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommand();

        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc           = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $pc->shouldReceive('calculateProviderOfVideoId')->zeroOrMoreTimes()->andReturn(org_tubepress_api_provider_Provider::YOUTUBE);

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->zeroOrMoreTimes()->with(org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(20);
        $execContext->shouldReceive('get')->zeroOrMoreTimes()->with(org_tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn('viewCount');
        $execContext->shouldReceive('get')->zeroOrMoreTimes()->with(org_tubepress_api_const_options_names_Feed::FILTER)->andReturn('moderate');
        $execContext->shouldReceive('get')->zeroOrMoreTimes()->with(org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY)->andReturn(true);
        $execContext->shouldReceive('get')->zeroOrMoreTimes()->with(org_tubepress_api_const_options_names_Feed::DEV_KEY)->andReturn('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
    }

    function testSingleVideoUrl()
    {
        self::assertEquals("http://gdata.youtube.com/feeds/api/videos/dfsdkjerufd?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg",
        $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, true, 'dfsdkjerufd')));
    }

    function testexecuteUserMode()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
           'userValue' => '3hough'
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/uploads?" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteTopRated()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
           'top_ratedValue' => 'today'
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?time=today&" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecutePopular()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_VIEWED,
           'most_viewedValue' => 'today'
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed?time=today&" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecutePlaylist()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
           org_tubepress_api_const_options_names_Feed::ORDER_BY => 'relevance',
           'playlistValue' => 'D2B04665B213AE35'
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&orderby=viewCount&safeSearch=moderate&format=5",
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteMostResponded()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteMostRecent()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteTopFavorites()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_favorites?" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteMostDiscussed()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteFavorites()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
           'favoritesValue' => 'mrdeathgod'
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites?" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteTagWithDoubleQuotes()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE      => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => '"stewart daily" -show',
        org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/videos?q=%22stewart+daily%22+-show&" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteTagWithExclusion()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE      => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => 'stewart daily -show',
        org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+-show&" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteTagWithPipes()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE      => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => 'stewart|daily|show',
        org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart%7Cdaily%7Cshow&" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteTag()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
           'tagValue' => 'stewart daily show',
        org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteTagWithUser()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
            org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '3hough',
            'tagValue' => 'stewart daily show'
        ));
        self::assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart+daily+show&author=3hough&" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    function testexecuteFeatured()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED
        ));

        self::assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(),
            $this->exec(self::_buildContext(org_tubepress_api_provider_Provider::YOUTUBE, false, 1)));
    }

    private function exec($context)
    {

        self::assertTrue($this->_sut->execute($context));
        return $context->returnValue;
    }

    private function _standardPostProcessingStuff()
    {
        return "v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&orderby=viewCount&safeSearch=moderate&format=5";
    }

    private function _expectOptions($opts)
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        foreach ($opts as $name => $value) {
            $execContext->shouldReceive('get')->zeroOrMoreTimes()->with($name)->andReturn($value);
        }
    }

    private static function _buildContext($provider, $single, $arg)
    {
        $context = new stdClass();
        $context->arg = $arg;
        $context->single = $single;
        $context->providerName = $provider;
        return $context;
    }
}



