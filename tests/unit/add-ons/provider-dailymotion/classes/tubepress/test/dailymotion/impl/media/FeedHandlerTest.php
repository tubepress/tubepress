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

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);

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

        $mockUrl      = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockUrlClone = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockQuery    = $this->mock('tubepress_api_url_QueryInterface');

        $mockUrl->shouldReceive('getClone')->once()->andReturn($mockUrlClone);

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockApiUtility->shouldReceive('checkForApiResponseError')->once();

        $this->_sut->onAnalysisStart($content, $mockUrl);

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

        $mockUrl      = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockUrlClone = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockQuery    = $this->mock('tubepress_api_url_QueryInterface');

        $mockUrl->shouldReceive('getClone')->once()->andReturn($mockUrlClone);

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockApiUtility->shouldReceive('checkForApiResponseError')->once();

        $this->_sut->onAnalysisStart($content, $mockUrl);

        $actualTotal = $this->_sut->getTotalResultCount();
        $this->assertEquals(65090348, $actualTotal);

        $actualThis = $this->_sut->getCurrentResultCount();
        $this->assertEquals(10, $actualThis);

        $actualId = $this->_sut->getIdForItemAtIndex(1);
        $this->assertEquals('dailymotion_x3pb97a', $actualId);

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


        $query = array(
            'fields' => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total'
        );

        foreach ($query as $key => $value) {

            $mockQuery->shouldReceive('set')->once()->with($key, $value)->andReturn($mockQuery);
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

            $times = $optionName === tubepress_api_options_Names::GALLERY_SOURCE ? 1 : 1;

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
                ),
                array(
                    'user', 'some-user', 'favorites',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_USER,
                    tubepress_dailymotion_api_Constants::OPTION_USER_VALUE              => 'some-user',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,

                ),
                array(
                    'user', 'some-user', 'videos',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_PLAYLIST,
                    tubepress_dailymotion_api_Constants::OPTION_PLAYLIST_VALUE          => 'some-playlist',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,

                ),
                array(
                    'playlist', 'some-playlist', 'videos',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_LIST,
                    tubepress_dailymotion_api_Constants::OPTION_LIST_VALUE              => 'one,  two   , three',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,

                ),
                array(
                    'videos',
                ),
                array(
                    'ids'               => 'one,two,three',
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FEATURED,
                    tubepress_dailymotion_api_Constants::OPTION_FEATURED_VALUE          => 'some-user',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,

                ),
                array(
                    'user', 'some-user', 'features'
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE              => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH,
                    tubepress_dailymotion_api_Constants::OPTION_SEARCH_VALUE => 'some search',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE       => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY               => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,

                ),
                array(
                    'videos',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'search'            => 'some search',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_RELATED,
                    tubepress_dailymotion_api_Constants::OPTION_RELATED_VALUE           => 'some-video-id',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                ),
                array(
                    'video', 'some-video-id', 'related',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                )
            ),
            array(
                array(
                    tubepress_api_options_Names::GALLERY_SOURCE                         => tubepress_dailymotion_api_Constants::GALLERY_SOURCE_TAG,
                    tubepress_dailymotion_api_Constants::OPTION_TAG_VALUE               => 'tag1  , tag2',
                    tubepress_api_options_Names::FEED_RESULTS_PER_PAGE                  => 44,
                    tubepress_api_options_Names::FEED_ORDER_BY                          => tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT,
                ),
                array(
                    'videos',
                ),
                array(
                    'page'              => 33,
                    'limit'             => 3,
                    'sort'              => 'visited',
                    'fields'            => 'id,access_error,allow_embed,private,private_id,published,channel.name,created_time,description,duration,owner.id,owner.screenname,owner.url,tags,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url,thumbnail_url,title,url,views_total',
                    'strongtags' => 'tag1,tag2',
                )
            ),
        );
    }

    public function testGetName()
    {
        $this->assertEquals('dailymotion', $this->_sut->getName());
    }
}
