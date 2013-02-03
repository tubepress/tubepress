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
class tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklistTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

	function onSetup()
	{
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

		$this->_sut = new tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklist();
	}

	function testYouTubeFavorites()
	{

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST)->andReturn('xxx');

        $mockVideo1 = new tubepress_api_video_Video();
        $mockVideo1->setId('p');

        $mockVideo2 = new tubepress_api_video_Video();
        $mockVideo2->setId('y');

        $mockVideo3 = new tubepress_api_video_Video();
        $mockVideo3->setId('xxx');

        $mockVideo4 = new tubepress_api_video_Video();
        $mockVideo4->setId('yyy');

        $videoArray = array($mockVideo1, $mockVideo2, $mockVideo3, $mockVideo4);

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($videoArray);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array($mockVideo1, $mockVideo2, $mockVideo4), $event->getSubject()->getVideos());
	}

}

