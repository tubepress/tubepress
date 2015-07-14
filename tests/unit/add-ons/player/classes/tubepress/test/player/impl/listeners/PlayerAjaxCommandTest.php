<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_player_impl_listeners_PlayerAjaxListener<extended>
 */
class tubepress_test_player_impl_listeners_PlayerAjaxListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_player_impl_listeners_PlayerAjaxListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockVideoCollector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockResponseCode;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAjaxEvent;

    public function onSetup()
    {
        $this->_mockLogger                      = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockExecutionContext            = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_lib_api_http_RequestParametersInterface::_);
        $this->_mockVideoCollector              = $this->mock(tubepress_app_api_media_CollectorInterface::_);
        $this->_mockResponseCode                = $this->mock(tubepress_lib_api_http_ResponseCodeInterface::_);
        $this->_mockTemplating                  = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);
        $this->_mockAjaxEvent                   = $this->mock('tubepress_lib_api_event_EventInterface');

        $this->_sut = new tubepress_player_impl_listeners_PlayerAjaxListener(
            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockVideoCollector,
            $this->_mockHttpRequestParameterService,
            $this->_mockResponseCode,
            $this->_mockTemplating
        );
    }

    public function testVideoFound()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $queryParams = array('foo' => 'bar', 'a' => 'b');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_options')->andReturn($queryParams);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_item')->andReturn('-video-');

        $this->_mockExecutionContext->shouldReceive('setEphemeralOptions')->once()->with($queryParams);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY)->andReturn(false);

        $mockVideo = new tubepress_app_api_media_MediaItem('id');
        $mockVideo->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE, 'video title');

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('gallery/player/ajax', array(
            tubepress_app_api_template_VariableNames::MEDIA_ITEM => $mockVideo
        ))->andReturn('player-html');

        $this->_mockVideoCollector->shouldReceive('collectSingle')->once()->andReturn($mockVideo);

        $this->_mockResponseCode->shouldReceive('setResponseCode')->once()->with(200);

        $this->expectOutputString('{"mediaItem":{"id":"id","title":"video title"},"html":"player-html"}');

        $this->_mockAjaxEvent->shouldReceive('setArgument')->once()->with('handled', true);
        $this->_sut->onAjax($this->_mockAjaxEvent);
        $this->assertTrue(true);
    }

    public function testLazyPlay()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $queryParams = array('foo' => 'bar', 'a' => 'b');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_options')->andReturn($queryParams);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_item')->andReturn('-video-');

        $this->_mockExecutionContext->shouldReceive('setEphemeralOptions')->once()->with($queryParams);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('setEphemeralOption')->once()->with(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY, true);

        $this->_mockVideoCollector->shouldReceive('collectSingle')->once()->andReturn(null);

        $this->_mockResponseCode->shouldReceive('setResponseCode')->once()->with(404);

        $this->_mockAjaxEvent->shouldReceive('setArgument')->once()->with('handled', true);
        $this->_sut->onAjax($this->_mockAjaxEvent);
        $this->assertTrue(true);
    }

    public function testVideoNotFound()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $queryParams = array('foo' => 'bar', 'a' => 'b');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_options')->andReturn($queryParams);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_item')->andReturn('-video-');

        $this->_mockExecutionContext->shouldReceive('setEphemeralOptions')->once()->with($queryParams);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY)->andReturn(false);

        $this->_mockVideoCollector->shouldReceive('collectSingle')->once()->andReturn(null);

        $this->_mockResponseCode->shouldReceive('setResponseCode')->once()->with(404);
        $this->_mockAjaxEvent->shouldReceive('setArgument')->once()->with('handled', true);

        $this->_sut->onAjax($this->_mockAjaxEvent);
        $this->assertTrue(true);
    }
}
