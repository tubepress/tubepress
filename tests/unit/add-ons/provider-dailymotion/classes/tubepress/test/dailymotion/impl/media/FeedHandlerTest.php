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
 * @covers tubepress_dailymotion_impl_media_FeedHandler
 */
class tubepress_test_dailymotion_impl_media_FeedHandlerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_dailymotion_impl_media_FeedHandler
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockApiUtility;

    public function onSetup()
    {
        $this->_mockLogger     = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockContext    = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockApiUtility = $this->mock('tubepress_dailymotion_impl_dmapi_ApiUtility');

        $this->_sut = new tubepress_dailymotion_impl_media_FeedHandler(

            $this->_mockLogger,
            $this->_mockContext,
            $this->_mockUrlFactory,
            new tubepress_array_impl_ArrayReader(),
            $this->_mockApiUtility
        );
    }

    /**
     * @dataProvider getDataUnableToUseVideo
     */
    public function testUnableToUseVideo($filename, $message)
    {
        $content = file_get_contents(__DIR__ . '/../../../../../../fixtures/feeds/' . $filename);

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockApiUtility->shouldReceive('checkForApiResponseError')->once();

        $this->_sut->onAnalysisStart($content);

        $actual = $this->_sut->getReasonUnableToUseItemAtIndex(0);
        $this->assertEquals($message, $actual);

        $this->_sut->onAnalysisComplete();
    }

    public function getDataUnableToUseVideo()
    {
        return array(
            array('access_error.json', 'Hi there'),
            array('no_embed.json', 'This video cannot be embedded outside of Dailymotion'),
            array('not_published.json', 'This video has not yet been published'),
            array('private.json', 'This video is private and TubePress does not have access to it'),
        );
    }

    public function testFeed()
    {
        $content = file_get_contents(__DIR__ . '/../../../../../../fixtures/feeds/simple.json');

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockApiUtility->shouldReceive('checkForApiResponseError')->once();

        $this->_sut->onAnalysisStart($content);

        $actualTotal = $this->_sut->getTotalResultCount();
        $this->assertEquals(65090348, $actualTotal);

        $actualThis = $this->_sut->getCurrentResultCount();
        $this->assertEquals(10, $actualThis);

        $actualId = $this->_sut->getIdForItemAtIndex(1);
        $this->assertEquals('x3pb97a', $actualId);

        $this->_sut->onAnalysisComplete();
    }

    public function testBuildUrlForItem()
    {
        $mockUrl   = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockQuery = $this->mock('tubepress_api_url_QueryInterface');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()
            ->with('https://api.dailymotion.com')->andReturn($mockUrl);

        $mockUrl->shouldReceive('addPath')->once()->with('video')->andReturn($mockUrl);
        $mockUrl->shouldReceive('addPath')->once()->with('abc')->andReturn($mockUrl);
        $mockUrl->shouldReceive('getQuery')->atLeast(1)->andReturn($mockQuery);

        $context = array(
            tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER => true,
            tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE => 'ja',
            tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO => 'square',
            tubepress_api_options_Names::HTML_HTTPS => true,
        );

        $query = array(
            'family_filter' => 'on',
            'ssl_assets' => 'on',
            'localization' => 'ja',
            'thumbnail_ratio' => 'square',
            'fields' => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total'
        );

        foreach ($query as $key => $value) {

            $mockQuery->shouldReceive('set')->once()->with($key, $value)->andReturn($mockQuery);
        }

        foreach ($context as $key => $value) {

            $this->_mockContext->shouldReceive('get')->once()->with($key)->andReturn($value);
        }

        $actual = $this->_sut->buildUrlForItem('abc');

        $this->assertSame($mockUrl, $actual);
    }

    /**
     * @dataProvider getDataBuildUrlForPage
     */
    public function testBuildUrlForPage(array $ctx, array $path, array $query)
    {
        $mockUrl   = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockQuery = $this->mock('tubepress_api_url_QueryInterface');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()
            ->with('https://api.dailymotion.com')->andReturn($mockUrl);

        $mockUrl->shouldReceive('getQuery')->atLeast(1)->andReturn($mockQuery);

        foreach ($ctx as $optionName => $value) {

            $times = $optionName === tubepress_api_options_Names::GALLERY_SOURCE ? 3 : 1;

            $this->_mockContext->shouldReceive('get')->times($times)->with($optionName)->andReturn($value);
        }

        foreach ($path as $pathElement) {

            $mockUrl->shouldReceive('addPath')->once()->with($pathElement)->andReturn($mockUrl);
        }

        foreach ($query as $key => $value) {

            $mockQuery->shouldReceive('set')->once()->with($key, $value)->andReturn($mockQuery);
        }

        $actual = $this->_sut->buildUrlForPage(33);

        $this->assertSame($mockUrl, $actual);
    }

    public function getDataBuildUrlForPage()
    {
        return array(

            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FAVORITES,
                    tubepress_dailymotion_api_Constants::OPTION_FAVORITES_VALUE         => 'some-user',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER      => false,
                    tubepress_api_options_Names::HTML_HTTPS                             => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE             => 'ja',
                    tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO            => 'widescreen',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'US',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'es',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'pl , ja',
                    tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST                 => 'video1   , video2',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Sports',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Comedy',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => 'onlyLive',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 45,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 145,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => 'onlyNonPremium',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'eric , bob',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'cats playing piano',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'basket, ball',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'tennis, racket',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => 'onlyUserGenerated',

                ),
                array(
                    'user', 'some-user', 'favorites',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'family_filter'     => 'off',
                    'ssl_assets'        => 'on',
                    'localization'      => 'ja',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'thumbnail_ratio'   => 'widescreen',
                    'country'           => 'US',
                    'detected_language' => 'es',
                    'languages'         => 'pl,ja',
                    'exclude_ids'       => 'video1,video2',
                    'genre'             => 'Sports',
                    'nogenre'           => 'Comedy',
                    'longer_than'       => 45,
                    'shorter_than'      => 145,
                    'owners'            => 'eric,bob',
                    'search'            => 'cats playing piano',
                    'strongtags'        => 'basket,ball',
                    'tags'              => 'tennis,racket',
                    'flags'             => 'featured,hd,live,no_premium,ugc',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_USER,
                    tubepress_dailymotion_api_Constants::OPTION_USER_VALUE              => 'some-user',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER      => false,
                    tubepress_api_options_Names::HTML_HTTPS                             => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE             => 'ja',
                    tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO            => 'widescreen',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'US',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'pl',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'en , ja',
                    tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST                 => 'video1   , video2',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Sports',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Comedy',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => false,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => 'onlyNonLive',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 45,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 145,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => 'onlyPremium',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'eric , bob',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'cats playing piano',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'basket, ball',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'tennis, racket',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => 'onlyUserGenerated',

                ),
                array(
                    'user', 'some-user', 'videos',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'family_filter'     => 'off',
                    'ssl_assets'        => 'on',
                    'localization'      => 'ja',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'thumbnail_ratio'   => 'widescreen',
                    'country'           => 'US',
                    'detected_language' => 'pl',
                    'languages'         => 'en,ja',
                    'exclude_ids'       => 'video1,video2',
                    'genre'             => 'Sports',
                    'nogenre'           => 'Comedy',
                    'longer_than'       => 45,
                    'shorter_than'      => 145,
                    'owners'            => 'eric,bob',
                    'search'            => 'cats playing piano',
                    'strongtags'        => 'basket,ball',
                    'tags'              => 'tennis,racket',
                    'flags'             => 'featured,no_live,premium,ugc',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_PLAYLIST,
                    tubepress_dailymotion_api_Constants::OPTION_PLAYLIST_VALUE          => 'some-playlist',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER      => true,
                    tubepress_api_options_Names::HTML_HTTPS                             => false,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE             => 'ja',
                    tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO            => 'square',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'US',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'pl',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'en , ja',
                    tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST                 => 'video1   , video2',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Sports',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Comedy',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => false,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_UPCOMING,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 45,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 145,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PREMIUM_ALL,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'eric , bob',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'cats playing piano',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'basket, ball',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'tennis, racket',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PARTNER_PARTNER_ONLY,

                ),
                array(
                    'playlist', 'some-playlist', 'videos',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'family_filter'     => 'on',
                    'ssl_assets'        => 'off',
                    'localization'      => 'ja',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'thumbnail_ratio'   => 'square',
                    'country'           => 'US',
                    'detected_language' => 'pl',
                    'languages'         => 'en,ja',
                    'exclude_ids'       => 'video1,video2',
                    'genre'             => 'Sports',
                    'nogenre'           => 'Comedy',
                    'longer_than'       => 45,
                    'shorter_than'      => 145,
                    'owners'            => 'eric,bob',
                    'search'            => 'cats playing piano',
                    'strongtags'        => 'basket,ball',
                    'tags'              => 'tennis,racket',
                    'flags'             => 'featured,live_upcoming,partner',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_LIST,
                    tubepress_dailymotion_api_Constants::OPTION_LIST_VALUE              => 'one,  two   , three',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER      => true,
                    tubepress_api_options_Names::HTML_HTTPS                             => false,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE             => 'ja',
                    tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO            => 'square',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'US',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'pl',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'en , ja',
                    tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST                 => 'video1   , video2',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Sports',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Comedy',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => false,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_OFF,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 45,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 145,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PREMIUM_ALL,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'eric , bob',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'cats playing piano',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'basket, ball',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'tennis, racket',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PARTNER_PARTNER_ONLY,

                ),
                array(
                    'videos',
                ),
                array(
                    'ids'               => 'one,two,three',
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'family_filter'     => 'on',
                    'ssl_assets'        => 'off',
                    'localization'      => 'ja',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'thumbnail_ratio'   => 'square',
                    'country'           => 'US',
                    'detected_language' => 'pl',
                    'languages'         => 'en,ja',
                    'exclude_ids'       => 'video1,video2',
                    'genre'             => 'Sports',
                    'nogenre'           => 'Comedy',
                    'longer_than'       => 45,
                    'shorter_than'      => 145,
                    'owners'            => 'eric,bob',
                    'search'            => 'cats playing piano',
                    'strongtags'        => 'basket,ball',
                    'tags'              => 'tennis,racket',
                    'flags'             => 'featured,live_offair,partner',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FEATURED,
                    tubepress_dailymotion_api_Constants::OPTION_FEATURED_VALUE          => 'some-user',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER      => true,
                    tubepress_api_options_Names::HTML_HTTPS                             => false,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE             => 'ja',
                    tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO            => 'square',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'US',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'pl',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'en , ja',
                    tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST                 => 'video1   , video2',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Sports',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Comedy',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => false,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_OFF,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 45,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 145,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PREMIUM_ALL,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'eric , bob',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'cats playing piano',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'basket, ball',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'tennis, racket',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PARTNER_PARTNER_ONLY,

                ),
                array(
                    'user', 'some-user', 'features'
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'family_filter'     => 'on',
                    'ssl_assets'        => 'off',
                    'localization'      => 'ja',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'thumbnail_ratio'   => 'square',
                    'country'           => 'US',
                    'detected_language' => 'pl',
                    'languages'         => 'en,ja',
                    'exclude_ids'       => 'video1,video2',
                    'genre'             => 'Sports',
                    'nogenre'           => 'Comedy',
                    'longer_than'       => 45,
                    'shorter_than'      => 145,
                    'owners'            => 'eric,bob',
                    'search'            => 'cats playing piano',
                    'strongtags'        => 'basket,ball',
                    'tags'              => 'tennis,racket',
                    'flags'             => 'featured,live_offair,partner',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH,
                    tubepress_dailymotion_api_Constants::OPTION_SEARCH_VALUE            => 'some search',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER      => true,
                    tubepress_api_options_Names::HTML_HTTPS                             => false,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE             => 'ja',
                    tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO            => 'square',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'US',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'pl',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'en , ja',
                    tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST                 => 'video1   , video2',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Sports',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Comedy',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => false,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_OFF,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 45,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 145,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PREMIUM_ALL,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'eric , bob',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'cats playing piano',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'basket, ball',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'tennis, racket',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PARTNER_PARTNER_ONLY,

                ),
                array(
                    'videos',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'family_filter'     => 'on',
                    'ssl_assets'        => 'off',
                    'localization'      => 'ja',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'thumbnail_ratio'   => 'square',
                    'country'           => 'US',
                    'detected_language' => 'pl',
                    'languages'         => 'en,ja',
                    'exclude_ids'       => 'video1,video2',
                    'genre'             => 'Sports',
                    'nogenre'           => 'Comedy',
                    'longer_than'       => 45,
                    'shorter_than'      => 145,
                    'owners'            => 'eric,bob',
                    'strongtags'        => 'basket,ball',
                    'tags'              => 'tennis,racket',
                    'flags'             => 'featured,live_offair,partner',
                    'search'            => 'some search',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_RELATED,
                    tubepress_dailymotion_api_Constants::OPTION_RELATED_VALUE           => 'some-video-id',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER      => false,
                    tubepress_api_options_Names::HTML_HTTPS                             => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE             => 'ja',
                    tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO            => 'widescreen',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'US',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'es',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'pl , ja',
                    tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST                 => 'video1   , video2',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Sports',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Comedy',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_ON,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 45,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 145,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => 'onlyNonPremium',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'eric , bob',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'cats playing piano',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'basket, ball',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'tennis, racket',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => 'onlyUserGenerated',

                ),
                array(
                    'video', 'some-video-id', 'related',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'family_filter'     => 'off',
                    'ssl_assets'        => 'on',
                    'localization'      => 'ja',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'thumbnail_ratio'   => 'widescreen',
                    'country'           => 'US',
                    'detected_language' => 'es',
                    'languages'         => 'pl,ja',
                    'exclude_ids'       => 'video1,video2',
                    'genre'             => 'Sports',
                    'nogenre'           => 'Comedy',
                    'longer_than'       => 45,
                    'shorter_than'      => 145,
                    'owners'            => 'eric,bob',
                    'search'            => 'cats playing piano',
                    'strongtags'        => 'basket,ball',
                    'tags'              => 'tennis,racket',
                    'flags'             => 'featured,hd,live_onair,no_premium,ugc',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_TAG,
                    tubepress_dailymotion_api_Constants::OPTION_TAG_VALUE               => 'tag1  , tag2',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER      => false,
                    tubepress_api_options_Names::HTML_HTTPS                             => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE             => 'ja',
                    tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO            => 'widescreen',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'US',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'es',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'pl , ja',
                    tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST                 => 'video1   , video2',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Sports',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Comedy',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => true,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_ON,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 45,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 145,
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => 'onlyNonPremium',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'eric , bob',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'cats playing piano',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'basket, ball',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'tennis, racket',
                    tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => 'onlyUserGenerated',

                ),
                array(
                    'videos',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'family_filter'     => 'off',
                    'ssl_assets'        => 'on',
                    'localization'      => 'ja',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'thumbnail_ratio'   => 'widescreen',
                    'country'           => 'US',
                    'detected_language' => 'es',
                    'languages'         => 'pl,ja',
                    'exclude_ids'       => 'video1,video2',
                    'genre'             => 'Sports',
                    'nogenre'           => 'Comedy',
                    'longer_than'       => 45,
                    'shorter_than'      => 145,
                    'owners'            => 'eric,bob',
                    'search'            => 'cats playing piano',
                    'strongtags'        => 'tag1,tag2',
                    'tags'              => 'tennis,racket',
                    'flags'             => 'featured,hd,live_onair,no_premium,ugc',
                )
            ),
        );
    }

    public function testGetName()
    {
        $this->assertEquals('dailymotion', $this->_sut->getName());
    }
}
