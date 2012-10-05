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

class org_tubepress_impl_options_DefaultOptionValidatorTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockOptionsDescriptorReference;

	public function setup()
	{
        $this->_mockOptionsDescriptorReference = \Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionsDescriptorReference);

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

