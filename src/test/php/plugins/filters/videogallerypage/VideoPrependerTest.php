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
class tubepress_plugins_core_filters_videogallerypage_VideoPrependerTest extends PHPUnit_Framework_TestCase
{
	private $_sut;

    private $_mockVideoProvider;

    private $_mockHttpRequestParameterService;

	function setup()
	{

        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);

        $this->_mockVideoProvider = Mockery::mock(tubepress_spi_provider_Provider::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProvider($this->_mockVideoProvider);

		$this->_sut = new tubepress_plugins_core_filters_videogallerypage_VideoPrepender();
	}

    function testCustomVideo()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('custom-video');

        $video = new tubepress_api_video_Video();
        $video->setId('video-id');

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($video));

        $this->_mockVideoProvider->shouldReceive('getSingleVideo')->once()->with('custom-video')->andReturn('x');

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array('x', $video), $event->getSubject()->getVideos());
	}

	function testNoCustomVideo()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('');

        $providerResult = new tubepress_api_video_VideoGalleryPage();

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array(), $event->getSubject()->getVideos());
	}

}

