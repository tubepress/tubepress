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
 * @covers tubepress_app_impl_listeners_search_html_SearchInputListener
 */
class tubepress_test_app_impl_listeners_search_html_SearchInputListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_search_html_SearchInputListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockTemplating       = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_lib_api_event_EventInterface');

        $this->_sut = new tubepress_app_impl_listeners_search_html_SearchInputListener(

            $this->_mockExecutionContext,
            $this->_mockTemplating
        );
    }

    public function testShouldNotExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::HTML_OUTPUT)->andReturn(tubepress_app_api_options_AcceptableValues::OUTPUT_SEARCH_RESULTS);

        $this->_sut->onHtmlGeneration($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::HTML_OUTPUT)->andReturn(tubepress_app_api_options_AcceptableValues::OUTPUT_SEARCH_INPUT);

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('search/input', array())->andReturn('bla');

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('bla');
        $this->_mockEvent->shouldReceive('stopPropagation');

        $this->_sut->onHtmlGeneration($this->_mockEvent);

        $this->assertTrue(true);
    }
}