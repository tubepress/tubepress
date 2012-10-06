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
class tubepress_impl_options_DefaultOptionDescriptorReferenceTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockEventDispatcher;

	public function setup()
	{
        $this->_mockEventDispatcher = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);

        $this->_mockEventDispatcher->shouldReceive('addListener')->once()->with(tubepress_api_const_event_CoreEventNames::OPTION_STORAGE_MANAGER_READY,

            Mockery::on(function ($arg) {

                return $arg[0] instanceof tubepress_impl_options_DefaultOptionDescriptorReference;
            })
        );

		$this->_sut = new tubepress_impl_options_DefaultOptionDescriptorReference();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testRegisterDuplicate()
	{
	    $od = new tubepress_spi_options_OptionDescriptor('name');

	    $this->_sut->registerOptionDescriptor($od);
	    $this->_sut->registerOptionDescriptor($od);
	}

    public function testGetAll()
    {
        $od = new tubepress_spi_options_OptionDescriptor('name');

        $this->_sut->registerOptionDescriptor($od);

        $result = $this->_sut->findAll();

        $this->assertTrue(is_array($result));
        $this->assertSame($od, $result[0]);
    }

    public function testFindOne()
    {
        $result = $this->_sut->findOneByName('x');

        $this->assertNull($result);

        $od = new tubepress_spi_options_OptionDescriptor('name');

        $this->_sut->registerOptionDescriptor($od);

        $result = $this->_sut->findOneByName('name');

        $this->assertSame($od, $result);
    }
}

