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

class tubepress_impl_options_DefaultOptionValidatorTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockOptionsDescriptorReference;

	public function onSetup()
	{
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);

		$this->_sut = new tubepress_impl_options_DefaultOptionValidator($this->_mockOptionsDescriptorReference);
	}

	public function testNoConstraints()
	{
	    $od = new tubepress_spi_options_OptionDescriptor('name');

	    $this->_mockOptionsDescriptorReference->shouldReceive('findOneByName')->twice()->with('name')->andReturn($od);

	    $this->assertTrue($this->_sut->isValid('name', 'poo') === true);
	    $this->assertEquals(null, $this->_sut->getProblemMessage('name', 'poo'));
	}

	public function testBoolean()
	{
        $od = new tubepress_spi_options_OptionDescriptor('name');
        $od->setBoolean();

        $this->_mockOptionsDescriptorReference->shouldReceive('findOneByName')->atLeast()->once()->with('name')->andReturn($od);

	    $this->assertTrue($this->_sut->isValid('name', 'poo') === false);
	    $this->assertEquals('"name" can only accept true/false values. You supplied "poo".', $this->_sut->getProblemMessage('name', 'poo'));
	    $this->assertTrue($this->_sut->isValid('name', true) === true);
	}

	public function testDiscreteValues()
	{
        $od = new tubepress_spi_options_OptionDescriptor('name');

	    $od->setAcceptableValues(array('biz' => 'bar', 'butt' => 'two'));

        $this->_mockOptionsDescriptorReference->shouldReceive('findOneByName')->atLeast()->once()->with('name')->andReturn($od);

	    $this->assertTrue($this->_sut->isValid('name', 'foo') === false);
	    $this->assertEquals('"name" must be one of {biz, butt}. You supplied "foo".', $this->_sut->getProblemMessage('name', 'foo'));
	    $this->assertTrue($this->_sut->isValid('name', 'biz') === true);
	}

	public function testBadRegex()
    {
        $od = new tubepress_spi_options_OptionDescriptor('name');
        $od->setValidValueRegex('/t{5}/i');

        $this->_mockOptionsDescriptorReference->shouldReceive('findOneByName')->atLeast()->once()->with('name')->andReturn($od);

		$this->assertTrue($this->_sut->isValid('name', 90) === false);
		$this->assertEquals('"name" must match the regular expression /t{5}/i. You supplied "90".', $this->_sut->getProblemMessage('name', 90));
		$this->assertTrue($this->_sut->isValid('name', 'tTtTt') === true);
	}


	public function testNotExists()
	{
        $this->_mockOptionsDescriptorReference->shouldReceive('findOneByName')->once()->with('name')->andReturn(null);

		$this->assertTrue($this->_sut->isValid('name', 120) === false);
	}
}

