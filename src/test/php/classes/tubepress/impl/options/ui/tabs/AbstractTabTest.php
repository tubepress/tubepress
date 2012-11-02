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
abstract class tubepress_impl_options_ui_tabs_AbstractTabTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockFieldBuilder;

    private $_mockTemplateBuilder;

    private $_mockServiceCollectionsRegistry;

    public function setup()
    {
        $ms                             = Mockery::mock(tubepress_spi_message_MessageService::_);
        $this->_mockTemplateBuilder     = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockFieldBuilder        = Mockery::mock(tubepress_spi_options_ui_FieldBuilder::_);
        $this->_mockServiceCollectionsRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($ms);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionsUiFieldBuilder($this->_mockFieldBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);

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
        $expected = $this->getHardcodedFieldArray();

        $fakeExtraField = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $fakeExtraField->shouldReceive('getDesiredTabName')->once()->andReturn($this->_sut->getName());
        $fakeExtraFields = array($fakeExtraField);
        $expectedFieldArray = $this->getAdditionalFields();

        foreach ($expected as $name => $type) {

            $this->_mockFieldBuilder->shouldReceive('build')->once()->with($name, $type, $this->_sut->getName())->andReturn("$name-$type");
            $expectedFieldArray[] = "$name-$type";
        }

        $allFields = array_merge($expectedFieldArray, $fakeExtraFields);

        $template = \Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_FIELDARRAY, $allFields);
        $template->shouldReceive('toString')->once()->andReturn('final result');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tab.tpl.php')->andReturn($template);



        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_options_ui_Field::CLASS_NAME)->andReturn(

            $fakeExtraFields
        );

        $this->assertEquals('final result', $this->_sut->getHtml());
    }

    protected function getAdditionalFields()
    {
        return array();
    }

    protected function getFieldBuilder()
    {
        return $this->_mockFieldBuilder;
    }

    protected abstract function getHardcodedFieldArray();

    protected abstract function _getRawTitle();

    protected abstract function _buildSut();
}