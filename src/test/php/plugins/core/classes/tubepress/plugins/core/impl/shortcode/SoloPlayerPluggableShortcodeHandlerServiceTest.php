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
class tubepress_plugins_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerServiceTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService
     */
    private $_sut;

    private $_mockExecutionContext;

    private $_mockHttpRequestParameterService;

    private $_mockSingleVideoShortcodeHandler;

	function setup()
	{

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);

        $this->_mockSingleVideoShortcodeHandler = Mockery::mock(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

		$this->_sut = new tubepress_plugins_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService($this->_mockSingleVideoShortcodeHandler);
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