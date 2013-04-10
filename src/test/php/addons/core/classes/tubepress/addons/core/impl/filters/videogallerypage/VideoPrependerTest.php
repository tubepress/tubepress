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
class tubepress_addons_core_impl_filters_videogallerypage_VideoPrependerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_filters_videogallerypage_VideoPrepender
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockVideoProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    function onSetup()
    {

        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_mockVideoProvider = $this->createMockSingletonService(tubepress_spi_collector_VideoCollector::_);

        $this->_sut = new tubepress_addons_core_impl_filters_videogallerypage_VideoPrepender();
    }

    public function testCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('custom-video');

        $video = new tubepress_api_video_Video();
        $video->setId('video-id');

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($video));

        $this->_mockVideoProvider->shouldReceive('collectSingleVideo')->once()->with('custom-video')->andReturn('x');

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array('x', $video), $event->getSubject()->getVideos());
    }

    function testNoCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('');

        $providerResult = new tubepress_api_video_VideoGalleryPage();

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array(), $event->getSubject()->getVideos());
    }

}

