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
class tubepress_impl_options_ui_fields_FilterMultiSelectFieldTest extends tubepress_impl_options_ui_fields_AbstractFieldTest
{
    /**
     * @var tubepress_impl_options_ui_fields_FilterMultiSelectField
     */
    private $_sut;

    private $_mockOptionDescriptorReference;

    /**
     * @var tubepress_spi_message_MessageService
     */
    private $_mockMessageService;

    private $_mockStorageManager;
    
    private $_mockHttpRequestParameterService;

    private $_mockTemplateBuilder;

    public function onSetup()
    {
        $this->_mockOptionDescriptorReference   = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockMessageService              = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockTemplateBuilder             = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');

        $mockOption = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS);
        $mockOption->setLabel('some crazy title');

        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn($mockOption);

        parent::doSetup($this->_mockMessageService);

        $this->_sut = new tubepress_impl_options_ui_fields_FilterMultiSelectField();
    }

    public function testGetHtml()
    {
        $mockTemplate = Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(

            TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/fields/multiselect-provider-filter.tpl.php'
        )->andReturn($mockTemplate);

        $this->_mockStorageManager->shouldReceive('get')->once()->with(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn('filter-name;filter-name-3');

        $this->setupMockFilters();

        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_FilterMultiSelectField::TEMPLATE_VAR_NAME, tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_FilterMultiSelectField::TEMPLATE_VAR_PROVIDERS, array(

            'filter-name'   => 'filter-friendly-name',
            'filter-name-2' => 'filter-friendly-name-2',
            'filter-name-3' => 'filter-friendly-name-3'
        ));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_FilterMultiSelectField::TEMPLATE_VAR_CURRENTVALUES, array(

            'filter-name-2'
        ));
        $mockTemplate->shouldReceive('toString')->once()->andReturn('xyz');

        $result = $this->_sut->getHtml();

        $this->assertEquals('xyz', $result);
    }

    public function testGetTitle()
    {
        $this->assertEquals('<<message: some crazy title>>', $this->_sut->getTitle());
    }
    
    public function testGetDescription()
    {
        $this->assertEquals('', $this->_sut->getDescription());
    }

    public function testIsProOnly()
    {
        $this->assertFalse($this->_sut->isProOnly());
    }

    public function testOnSubmitOnlyShowOne()
    {
        $this->setupMockFilters();

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn(

            array('filter-name-2')
        );

        $this->_mockStorageManager->shouldReceive('set')->once()->with(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS, 'filter-name;filter-name-3')->andReturn(true);

        $this->assertNull($this->_sut->onSubmit());
    }

    public function testOnSubmitNonArray()
    {
        $this->setupMockFilters();

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn('');

        $this->assertNull($this->_sut->onSubmit());
    }

    public function testOnSubmitEverythingMissing()
    {
        $this->setupMockFilters();

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn(false);

        $this->_mockStorageManager->shouldReceive('set')->once()->with(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS, 'filter-name;filter-name-2;filter-name-3');

        $this->assertNull($this->_sut->onSubmit());
    }

    private function setupMockFilters()
    {
        $mockFilter = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
        $mockFilter->shouldReceive('getName')->twice()->andReturn('filter-name');
        $mockFilter->shouldReceive('getFriendlyName')->once()->andReturn('filter-friendly-name');
        $mockFilter2 = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
        $mockFilter2->shouldReceive('getName')->twice()->andReturn('filter-name-2');
        $mockFilter2->shouldReceive('getFriendlyName')->once()->andReturn('filter-friendly-name-2');
        $mockFilter3 = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
        $mockFilter3->shouldReceive('getName')->twice()->andReturn('filter-name-3');
        $mockFilter3->shouldReceive('getFriendlyName')->once()->andReturn('filter-friendly-name-3');
    }
}

