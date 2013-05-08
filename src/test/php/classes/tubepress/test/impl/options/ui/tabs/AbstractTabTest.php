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
abstract class tubepress_test_impl_options_ui_tabs_AbstractTabTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFieldBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $ms                             = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockTemplateBuilder     = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockFieldBuilder        = $this->createMockSingletonService(tubepress_spi_options_ui_FieldBuilder::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockEventDispatcher     = $this->createMockSingletonService('ehough_tickertape_EventDispatcherInterface');

        $ms->shouldReceive('_')->andReturnUsing( function ($key) {

            return "<<message: $key>>";
        });

        $this->_sut = $this->_buildSut();
    }

    public function testGetName()
    {
        $this->assertEquals('<<message: ' . $this->_getRawTitle() . '>>', $this->_sut->getTitle());
    }

    public function testGetHtml()
    {
        $mockOptionsPageParticipant1          = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
        $mockOptionsPageParticipant2          = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
        $mockPluggableOptionsPageParticipants = array($mockOptionsPageParticipant1, $mockOptionsPageParticipant2);

        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('<tubepress_base_url>');

        $mockOptionsPageParticipant1->shouldReceive('getFieldsForTab')->once()->with($this->_sut->getName())->andReturn(array());
        $mockOptionsPageParticipant2->shouldReceive('getFieldsForTab')->once()->with($this->_sut->getName())->andReturn(array('x'));

        $template = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_PARTICIPANT_ARRAY, array($mockOptionsPageParticipant2));
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_TAB_NAME, $this->_sut->getName());
        $template->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, '<tubepress_base_url>');
        $template->shouldReceive('toString')->once()->andReturn('final result');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('path to template')->andReturn($template);

        $tabName = $this->_sut->getName();

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::TEMPLATE_OPTIONS_UI_TABS_SINGLE, ehough_mockery_Mockery::on(function ($event) use ($tabName) {

            return $event instanceof tubepress_api_event_EventInterface && $event->getArgument('tabName') === $tabName;
        }));

        $this->assertEquals('final result', $this->_sut->getHtml());
    }

    public function testGetDelegateFormHandlers()
    {
        $mockOptionsPageParticipant1          = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
        $mockOptionsPageParticipant2          = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);

        $fakeField1 = ehough_mockery_Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $fakeField2 = ehough_mockery_Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $fakeField3 = ehough_mockery_Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);

        $mockOptionsPageParticipant1->shouldReceive('getFieldsForTab')->once()->with($this->_sut->getName())->andReturn(array($fakeField1));
        $mockOptionsPageParticipant2->shouldReceive('getFieldsForTab')->once()->with($this->_sut->getName())->andReturn(array($fakeField2, $fakeField3));

        $result = $this->_sut->getDelegateFormHandlers();

        $expected = array($fakeField1, $fakeField2, $fakeField3);

        $this->assertEquals($expected, $result);
    }

    protected function getFieldBuilder()
    {
        return $this->_mockFieldBuilder;
    }

    protected abstract function _getRawTitle();

    protected abstract function _buildSut();
}