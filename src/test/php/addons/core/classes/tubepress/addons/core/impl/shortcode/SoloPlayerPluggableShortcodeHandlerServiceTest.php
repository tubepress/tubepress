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
class tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerServiceTest extends TubePressUnitTest
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

	function onSetup()
	{

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_mockSingleVideoShortcodeHandler = $this->createMockPluggableService(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

		$this->_sut = new tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService($this->_mockSingleVideoShortcodeHandler);
	}

	function testExecuteWrongPlayer()
	{
	    $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('shadowbox');

	    $this->assertFalse($this->_sut->shouldExecute());
	}

	function testExecuteNoVideoId()
	{
	    $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('solo');

	    $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('');

	    $this->assertFalse($this->_sut->shouldExecute());
	}

	function testExecute()
	{
	    $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::VIDEO, 'video-id')->andReturn(true);

	    $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('video-id');

	    $this->_mockSingleVideoShortcodeHandler->shouldReceive('getHtml')->once()->andReturn('abc');

	    $this->assertEquals('abc', $this->_sut->getHtml());
	}
}