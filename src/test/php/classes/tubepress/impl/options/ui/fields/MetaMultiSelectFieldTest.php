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
class tubepress_impl_options_ui_fields_MetaMultiSelectFieldTest extends tubepress_impl_options_ui_fields_AbstractFieldTest
{
    private $_sut;

    private $_mockOptionDescriptorArrary;

    private $_mockOptionDescriptorReference;

    private $_mockMessageService;

    private $_mockStorageManager;

    private $_mockHttpRequestParameterService;

    private $_mockTemplateBuilder;

    private $_mockEnvironmentDetector;

    public function setUp()
    {
        $this->_mockOptionDescriptorReference = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockOptionDescriptorArrary    = $this->_buildMockOptionDescriptorArray();
        $this->_mockMessageService            = Mockery::mock(tubepress_spi_message_MessageService::_);

        $this->_mockStorageManager              = Mockery::mock(tubepress_spi_options_StorageManager::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockTemplateBuilder             = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEnvironmentDetector         = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionDescriptorReference);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($this->_mockMessageService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager($this->_mockStorageManager);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);

        $this->doSetup($this->_mockMessageService);

        $this->_sut = new tubepress_impl_options_ui_fields_MetaMultiSelectField();
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

    public function testGetProviders()
    {
        $this->assertEquals(array(
            'youtube',
            'vimeo',
            ), $this->_sut->getArrayOfApplicableProviderNames());
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
        $template     = \Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_NAME, 'metadropdown');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_DESCRIPTORS, $this->_mockOptionDescriptorArrary);
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_CURRENTVALUES, $this->_getOdNames());
        $template->shouldReceive('toString')->once()->andReturn('boogity');
        $odNames = $this->_getOdNames();

        foreach ($odNames as $odName) {

            $this->_mockStorageManager->shouldReceive('get')->once()->with($odName)->andReturn(true);
        }

        $this->_mockEnvironmentDetector->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath>>/src/main/resources/system-templates/options_page/fields/multiselect.tpl.php')->andReturn($template);

        $this->assertEquals('boogity', $this->_sut->getHtml());
    }

    protected function _getOdNames()
    {
        return array(

            tubepress_api_const_options_names_Meta::AUTHOR,
            tubepress_api_const_options_names_Meta::RATING,
            tubepress_api_const_options_names_Meta::CATEGORY,
            tubepress_api_const_options_names_Meta::UPLOADED,
            tubepress_api_const_options_names_Meta::DESCRIPTION,
            tubepress_api_const_options_names_Meta::ID,
            tubepress_api_const_options_names_Meta::KEYWORDS,
            tubepress_api_const_options_names_Meta::LIKES,
            tubepress_api_const_options_names_Meta::RATINGS,
            tubepress_api_const_options_names_Meta::LENGTH,
            tubepress_api_const_options_names_Meta::TITLE,
            tubepress_api_const_options_names_Meta::URL,
            tubepress_api_const_options_names_Meta::VIEWS,
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

