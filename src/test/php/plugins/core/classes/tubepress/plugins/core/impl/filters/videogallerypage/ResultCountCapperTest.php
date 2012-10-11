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
class tubepress_plugins_core_impl_filters_videogallerypage_ResultCountCapperTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

	function setup()
	{
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

		$this->_sut = new tubepress_plugins_core_impl_filters_videogallerypage_ResultCountCapper();
	}

	function testYouTubeFavorites()
	{
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn(tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES);

        $videoArray = array('x', 'y');

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setTotalResultCount(999);
        $providerResult->setVideos($videoArray);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(50, $event->getSubject()->getTotalResultCount());
	}

}

