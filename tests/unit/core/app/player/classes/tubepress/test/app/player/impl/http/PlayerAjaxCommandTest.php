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
 * @covers tubepress_app_player_impl_http_PlayerAjaxCommand<extended>
 */
class tubepress_test_app_http_impl_PlayerAjaxCommandTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_player_impl_http_PlayerAjaxCommand
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPlayerHtmlGenerator;

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

    public function onSetup()
    {
        $this->_mockLogger                      = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockExecutionContext            = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_app_http_api_RequestParametersInterface::_);
        $this->_mockVideoCollector              = $this->mock(tubepress_app_media_provider_api_CollectorInterface::_);
        $this->_mockPlayerHtmlGenerator         = $this->mock(tubepress_app_player_api_PlayerHtmlInterface::_);
        $this->_mockResponseCode                = $this->mock(tubepress_lib_http_api_ResponseCodeInterface::_);

        $this->_sut = new tubepress_app_player_impl_http_PlayerAjaxCommand(
            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockPlayerHtmlGenerator,
            $this->_mockVideoCollector,
            $this->_mockHttpRequestParameterService,
            $this->_mockResponseCode
        );
    }

    public function testVideoFound()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $queryParams = array('foo' => 'bar', 'a' => 'b');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_options')->andReturn($queryParams);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_lib_http_api_Constants::PARAM_NAME_ITEMID)->andReturn('-video-');

        $this->_mockExecutionContext->shouldReceive('setEphemeralOptions')->once()->with($queryParams);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_embedded_api_Constants::OPTION_LAZYPLAY)->andReturn(false);

        $mockVideo = new tubepress_app_media_item_api_MediaItem('id');
        $mockVideo->setAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE, 'video title');

        $this->_mockPlayerHtmlGenerator->shouldReceive('getAjaxHtml')->once()->with($mockVideo)->andReturn('player-html');

        $this->_mockVideoCollector->shouldReceive('collectSingle')->once()->andReturn($mockVideo);

        $this->_mockResponseCode->shouldReceive('setResponseCode')->once()->with(200);

        $this->expectOutputString('{"item":{"title":"video title"},"html":"player-html"}');

        $this->_sut->handle();
    }

    public function testLazyPlay()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $queryParams = array('foo' => 'bar', 'a' => 'b');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_options')->andReturn($queryParams);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_lib_http_api_Constants::PARAM_NAME_ITEMID)->andReturn('-video-');

        $this->_mockExecutionContext->shouldReceive('setEphemeralOptions')->once()->with($queryParams);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_embedded_api_Constants::OPTION_LAZYPLAY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('setEphemeralOption')->once()->with(tubepress_app_embedded_api_Constants::OPTION_AUTOPLAY, true);

        $this->_mockVideoCollector->shouldReceive('collectSingle')->once()->andReturn(null);

        $this->_mockResponseCode->shouldReceive('setResponseCode')->once()->with(404);
        $this->expectOutputString('Video -video- not found');

        $this->_sut->handle();
    }

    public function testVideoNotFound()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $queryParams = array('foo' => 'bar', 'a' => 'b');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_options')->andReturn($queryParams);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_lib_http_api_Constants::PARAM_NAME_ITEMID)->andReturn('-video-');

        $this->_mockExecutionContext->shouldReceive('setEphemeralOptions')->once()->with($queryParams);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_embedded_api_Constants::OPTION_LAZYPLAY)->andReturn(false);

        $this->_mockVideoCollector->shouldReceive('collectSingle')->once()->andReturn(null);


        $this->_mockResponseCode->shouldReceive('setResponseCode')->once()->with(404);
        $this->expectOutputString('Video -video- not found');
        $this->_sut->handle();
    }

    public function testGetCommandName()
    {
        $this->assertEquals('playerHtml', $this->_sut->getName());
    }
}
