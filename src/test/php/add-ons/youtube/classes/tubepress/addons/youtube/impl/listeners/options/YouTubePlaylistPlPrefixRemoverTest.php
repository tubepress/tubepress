<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistPlPrefixRemoverTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistPlPrefixRemover
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistPlPrefixRemover();
    }

    public function testAlterDifferentVariable()
    {
        $event = new tubepress_spi_event_EventBase('PLsomething');
        $event->setArgument('optionName', tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('PLsomething', $event->getSubject());
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