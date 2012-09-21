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
class tubepress_plugins_core_filters_videogallerypage_VideoBlacklistTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

	function setup()
	{
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

		$this->_sut = new tubepress_plugins_core_filters_videogallerypage_VideoBlacklist();
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

