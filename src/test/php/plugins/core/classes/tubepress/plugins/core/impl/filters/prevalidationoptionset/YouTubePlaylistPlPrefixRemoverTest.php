<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemoverTest extends TubePressUnitTest
{
	private $_sut;

	function onSetup()
	{
		$this->_sut = new tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover();
	}

	function testAlterDifferentVariable()
	{
        $event = new tubepress_api_event_TubePressEvent('PLsomething');
        $event->setArgument('optionName', tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

	    $this->assertEquals('PLsomething', $event->getSubject());
	}

	function testAlterNonString()
	{
        $event = new tubepress_api_event_TubePressEvent(array('hello'));
        $event->setArgument('optionName', tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

	    $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals(array('hello'), $event->getSubject());
	}

	function testAlterHtmlNonPrefix()
	{
        $event = new tubepress_api_event_TubePressEvent('hello');
        $event->setArgument('optionName', tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('hello', $event->getSubject());
	}

	function testAlterPrefix()
	{
        $event = new tubepress_api_event_TubePressEvent('PLhelloPL');
        $event->setArgument('optionName', tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

        $this->_sut->onPreValidationOptionSet($event);

        $this->assertEquals('helloPL', $event->getSubject());
	}
}