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
 * @covers tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField<extended>
 */
class tubepress_test_impl_options_ui_fields_MetaMultiSelectFieldTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionDescriptorArrary;

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
        $this->_mockOptionDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockOptionDescriptorArrary    = $this->_buildMockOptionDescriptorArray();
        $this->_mockMessageService            = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);

        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockTemplateBuilder             = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $mockProvider1 = ehough_mockery_Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockProvider1->shouldReceive('getAdditionalMetaNames')->once()->andReturn(array('xyz'));

        $mockProvider2 = ehough_mockery_Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockProvider2->shouldReceive('getAdditionalMetaNames')->once()->andReturn(array('abc'));


        $this->_sut = new tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField(array($mockProvider1, $mockProvider2));
    }

    public function testGetTitle()
    {
        $this->assertEquals('<<message: Show each video\'s...>>', $this->_sut->getTitle());
    }

    public function testGetDesc()
    {
        $this->assertEquals('', $this->_sut->getDescription());
    }

    public function testIsProOnly()
    {
        $this->assertFalse($this->_sut->isProOnly());
    }

    public function testOnSubmit()
    {

        $odNames      = $this->_getOdNames();
        $indexOfFalse = array_rand($odNames);
        $postVars     = array();

        for ($x = 0; $x < count($odNames); $x++) {

            $keep = $x !== $indexOfFalse;

            $this->_mockStorageManager->shouldReceive('set')->once()->with($odNames[$x], $keep);

            if ($keep) {

                $postVars[] = $odNames[$x];
            }
        }

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('metadropdown')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('metadropdown')->andReturn($postVars);

        $this->_sut->onSubmit(array('metadropdown' => $postVars));

        $this->assertTrue(true);
    }

    public function testGetHtml()
    {
        $template     = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_NAME, 'metadropdown');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_DESCRIPTORS, $this->_mockOptionDescriptorArrary);
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_CURRENTVALUES, $this->_getOdNames());
        $template->shouldReceive('toString')->once()->andReturn('boogity');
        $odNames = $this->_getOdNames();

        foreach ($odNames as $odName) {

            $this->_mockStorageManager->shouldReceive('get')->once()->with($odName)->andReturn(true);
        }

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/fields/multiselect.tpl.php')->andReturn($template);

        $this->assertEquals('boogity', $this->_sut->getHtml());
    }

    protected function _getOdNames()
    {
        return array(

            tubepress_api_const_options_names_Meta::AUTHOR,

            tubepress_api_const_options_names_Meta::CATEGORY,
            tubepress_api_const_options_names_Meta::UPLOADED,
            tubepress_api_const_options_names_Meta::DESCRIPTION,
            tubepress_api_const_options_names_Meta::ID,
            tubepress_api_const_options_names_Meta::KEYWORDS,
            tubepress_api_const_options_names_Meta::LENGTH,
            tubepress_api_const_options_names_Meta::TITLE,
            tubepress_api_const_options_names_Meta::URL,
            tubepress_api_const_options_names_Meta::VIEWS,
            'xyz',
            'abc',
        );
    }

    private function _buildMockOptionDescriptorArray()
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
