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
 * @covers tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistHandler
 */
class tubepress_test_addons_youtube_impl_listeners_options_YouTubePlaylistHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistHandler
     */
    private $_sut;

    public function onSetup()
    {

        $mockUrlFactory = $this->createMockSingletonService(tubepress_api_url_UrlFactoryInterface::_);
        $mockUrlFactory->shouldReceive('fromString')->andReturnUsing(function ($incoming) {

            $factory = new tubepress_addons_puzzle_impl_url_UrlFactory();

            return $factory->fromString($incoming);
        });
        $this->_sut = new tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistHandler($mockUrlFactory);
    }

    public function testPullListFromUrl()
    {
        $event = new tubepress_spi_event_EventBase('http://youtube.com/?list=PL123');
        $event->setArgument('optionName', tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('123', $event->getSubject());
    }

    public function testPullListFromUrlNoListParam()
    {
        $event = new tubepress_spi_event_EventBase('http://youtube.com/?lt=123');
        $event->setArgument('optionName', tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('http://youtube.com/?lt=123', $event->getSubject());
    }

    public function testPullListFromUrlNonYouTube()
    {
        $event = new tubepress_spi_event_EventBase('http://vimeo.com/?list=123');
        $event->setArgument('optionName', tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('http://vimeo.com/?list=123', $event->getSubject());
    }

    public function testAlterNonString()
    {
        $event = new tubepress_spi_event_EventBase(array('hello'));
        $event->setArgument('optionName', tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals(array('hello'), $event->getSubject());
    }

    public function testAlterHtmlNonPrefix()
    {
        $event = new tubepress_spi_event_EventBase('hello');
        $event->setArgument('optionName', tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('hello', $event->getSubject());
    }

    public function testAlterPrefix()
    {
        $event = new tubepress_spi_event_EventBase('PLhelloPL');
        $event->setArgument('optionName', tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('helloPL', $event->getSubject());
    }
}