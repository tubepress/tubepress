<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/SimpleOptionValidator.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/const/options/names/Meta.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/const/options/names/Advanced.class.php';

class org_tubepress_impl_options_SimpleOptionValidatorTest extends TubePressUnitTest {

	private $_sut;

	public function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_options_SimpleOptionValidator();
	}

	public function testExists()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
	    
	    $od = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
	    
	    $odr->shouldReceive('findOneByName')->once()->with(org_tubepress_api_const_options_names_Display::THUMB_HEIGHT)->andReturn($od);
	    
		$this->assertTrue($this->_sut->isValid(org_tubepress_api_const_options_names_Display::THUMB_HEIGHT, 90) === true);
	}


	public function testNotExists()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
	     
	    $odr->shouldReceive('findOneByName')->once()->with(org_tubepress_api_const_options_names_Display::THUMB_WIDTH)->andReturn(null);
	    
		$this->assertTrue($this->_sut->isValid(org_tubepress_api_const_options_names_Display::THUMB_WIDTH, 120) === false);
	}
}

