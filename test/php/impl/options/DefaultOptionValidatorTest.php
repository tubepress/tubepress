<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/DefaultOptionValidator.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/const/options/names/Meta.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/const/options/names/Advanced.class.php';

class org_tubepress_impl_options_DefaultOptionValidatorTest extends TubePressUnitTest {

	private $_sut;

	public function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_options_DefaultOptionValidator();
	}

	public function testNoConstraints()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);

	    $od = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
	    $od->shouldReceive('hasValidValueRegex')->once()->andReturn(false);
	    $od->shouldReceive('hasDiscreteAcceptableValues')->once()->andReturn(false);
	    $od->shouldReceive('isBoolean')->once()->andReturn(false);

	    $odr->shouldReceive('findOneByName')->once()->with('name')->andReturn($od);

	    $this->assertTrue($this->_sut->isValid('name', 'poo') === true);
	}

	public function testBoolean()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);

	    $od = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
	    $od->shouldReceive('hasValidValueRegex')->twice()->andReturn(false);
	    $od->shouldReceive('hasDiscreteAcceptableValues')->twice()->andReturn(false);
	    $od->shouldReceive('isBoolean')->twice()->andReturn(true);

	    $odr->shouldReceive('findOneByName')->twice()->with('name')->andReturn($od);

	    $this->assertTrue($this->_sut->isValid('name', 'poo') === false);
	    $this->assertTrue($this->_sut->isValid('name', true) === true);
	}

	public function testDiscreteValues()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);

	    $od = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
	    $od->shouldReceive('hasValidValueRegex')->twice()->andReturn(false);
	    $od->shouldReceive('hasDiscreteAcceptableValues')->twice()->andReturn(true);
	    $od->shouldReceive('getAcceptableValues')->twice()->andReturn(array('bar', 'two'));

	    $odr->shouldReceive('findOneByName')->twice()->with('name')->andReturn($od);

	    $this->assertTrue($this->_sut->isValid('name', 'foo') === false);
	    $this->assertTrue($this->_sut->isValid('name', 'bar') === true);
	}

	public function testBadRegex()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);

	    $od = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
	    $od->shouldReceive('hasValidValueRegex')->twice()->andReturn(true);
	    $od->shouldReceive('getValidValueRegex')->times(3)->andReturn('/t{5}/i');

	    $odr->shouldReceive('findOneByName')->twice()->with('name')->andReturn($od);

		$this->assertTrue($this->_sut->isValid('name', 90) === false);
		$this->assertTrue($this->_sut->isValid('name', 'tTtTt') === true);
	}


	public function testNotExists()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);

	    $odr->shouldReceive('findOneByName')->once()->with('name')->andReturn(null);

		$this->assertTrue($this->_sut->isValid('name', 120) === false);
	}
}

