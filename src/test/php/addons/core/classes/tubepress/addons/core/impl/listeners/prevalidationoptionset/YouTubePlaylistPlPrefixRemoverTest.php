<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_core_impl_listeners_prevalidationoptionset_YouTubePlaylistPlPrefixRemoverTest extends TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_prevalidationoptionset_YouTubePlaylistPlPrefixRemover
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_listeners_prevalidationoptionset_YouTubePlaylistPlPrefixRemover();
    }

    public function testAlterDifferentVariable()
    {
        $event = new tubepress_api_event_TubePressEvent('PLsomething');
        $event->setArgument('optionName', tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('PLsomething', $event->getSubject());
    }

    public function testAlterNonString()
    {
        $event = new tubepress_api_event_TubePressEvent(array('hello'));
        $event->setArgument('optionName', tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals(array('hello'), $event->getSubject());
    }

    public function testAlterHtmlNonPrefix()
    {
        $event = new tubepress_api_event_TubePressEvent('hello');
        $event->setArgument('optionName', tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('hello', $event->getSubject());
    }

    public function testAlterPrefix()
    {
        $event = new tubepress_api_event_TubePressEvent('PLhelloPL');
        $event->setArgument('optionName', tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('helloPL', $event->getSubject());
    }
}