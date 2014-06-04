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
 * @covers tubepress_core_media_search_impl_listeners_html_SearchInputListener
 */
class tubepress_test_core_impl_shortcode_SearchInputCommandTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_search_impl_listeners_html_SearchInputListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockEventDispatcher  = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_mockTemplateFactory  = $this->mock(tubepress_core_template_api_TemplateFactoryInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_core_event_api_EventInterface');

        $this->_sut = new tubepress_core_media_search_impl_listeners_html_SearchInputListener(

            $this->_mockExecutionContext,
            $this->_mockEventDispatcher,
            $this->_mockTemplateFactory
        );
    }

    public function testShouldNotExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_OUTPUT)->andReturn(tubepress_core_media_search_api_Constants::OUTPUT_SEARCH_RESULTS);

        $this->_sut->onHtmlGeneration($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_OUTPUT)->andReturn(tubepress_core_media_search_api_Constants::OUTPUT_SEARCH_INPUT);

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array('search/search_input.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/search/search_input.tpl.php'))->andReturn($mockTemplate);

        $mockTemplateEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockTemplate)->andReturn($mockTemplateEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_media_search_api_Constants::EVENT_TEMPLATE_SEARCH_INPUT, $mockTemplateEvent);

        $mockHtmlEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('bla');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('template-string')->andReturn($mockHtmlEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_media_search_api_Constants::EVENT_HTML_SEARCH_INPUT, $mockHtmlEvent);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('bla');
        $this->_mockEvent->shouldReceive('stopPropagation');

        $this->_sut->onHtmlGeneration($this->_mockEvent);

        $this->assertTrue(true);
    }
}