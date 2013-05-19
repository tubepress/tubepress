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
class tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionDescriptorReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_mockOptionDescriptorReference   = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockMessageService              = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockTemplateBuilder             = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut = new tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder();
    }

    public function testMetaDropdown()
    {
        $this->_setupOptionDescriptorReferenceForMetaMultiSelect();

        $mockProvider1 = ehough_mockery_Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockProvider1->shouldReceive('getAdditionalMetaNames')->once()->andReturn(array('xyz'));

        $mockProvider2 = ehough_mockery_Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockProvider2->shouldReceive('getAdditionalMetaNames')->once()->andReturn(array('abc'));

        $this->_sut->setPluggableVideoProviders(array($mockProvider1, $mockProvider2));

        $result = $this->_sut->build('metadropdown', 'tubepress_impl_options_ui_fields_MetaMultiSelectField');

        $this->assertTrue($result instanceof tubepress_impl_options_ui_fields_MetaMultiSelectField);
    }

    public function testNonMetaDropdown()
    {
        $result = $this->_sut->build('something', 'something else');

        $this->assertNull($result);
    }

    private function _getOdNames()
    {
        return array(

            tubepress_api_const_options_names_Meta::AUTHOR,
            tubepress_api_const_options_names_Meta::CATEGORY,
            tubepress_api_const_options_names_Meta::DESCRIPTION,
            tubepress_api_const_options_names_Meta::ID,
            tubepress_api_const_options_names_Meta::LENGTH,
            'abc',
            'xyz',
            tubepress_api_const_options_names_Meta::KEYWORDS,
            tubepress_api_const_options_names_Meta::TITLE,
            tubepress_api_const_options_names_Meta::UPLOADED,
            tubepress_api_const_options_names_Meta::URL,
            tubepress_api_const_options_names_Meta::VIEWS,
        );
    }

    private function _setupOptionDescriptorReferenceForMetaMultiSelect()
    {
        $names = $this->_getOdNames();

        $ods = array();

        foreach ($names as $name) {

            $od = new tubepress_spi_options_OptionDescriptor($name);
            $od->setBoolean();

            $ods[] = $od;

            $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->with($name)->once()->andReturn($od);
        }

        return $ods;
    }
}