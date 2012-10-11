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
class tubepress_impl_options_ui_tabs_GallerySourceTabTest extends TubePressUnitTest
{
    private $_mockFieldBuilder;

    private $_mockTemplateBuilder;

    private $_mockExecutionContext;

    private $_mockServiceCollectionsRegistry;

    public function setup()
    {
        $ms                             = Mockery::mock(tubepress_spi_message_MessageService::_);
        $this->_mockTemplateBuilder     = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockFieldBuilder        = Mockery::mock(tubepress_spi_options_ui_FieldBuilder::_);
        $this->_mockExecutionContext    = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockServiceCollectionsRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);

        $ms->shouldReceive('_')->andReturnUsing( function ($key) {

            return "<<message: $key>>";
        });

        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($ms);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionsUiFieldBuilder($this->_mockFieldBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);

        $this->_sut = new tubepress_impl_options_ui_tabs_GallerySourceTab();
    }

    public function testGetName()
    {
        $this->assertEquals('<<message: Which videos?>>', $this->_sut->getTitle());
    }

    public function testGetHtml()
    {
        $fakeExtraField = Mockery::mock(tubepress_spi_options_ui_PluggableOptionsPageField::CLASS_NAME);
        $fakeExtraField->shouldReceive('getDesiredTabName')->once()->andReturn($this->_sut->getName());
        $fakeExtraFields = array($fakeExtraField);

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_options_ui_PluggableOptionsPageField::CLASS_NAME)->andReturn(

            $fakeExtraFields
        );

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('some mode');

        $template = \Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_tabs_GallerySourceTab::TEMPLATE_VAR_CURRENT_MODE, 'some mode');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_FIELDARRAY, $fakeExtraFields);
        $template->shouldReceive('toString')->once()->andReturn('final result');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/gallery_source_tab.tpl.php')->andReturn($template);

        $this->assertEquals('final result', $this->_sut->getHtml());
    }

    private function getFieldArray()
    {
        return array();
    }
}