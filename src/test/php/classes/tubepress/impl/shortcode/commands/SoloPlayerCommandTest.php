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
class org_tubepress_impl_shortcode_commands_SoloPlayerCommandTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

    private $_mockChain;

    private $_mockHttpRequestParameterService;

	function setup()
	{
        $this->_mockChain = Mockery::mock('ehough_chaingang_api_Chain');

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);

		$this->_sut = new tubepress_impl_shortcode_commands_SoloPlayerCommand($this->_mockChain);
	}

	function testExecuteWrongPlayer()
	{
	    $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn(tubepress_api_const_options_values_PlayerLocationValue::SHADOWBOX);

	    $this->assertFalse($this->_sut->execute(new ehough_chaingang_impl_StandardContext()));
	}

	function testExecuteNoVideoId()
	{
	    $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn(tubepress_api_const_options_values_PlayerLocationValue::SOLO);

	    $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('');

	    $this->assertFalse($this->_sut->execute(new ehough_chaingang_impl_StandardContext()));
	}

	function testExecuteSetExecContextFails()
	{
	    $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn(tubepress_api_const_options_values_PlayerLocationValue::SOLO);
	    $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::VIDEO, 'video-id')->andReturn(false);

	    $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('video-id');

	    $this->assertFalse($this->_sut->execute(new ehough_chaingang_impl_StandardContext()));
	}

	function testExecute()
	{
	    $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn(tubepress_api_const_options_values_PlayerLocationValue::SOLO);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::VIDEO, 'video-id')->andReturn(true);

	    $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('video-id');

        $mockChainContext = new ehough_chaingang_impl_StandardContext();

	    $this->_mockChain->shouldReceive('execute')->once()->with($mockChainContext)->andReturn(true);

	    $this->assertTrue($this->_sut->execute($mockChainContext));
	}
}