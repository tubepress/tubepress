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
 * @covers tubepress_core_impl_listeners_videogallerypage_VideoPrepender
 */
class tubepress_test_core_impl_listeners_videogallerypage_VideoPrependerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_videogallerypage_VideoPrepender
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_api_http_RequestParametersInterface::_);

        $this->_mockVideoProvider = $this->mock(tubepress_core_api_collector_CollectorInterface::_);

        $this->_sut = new tubepress_core_impl_listeners_videogallerypage_VideoPrepender($this->_mockLogger, $this->_mockHttpRequestParameterService, $this->_mockVideoProvider);
    }

    public function testCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_core_api_const_http_ParamName::VIDEO)->andReturn('custom-video');

        $video = new tubepress_core_api_video_Video();
        $video->setId('video-id');

        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($video));

        $this->_mockVideoProvider->shouldReceive('collectSingle')->once()->with('custom-video')->andReturn('x');

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->times(2)->andReturn($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array('x', $video), $providerResult->getVideos());
    }

    public function testNoCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_core_api_const_http_ParamName::VIDEO)->andReturn('');

        $providerResult = new tubepress_core_api_video_VideoGalleryPage();

        $event = $this->mock('tubepress_core_api_event_EventInterface');

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array(), $providerResult->getVideos());
    }
}