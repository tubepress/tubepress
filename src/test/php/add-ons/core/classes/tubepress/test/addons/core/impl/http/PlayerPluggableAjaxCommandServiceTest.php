<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService<extended>
 */
class tubepress_test_addons_core_impl_http_PlayerPluggableAjaxCommandServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService
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

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService();

        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockVideoCollector              = $this->createMockSingletonService(tubepress_spi_collector_VideoCollector::_);
        $this->_mockPlayerHtmlGenerator         = $this->createMockSingletonService(tubepress_spi_player_PlayerHtmlGenerator::_);
    }

    public function testVideoFound()
    {
        $queryParams = array('foo' => 'bar', 'a' => 'b');

        $this->_mockHttpRequestParameterService->shouldReceive('getAllParams')->once()->andReturn($queryParams);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('-video-');

        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($queryParams);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LAZYPLAY)->andReturn(false);

        $mockVideo = new tubepress_api_video_Video();
        $mockVideo->setAttribute(tubepress_api_video_Video::ATTRIBUTE_TITLE, 'video title');

        $this->_mockPlayerHtmlGenerator->shouldReceive('getHtml')->once()->with($mockVideo)->andReturn('player-html');

        $this->_mockVideoCollector->shouldReceive('collectSingleVideo')->once()->andReturn($mockVideo);

        $this->_sut->handle();

        $this->assertEquals(200, $this->_sut->getHttpStatusCode());
        $this->assertEquals('{"title":"video title","html":"player-html"}', $this->_sut->getOutput());
    }

    public function testLazyPlay()
    {
        $queryParams = array('foo' => 'bar', 'a' => 'b');

        $this->_mockHttpRequestParameterService->shouldReceive('getAllParams')->once()->andReturn($queryParams);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('-video-');

        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($queryParams);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LAZYPLAY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY, true);

        $this->_mockVideoCollector->shouldReceive('collectSingleVideo')->once()->andReturn(null);

        $this->_sut->handle();

        $this->assertEquals(404, $this->_sut->getHttpStatusCode());
        $this->assertEquals('Video -video- not found', $this->_sut->getOutput());
    }

    public function testVideoNotFound()
    {
        $queryParams = array('foo' => 'bar', 'a' => 'b');

        $this->_mockHttpRequestParameterService->shouldReceive('getAllParams')->once()->andReturn($queryParams);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('-video-');

        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($queryParams);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LAZYPLAY)->andReturn(false);

        $this->_mockVideoCollector->shouldReceive('collectSingleVideo')->once()->andReturn(null);

        $this->_sut->handle();

        $this->assertEquals(404, $this->_sut->getHttpStatusCode());
        $this->assertEquals('Video -video- not found', $this->_sut->getOutput());
    }

    public function testGetCommandName()
    {
        $this->assertEquals('playerHtml', $this->_sut->getName());
    }

}
