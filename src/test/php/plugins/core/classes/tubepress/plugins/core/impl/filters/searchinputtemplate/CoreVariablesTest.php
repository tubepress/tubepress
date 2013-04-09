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

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_HANDLER_URL, 'http://tubepress.org?foo=bar&something=else');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS, array('foo' => 'bar', 'something' => 'else'));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_TERMS, 'search for something');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_BUTTON, '##Search##');

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);

        $this->_sut->onSearchInputTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
	}

}

