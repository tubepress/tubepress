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
 * @covers tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService
 */
class tubepress_test_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSingleVideoShortcodeHandler;

    public function onSetup()
    {

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_mockSingleVideoShortcodeHandler = ehough_mockery_Mockery::mock(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $this->_sut = new tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService($this->_mockSingleVideoShortcodeHandler);
    }

    public function testExecuteWrongPlayer()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('shadowbox');

        $this->assertFalse($this->_sut->shouldExecute());
    }

    public function testExecuteNoVideoId()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('solo');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('');

        $this->assertFalse($this->_sut->shouldExecute());
    }

    public function testExecute()
    {
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::VIDEO, 'video-id')->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('video-id');

        $this->_mockSingleVideoShortcodeHandler->shouldReceive('getHtml')->once()->andReturn('abc');

        $this->assertEquals('abc', $this->_sut->getHtml());
    }
}