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
class tubepress_test_impl_options_ui_DefaultFormHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_DefaultFormHandler
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTabs;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFilter;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockTabs                = $this->createMockSingletonService(tubepress_spi_options_ui_FormHandler::_);
        $this->_mockMessageService      = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockTemplateBuilder     = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEventDispatcher     = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);

        $this->_mockFilter = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_Field');

        $this->_sut = new tubepress_impl_options_ui_DefaultFormHandler($this->_mockTabs, $this->_mockFilter, 'some path');
    }


    public function testGetFailureMessagesOneError()
    {
        $this->_mockTabs->shouldReceive('onSubmit')->once()->andReturn(array('holy smokes!'));
        $this->_mockFilter->shouldReceive('onSubmit')->once()->andReturn(null);

        $this->assertEquals(array('holy smokes!'), $this->_sut->onSubmit());
    }

    public function testOnSubmit()
    {
        $this->_mockTabs->shouldReceive('onSubmit')->once()->andReturn(null);
        $this->_mockFilter->shouldReceive('onSubmit')->once()->andReturn(null);

        $this->assertNull($this->_sut->onSubmit());
    }

    public function testGetHtml()
    {
        $template = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('some path')->andReturn($template);

        $this->_mockMessageService->shouldReceive('_')->once()->with('Save')->andReturn('<<save>>');

        $this->_mockTabs->shouldReceive('getHtml')->once()->andReturn('<<tabhtml>>');

        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_DefaultFormHandler::TEMPLATE_VAR_SAVE_TEXT, '<<save>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_DefaultFormHandler::TEMPLATE_VAR_SAVE_ID, 'tubepress_save');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_DefaultFormHandler::TEMPLATE_VAR_TABS, '<<tabhtml>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_DefaultFormHandler::TEMPLATE_VAR_FILTER, $this->_mockFilter);
        $template->shouldReceive('toString')->once()->andReturn('foo');

        $this->_mockEventDispatcher->shouldReceive('publish')->once()->with(tubepress_api_const_event_EventNames::TEMPLATE_OPTIONS_UI_MAIN, ehough_mockery_Mockery::on(function ($event) use ($template) {

            return $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === $template;
        }));

        $this->assertEquals('foo', $this->_sut->getHtml());
    }
}