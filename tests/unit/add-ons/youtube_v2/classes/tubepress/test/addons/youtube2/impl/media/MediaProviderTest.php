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
 * @covers tubepress_youtube2_impl_media_MediaProvider
 */
class tubepress_test_youtube2_impl_media_MediaProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube2_impl_media_MediaProvider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpCollector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFeedHandler;

    public function onSetup()
    {
        $this->_mockHttpCollector = $this->mock(tubepress_app_api_media_HttpCollectorInterface::_);
        $this->_mockFeedHandler   = $this->mock(tubepress_app_api_media_HttpFeedHandlerInterface::_);

        $this->_sut = new tubepress_youtube2_impl_media_MediaProvider(

            $this->_mockHttpCollector, $this->_mockFeedHandler
        );
    }

    public function testGallery()
    {
        $mockMediaPage = $this->mock('tubepress_app_api_media_MediaPage');

        $this->_mockHttpCollector->shouldReceive('collectPage')->once()->with(
            45, $this->_mockFeedHandler
        )->andReturn($mockMediaPage);

        $actual = $this->_sut->collectPage(45);

        $this->assertSame($mockMediaPage, $actual);
    }

    public function testSingle()
    {
        $mockMediaItem = $this->mock('tubepress_app_api_media_MediaItem');

        $this->_mockHttpCollector->shouldReceive('collectSingle')->once()->with(
            'abc', $this->_mockFeedHandler
        )->andReturn($mockMediaItem);

        $actual = $this->_sut->collectSingle('abc');

        $this->assertSame($mockMediaItem, $actual);
    }

    public function testGetFeedSortNamesToLabels()
    {
        $expected = array(
            tubepress_youtube2_api_Constants::ORDER_BY_COMMENT_COUNT  => 'comment count',                   //>(translatable)<
            tubepress_youtube2_api_Constants::ORDER_BY_DEFAULT        => 'default',                         //>(translatable)<
            tubepress_youtube2_api_Constants::ORDER_BY_NEWEST         => 'date published (newest first)',   //>(translatable)<
            tubepress_youtube2_api_Constants::ORDER_BY_DURATION       => 'length',                          //>(translatable)<
            tubepress_youtube2_api_Constants::ORDER_BY_POSITION       => 'position in a playlist',          //>(translatable)<
            tubepress_youtube2_api_Constants::ORDER_BY_REV_POSITION   => 'reversed position in a playlist', //>(translatable)<
            tubepress_youtube2_api_Constants::ORDER_BY_RATING         => 'rating',                          //>(translatable)<
            tubepress_youtube2_api_Constants::ORDER_BY_RELEVANCE      => 'relevance',                       //>(translatable)<
            tubepress_youtube2_api_Constants::ORDER_BY_TITLE          => 'title',                           //>(translatable)<
            tubepress_youtube2_api_Constants::ORDER_BY_VIEW_COUNT     => 'view count',                      //>(translatable)<
        );

        $this->assertEquals($expected, $this->_sut->getMapOfFeedSortNamesToUntranslatedLabels());
    }

    public function testGetMetaOptionNamesToDisplayNames()
    {
        $expected = array(
            tubepress_app_api_options_Names::META_DISPLAY_TITLE       => tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE,
            tubepress_app_api_options_Names::META_DISPLAY_LENGTH      => tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_AUTHOR      => tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
            tubepress_app_api_options_Names::META_DISPLAY_KEYWORDS    => tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_URL         => tubepress_app_api_media_MediaItem::ATTRIBUTE_HOME_URL,
            tubepress_app_api_options_Names::META_DISPLAY_CATEGORY    => tubepress_app_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME,

            tubepress_youtube2_api_Constants::OPTION_RATINGS           => tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_COUNT,
            tubepress_youtube2_api_Constants::OPTION_RATING            => tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_AVERAGE,

            tubepress_app_api_options_Names::META_DISPLAY_ID          => tubepress_app_api_media_MediaItem::ATTRIBUTE_ID,
            tubepress_app_api_options_Names::META_DISPLAY_VIEWS       => tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
            tubepress_app_api_options_Names::META_DISPLAY_UPLOADED    => tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_DESCRIPTION => tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
        );

        $this->assertEquals($expected, $this->_sut->getMapOfMetaOptionNamesToAttributeDisplayNames());
    }

    public function testGetDisplayName()
    {
        $this->assertEquals('YouTube', $this->_sut->getDisplayName());
    }

    public function testGetName()
    {
        $this->assertEquals('youtube_v2', $this->_sut->getName());
    }

    public function testGetSearchModeName()
    {
        $this->assertEquals(tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH, $this->_sut->getSearchModeName());
    }

    public function testGetSearchQueryOptionName()
    {
        $this->assertEquals(tubepress_youtube2_api_Constants::OPTION_YOUTUBE_TAG_VALUE, $this->_sut->getSearchQueryOptionName());
    }

    public function testRecognizesVideoId()
    {
        $this->assertTrue($this->_sut->ownsItem('SJxBZgC29ts'));
        $this->assertTrue($this->_sut->ownsItem('S5yDS-mFRy4'));
        $this->assertTrue($this->_sut->ownsItem('KcXjhikIz6o'));
        $this->assertTrue($this->_sut->ownsItem('T8KJGtMGMSY'));
        $this->assertFalse($this->_sut->ownsItem('339494949'));
        $this->assertFalse($this->_sut->ownsItem('S5yDS-mFRy]'));
        $this->assertFalse($this->_sut->ownsItem('KcXjhikIz'));
        $this->assertFalse($this->_sut->ownsItem('T8K..tMGMSY'));
    }

    public function testSources()
    {
        $this->assertEquals(

            array(

                tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
                tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED,
                tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
                tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
            ),

            $this->_sut->getGallerySourceNames()
        );
    }
}
