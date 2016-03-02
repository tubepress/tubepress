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
 * @covers tubepress_dailymotion_impl_media_MediaProvider
 */
class tubepress_test_dailymotion_impl_media_MediaProviderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_dailymotion_impl_media_MediaProvider
     */
    private $_sut;


    /**
     * @var Mockery\MockInterface
     */
    private $_mockHttpCollector;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockFeedHandler;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEnvironment;

    public function onSetup()
    {
        $this->_mockHttpCollector = $this->mock(tubepress_api_media_HttpCollectorInterface::_);
        $this->_mockFeedHandler   = $this->mock(tubepress_spi_media_HttpFeedHandlerInterface::_);
        $this->_mockEnvironment   = $this->mock(tubepress_api_environment_EnvironmentInterface::_);
        $mockBaseUrl              = $this->mock('tubepress_api_url_UrlInterface');

        $this->_mockEnvironment->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('getClone')->once()->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('addPath')->once()->with('src/add-ons/provider-dailymotion/web/images/icons/dailymotion-icon-34w_x_34h.png')->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('icon-url');

        $this->_sut = new tubepress_dailymotion_impl_media_MediaProvider(

            $this->_mockHttpCollector,
            $this->_mockFeedHandler,
            $this->_mockEnvironment,
            new tubepress_util_impl_StringUtils()
        );
    }

    public function testGetSearchModeName()
    {
        $this->assertEquals(tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH, $this->_sut->getSearchModeName());
    }

    public function testGetSearchQueryOptionName()
    {
        $this->assertEquals(tubepress_dailymotion_api_Constants::OPTION_SEARCH_VALUE, $this->_sut->getSearchQueryOptionName());
    }

    public function testGetSort()
    {
        $expected = array(

            tubepress_dailymotion_api_Constants::ORDER_BY_DEFAULT    => 'default',
            tubepress_dailymotion_api_Constants::ORDER_BY_NEWEST     => 'date published (newest first)',
            tubepress_dailymotion_api_Constants::ORDER_BY_OLDEST     => 'date published (oldest first)',
            tubepress_dailymotion_api_Constants::ORDER_BY_RELEVANCE  => 'relevance',
            tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT => 'view count',
            tubepress_dailymotion_api_Constants::ORDER_BY_RANDOM     => 'random',
            tubepress_dailymotion_api_Constants::ORDER_BY_RANKING    => 'ranking',
            tubepress_dailymotion_api_Constants::ORDER_BY_TRENDING   => 'trending',
        );

        $actual = $this->_sut->getMapOfFeedSortNamesToUntranslatedLabels();

        $this->assertEquals($expected, $actual);
    }

    public function testGetName()
    {
        $this->assertEquals('dailymotion', $this->_sut->getName());
    }

    public function testRecognizesVideoId()
    {
        $this->assertFalse($this->_sut->ownsItem('11111111d'));
        $this->assertFalse($this->_sut->ownsItem('dddddddd'));
        $this->assertTrue($this->_sut->ownsItem('dailymotion_xyz'));
    }

    public function testSources()
    {
        $this->assertEquals(

            array(

                tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FAVORITES,
                tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FEATURED,
                tubepress_dailymotion_api_Constants::GALLERY_SOURCE_LIST,
                tubepress_dailymotion_api_Constants::GALLERY_SOURCE_PLAYLIST,
                tubepress_dailymotion_api_Constants::GALLERY_SOURCE_RELATED,
                tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH,
                tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SUBSCRIPTIONS,
                tubepress_dailymotion_api_Constants::GALLERY_SOURCE_TAG,
                tubepress_dailymotion_api_Constants::GALLERY_SOURCE_USER,
            ),

            $this->_sut->getGallerySourceNames()
        );
    }

    public function testGallery()
    {
        $mockMediaPage = $this->mock('tubepress_api_media_MediaPage');

        $this->_mockHttpCollector->shouldReceive('collectPage')->once()->with(
            45, $this->_mockFeedHandler
        )->andReturn($mockMediaPage);

        $actual = $this->_sut->collectPage(45);

        $this->assertSame($mockMediaPage, $actual);
    }

    public function testSingle()
    {
        $mockMediaItem = $this->mock('tubepress_api_media_MediaItem');

        $this->_mockHttpCollector->shouldReceive('collectSingle')->once()->with(
            'abc', $this->_mockFeedHandler
        )->andReturn($mockMediaItem);

        $actual = $this->_sut->collectSingle('abc');

        $this->assertSame($mockMediaItem, $actual);
    }

    public function testGetfriendlyName()
    {
        $this->assertEquals('Dailymotion', $this->_sut->getDisplayName());
    }

    public function testGetAdditionalMetaNames()
    {
        $expected = array(

            tubepress_api_options_Names::META_DISPLAY_TITLE       => tubepress_api_media_MediaItem::ATTRIBUTE_TITLE,
            tubepress_api_options_Names::META_DISPLAY_LENGTH      => tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_AUTHOR      => tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
            tubepress_api_options_Names::META_DISPLAY_KEYWORDS    => tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_URL         => tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL,
            tubepress_api_options_Names::META_DISPLAY_CATEGORY    => tubepress_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME,
            tubepress_api_options_Names::META_DISPLAY_KEYWORDS    => tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED,

            tubepress_api_options_Names::META_DISPLAY_ID          => tubepress_api_media_MediaItem::ATTRIBUTE_ID,
            tubepress_api_options_Names::META_DISPLAY_VIEWS       => tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
            tubepress_api_options_Names::META_DISPLAY_UPLOADED    => tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_DESCRIPTION => tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
        );

        $actual = $this->_sut->getMapOfMetaOptionNamesToAttributeDisplayNames();
        $this->assertEquals($expected, $actual);
    }
}
