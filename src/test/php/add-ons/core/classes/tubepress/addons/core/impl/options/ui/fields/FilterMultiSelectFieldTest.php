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
 * @covers tubepress_addons_core_impl_options_ui_fields_FilterMultiSelectField<extended>
 */
class tubepress_test_impl_options_ui_fields_FilterMultiSelectFieldTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_options_ui_fields_FilterMultiSelectField
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionDescriptorReference;

    /**
     * @var tubepress_spi_message_MessageService
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
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

        $this->_sut = new tubepress_addons_core_impl_options_ui_fields_FilterMultiSelectField(array());
    }

    public function testGetHtml()
    {
        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');

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
        $mockOptionsPageParticipant = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
        $mockOptionsPageParticipant->shouldReceive('getName')->twice()->andReturn('filter-name');
        $mockOptionsPageParticipant->shouldReceive('getFriendlyName')->once()->andReturn('filter-friendly-name');
        $mockOptionsPageParticipant2 = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
        $mockOptionsPageParticipant2->shouldReceive('getName')->twice()->andReturn('filter-name-2');
        $mockOptionsPageParticipant2->shouldReceive('getFriendlyName')->once()->andReturn('filter-friendly-name-2');
        $mockOptionsPageParticipant3 = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
        $mockOptionsPageParticipant3->shouldReceive('getName')->twice()->andReturn('filter-name-3');
        $mockOptionsPageParticipant3->shouldReceive('getFriendlyName')->once()->andReturn('filter-friendly-name-3');

        $this->_sut->setPluggableOptionsPageParticipants(array($mockOptionsPageParticipant, $mockOptionsPageParticipant2, $mockOptionsPageParticipant3));
    }
}

