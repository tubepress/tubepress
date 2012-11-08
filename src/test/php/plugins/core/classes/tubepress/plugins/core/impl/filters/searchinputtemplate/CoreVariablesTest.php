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
class tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariablesTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

    private $_mockQueryStringService;

    private $_mockMessageService;

    private $_mockHttpRequestParameterService;

	function onSetup()
	{
		$this->_sut = new tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariables();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_mockQueryStringService = $this->createMockSingletonService(tubepress_spi_querystring_QueryStringService::_);;

        $this->_mockMessageService = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);

        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
	}

	function testYouTubeFavorites()
	{
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL)->andReturn('');

        $this->_mockQueryStringService->shouldReceive('getFullUrl')->once()->andReturn('http://tubepress.org?foo=bar&something=else');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("search for something");

        $this->_mockMessageService->shouldReceive('_')->once()->andReturnUsing(function ($msg) {
            return "##$msg##";
        });

        $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_HANDLER_URL, 'http://tubepress.org?foo=bar&something=else');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS, array('foo' => 'bar', 'something' => 'else'));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_TERMS, 'search for something');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_BUTTON, '##Search##');

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);

        $this->_sut->onSearchInputTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
	}

}

