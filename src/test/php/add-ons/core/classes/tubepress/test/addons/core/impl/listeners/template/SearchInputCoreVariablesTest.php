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
 * @covers tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables
 */
class tubepress_test_addons_core_impl_listeners_template_SearchInputCoreVariablesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockQueryStringService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_mockQueryStringService = $this->createMockSingletonService(tubepress_spi_querystring_QueryStringService::_);;

        $this->_mockMessageService = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);

        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
    }

    public function testYouTubeFavorites()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL)->andReturn('');

        $this->_mockQueryStringService->shouldReceive('getFullUrl')->once()->andReturn('http://tubepress.com?foo=bar&something=else');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("search for something");

        $this->_mockMessageService->shouldReceive('_')->once()->andReturnUsing(function ($msg) {
            return "##$msg##";
        });

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_HANDLER_URL, 'http://tubepress.com?foo=bar&something=else');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS, array('foo' => 'bar', 'something' => 'else'));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_TERMS, 'search for something');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::SEARCH_BUTTON, '##Search##');

        $event = new tubepress_spi_event_EventBase($mockTemplate);

        $this->_sut->onSearchInputTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }

}

