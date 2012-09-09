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
class FakeThingy
{
    public $_arg;

    public function __construct($arg)
    {
        $this->_arg = $arg;
    }
}

class org_tubepress_impl_options_ui_DefaultFieldBuilderTest extends PHPUnit_Framework_TestCase
{
	private $_sut;

    private $_mockOptionDescriptorReference;

    private $_mockMessageService;

    private $_mockStorageManager;

    private $_mockHttpRequestParameterService;

    private $_mockTemplateBuilder;

    private $_mockEnvironmentDetector;

	public function setup()
	{
        $this->_mockOptionDescriptorReference   = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockMessageService              = Mockery::mock(tubepress_spi_message_MessageService::_);
        $this->_mockStorageManager              = Mockery::mock(tubepress_spi_options_StorageManager::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockTemplateBuilder             = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEnvironmentDetector         = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut = new tubepress_impl_options_ui_DefaultFieldBuilder(

            $this->_mockMessageService,
            $this->_mockHttpRequestParameterService,
            $this->_mockEnvironmentDetector,
            $this->_mockTemplateBuilder,
            $this->_mockStorageManager,
            $this->_mockOptionDescriptorReference);
	}

	public function testBuild()
	{
        $result = $this->_sut->build('something awesome', 'FakeThingy');

        $this->assertTrue($result instanceof FakeThingy);
        $this->assertEquals('something awesome', $result->_arg);
	}
	
    public function testBuildMeta()
    {
        $this->_setupOptionDescriptorReferenceForMetaMultiSelect();
        
        $result = $this->_sut->buildMetaDisplayMultiSelectField();
        
        $this->assertTrue($result instanceof tubepress_impl_options_ui_fields_MetaMultiSelectField);
    }
    
    private function _getOdNames()
    {
        return array(
    
            tubepress_api_const_options_names_Meta::AUTHOR,
            tubepress_api_const_options_names_Meta::CATEGORY,
            tubepress_api_const_options_names_Meta::DESCRIPTION,
            tubepress_api_const_options_names_Meta::ID,
            tubepress_api_const_options_names_Meta::LENGTH,
            tubepress_api_const_options_names_Meta::LIKES,
            tubepress_api_const_options_names_Meta::RATING,
            tubepress_api_const_options_names_Meta::RATINGS,
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
    
            $od = \Mockery::mock(tubepress_spi_options_OptionDescriptor::_);

            $od->shouldReceive('isBoolean')->once()->andReturn(true);
            $od->shouldReceive('getName')->andReturn($name);
    
            $ods[] = $od;
    
            $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->with($name)->once()->andReturn($od);
        }
    
        return $ods;
    }
}

