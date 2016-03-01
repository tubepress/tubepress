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
 * @covers tubepress_youtube3_impl_media_MediaProvider
 */
class tubepress_test_youtube3_impl_media_MediaProviderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube3_impl_media_MediaProvider
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
        $mockBaseUrl->shouldReceive('addPath')->once()->with('/src/add-ons/provider-youtube-v3/web/images/icons/youtube-icon-34w_x_34h.png')->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('icon-url');

        $this->_sut = new tubepress_youtube3_impl_media_MediaProvider(

            $this->_mockHttpCollector,
            $this->_mockFeedHandler,
            $this->_mockEnvironment
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

    public function testGetFeedSortNamesToLabels()
    {
        $expected = array(
            tubepress_youtube3_api_Constants::ORDER_BY_DEFAULT        => 'default',                         //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_NEWEST         => 'date published (newest first)',   //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_RATING         => 'rating',                          //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_RELEVANCE      => 'relevance',                       //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_TITLE          => 'title',                           //>(translatable)<
            tubepress_youtube3_api_Constants::ORDER_BY_VIEW_COUNT     => 'view count',                      //>(translatable)<
        );

        $this->assertEquals($expected, $this->_sut->getMapOfFeedSortNamesToUntranslatedLabels());
    }

    public function testGetMetaOptionNamesToDisplayNames()
    {
        $expected = array(
            tubepress_api_options_Names::META_DISPLAY_TITLE       => tubepress_api_media_MediaItem::ATTRIBUTE_TITLE,
            tubepress_api_options_Names::META_DISPLAY_LENGTH      => tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_AUTHOR      => tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
            tubepress_api_options_Names::META_DISPLAY_KEYWORDS    => tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_URL         => tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL,
            tubepress_api_options_Names::META_DISPLAY_CATEGORY    => tubepress_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME,

            tubepress_youtube3_api_Constants::OPTION_META_COUNT_LIKES     => tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT_FORMATTED,
            tubepress_youtube3_api_Constants::OPTION_META_COUNT_DISLIKES  => tubepress_api_media_MediaItem::ATTRIBUTE_COUNT_DISLIKES_FORMATTED,
            tubepress_youtube3_api_Constants::OPTION_META_COUNT_COMMENTS  => tubepress_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT_FORMATTED,
            tubepress_youtube3_api_Constants::OPTION_META_COUNT_FAVORITES => tubepress_api_media_MediaItem::ATTRIBUTE_COUNT_FAVORITED_FORMATTED,

            tubepress_api_options_Names::META_DISPLAY_ID          => tubepress_api_media_MediaItem::ATTRIBUTE_ID,
            tubepress_api_options_Names::META_DISPLAY_VIEWS       => tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_UPLOADED    => tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_DESCRIPTION => tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
        );

        $this->assertEquals($expected, $this->_sut->getMapOfMetaOptionNamesToAttributeDisplayNames());
    }

    public function testGetDisplayName()
    {
        $this->assertEquals('YouTube', $this->_sut->getDisplayName());
    }

    public function testGetName()
    {
        $this->assertEquals('youtube', $this->_sut->getName());
    }

    public function testGetSearchModeName()
    {
        $this->assertEquals(tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH, $this->_sut->getSearchModeName());
    }

    public function testGetSearchQueryOptionName()
    {
        $this->assertEquals(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_TAG_VALUE, $this->_sut->getSearchQueryOptionName());
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

                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_LIST,
            ),

            $this->_sut->getGallerySourceNames()
        );
    }
}
