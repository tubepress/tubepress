<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_vimeo3_impl_media_MediaProvider
 */
class tubepress_test_vimeo2_impl_media_MediaProviderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo3_impl_media_MediaProvider
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

    /**
     * @var ehough_mockery_mockery_MockInterface
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
        $mockBaseUrl->shouldReceive('addPath')->once()->with('src/add-ons/provider-vimeo-v3/web/images/icons/vimeo-icon-34w_x_34h.png')->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('icon-url');

        $this->_sut = new tubepress_vimeo3_impl_media_MediaProvider(

            $this->_mockHttpCollector,
            $this->_mockFeedHandler,
            $this->_mockEnvironment
        );
    }

    public function testGetName()
    {
        $this->assertEquals('vimeo', $this->_sut->getName());
    }

    public function testRecognizesVideoId()
    {
        $this->assertTrue($this->_sut->ownsItem('11111111'));
        $this->assertFalse($this->_sut->ownsItem('11111111d'));
        $this->assertFalse($this->_sut->ownsItem('dddddddd'));
    }

    public function testSources()
    {
        $this->assertEquals(

            array(

                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CATEGORY,
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_TAG,
                tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY
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
        $this->assertEquals('Vimeo', $this->_sut->getDisplayName());
    }

    public function testGetAdditionalMetaNames()
    {
        $expected = array(

            tubepress_api_options_Names::META_DISPLAY_TITLE       => tubepress_api_media_MediaItem::ATTRIBUTE_TITLE,
            tubepress_api_options_Names::META_DISPLAY_LENGTH      => tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_AUTHOR      => tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
            tubepress_api_options_Names::META_DISPLAY_KEYWORDS    => tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_URL         => tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL,

            tubepress_vimeo3_api_Constants::OPTION_LIKES                 => tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT,

            tubepress_api_options_Names::META_DISPLAY_ID          => tubepress_api_media_MediaItem::ATTRIBUTE_ID,
            tubepress_api_options_Names::META_DISPLAY_VIEWS       => tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
            tubepress_api_options_Names::META_DISPLAY_UPLOADED    => tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_DESCRIPTION => tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
        );

        $actual = $this->_sut->getMapOfMetaOptionNamesToAttributeDisplayNames();
        $this->assertEquals($expected, $actual);
    }
}
