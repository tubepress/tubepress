<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
abstract class tubepress_impl_options_ui_tabs_AbstractTabTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab
     */
    private $_sut;

    private $_mockFieldBuilder;

    private $_mockTemplateBuilder;

    public function onSetup()
    {
        global $tubepress_base_url;

        $tubepress_base_url = 'tubepress-base-url';

        $ms                             = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockTemplateBuilder     = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockFieldBuilder        = $this->createMockSingletonService(tubepress_spi_options_ui_FieldBuilder::_);

        $ms->shouldReceive('_')->andReturnUsing( function ($key) {

            return "<<message: $key>>";
        });

        $this->_sut = $this->_buildSut();
    }

    protected function onTearDown()
    {
        global $tubepress_base_url;

        unset($tubepress_base_url);
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

        $mockOptionsPageParticipant1->shouldReceive('getFieldsForTab')->once()->with($this->_sut->getName())->andReturn(array());
        $mockOptionsPageParticipant2->shouldReceive('getFieldsForTab')->once()->with($this->_sut->getName())->andReturn(array('x'));

        $template = \Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_PARTICIPANT_ARRAY, array($mockOptionsPageParticipant2));
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_TAB_NAME, $this->_sut->getName());
        $template->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, 'tubepress-base-url');
        $template->shouldReceive('toString')->once()->andReturn('final result');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tab.tpl.php')->andReturn($template);

        $this->assertEquals('final result', $this->_sut->getHtml());
    }

    public function testGetDelegateFormHandlers()
    {
        $mockOptionsPageParticipant1          = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
        $mockOptionsPageParticipant2          = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);

        $fakeField1 = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $fakeField2 = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $fakeField3 = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);

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