<?php

require_once 'AbstractFieldTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/fields/FilterMultiSelectField.class.php';

class org_tubepress_impl_options_ui_fields_FilterMultiSelectFieldTest extends org_tubepress_impl_options_ui_fields_AbstractFieldTest {

    private $_sut;
    
    public function setUp()
    {
        parent::setUp();
        
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
        
        $mockYouTubeOptions = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
        $mockYouTubeOptions->shouldReceive('isBoolean')->once()->andReturn(true);
        $mockVimeoOptions = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
        $mockVimeoOptions->shouldReceive('isBoolean')->once()->andReturn(true);
        
        $odr->shouldReceive('findOneByName')->once()->with(org_tubepress_api_const_options_names_WordPress::SHOW_VIMEO_OPTIONS)->andReturn($mockVimeoOptions);
        $odr->shouldReceive('findOneByName')->once()->with(org_tubepress_api_const_options_names_WordPress::SHOW_YOUTUBE_OPTIONS)->andReturn($mockYouTubeOptions);
        
        $this->_sut = new org_tubepress_impl_options_ui_fields_FilterMultiSelectField();
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

