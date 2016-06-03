<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_player_impl_listeners_SoloPlayerListener
 */
class tubepress_test_player_impl_listeners_SoloPlayerListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_player_impl_listeners_SoloPlayerListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {

        $this->_mockExecutionContext            = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockLogger                      = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEvent                       = $this->mock('tubepress_api_event_EventInterface');

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_player_impl_listeners_SoloPlayerListener(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockHttpRequestParameterService
        );
    }

    public function testExecuteWrongPlayer()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::PLAYER_LOCATION)->andReturn('shadowbox');

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testExecuteNoVideoId()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::PLAYER_LOCATION)->andReturn('solo');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_item')->andReturn('');

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::PLAYER_LOCATION)->andReturn('solo');

        $this->_mockExecutionContext->shouldReceive('setEphemeralOption')->once()->with(tubepress_api_options_Names::SINGLE_MEDIA_ITEM_ID, 'video-id')->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->twice()->with('tubepress_item')->andReturn('video-id');

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }
}
