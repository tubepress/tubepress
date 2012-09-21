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

    private $_sut;

    private $_mockOptionDescriptorReference;

    private $_mockMessageService;

    private $_mockStorageManager;

    private $_mockHttpRequestParameterService;

    private $_mockTemplateBuilder;

    private $_mockEnvironmentDetector;
    
    public function setUp()
    {
        $this->_mockOptionDescriptorReference   = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockMessageService              = Mockery::mock(tubepress_spi_message_MessageService::_);
        $this->_mockStorageManager              = Mockery::mock(tubepress_spi_options_StorageManager::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockTemplateBuilder             = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEnvironmentDetector         = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);

        $mockYouTubeOptions = \Mockery::mock(tubepress_spi_options_OptionDescriptor::_);
        $mockYouTubeOptions->shouldReceive('isBoolean')->once()->andReturn(true);

        $mockVimeoOptions = \Mockery::mock(tubepress_spi_options_OptionDescriptor::_);
        $mockVimeoOptions->shouldReceive('isBoolean')->once()->andReturn(true);

        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with(tubepress_plugins_wordpresscore_lib_api_const_options_names_WordPress::SHOW_VIMEO_OPTIONS)->andReturn($mockVimeoOptions);
        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with(tubepress_plugins_wordpresscore_lib_api_const_options_names_WordPress::SHOW_YOUTUBE_OPTIONS)->andReturn($mockYouTubeOptions);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionDescriptorReference);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($this->_mockMessageService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager($this->_mockStorageManager);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);

        parent::doSetup($this->_mockMessageService);

        $this->_sut = new tubepress_impl_options_ui_fields_FilterMultiSelectField();
    }
    
    public function testGetTitle()
    {
        $this->assertEquals('<<message: Only show options applicable to...>>', $this->_sut->getTitle());
    }
    
    public function testGetDescription()
    {
        $this->assertEquals('', $this->_sut->getDescription());
    }
}

