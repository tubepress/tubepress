<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_options_DefaultOptionDescriptorReferenceTest extends TubePressUnitTest
{
	private $_sut;

    private $_storageManager;

	public function onSetup()
	{
		$this->_sut = new tubepress_impl_options_DefaultOptionDescriptorReference();

        $this->_storageManager = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);

	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testRegisterDuplicate()
	{
	    $od = new tubepress_spi_options_OptionDescriptor('name');
        $od->setDefaultValue('xyz');

        $this->_storageManager->shouldReceive('createIfNotExists')->once()->with('name', 'xyz');

	    $this->_sut->registerOptionDescriptor($od);
	    $this->_sut->registerOptionDescriptor($od);
	}

    public function testGetAll()
    {
        $od = new tubepress_spi_options_OptionDescriptor('name');

        $od->setDefaultValue('xyz');

        $this->_storageManager->shouldReceive('createIfNotExists')->once()->with('name', 'xyz');

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
        $od->setDefaultValue('xyz');

        $this->_storageManager->shouldReceive('createIfNotExists')->once()->with('name', 'xyz');

        $this->_sut->registerOptionDescriptor($od);

        $result = $this->_sut->findOneByName('name');

        $this->assertSame($od, $result);
    }
}

