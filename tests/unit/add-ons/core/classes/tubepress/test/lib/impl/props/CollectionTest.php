<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_platform_impl_property_Collection<extended>
 */
class tubepress_test_lib_impl_props_CollectionTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_platform_impl_property_Collection
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut  = new tubepress_platform_impl_property_Collection();
    }

    public function testHas()
    {
        $this->assertFalse($this->_sut->has('foo'));

        $this->_sut->set('foo', 'bar');

        $this->assertTrue($this->_sut->has('foo'));
    }

    public function testGetAsBoolean()
    {
        $this->_sut->set('foo', '0');
        $this->assertTrue($this->_sut->getAsBoolean('foo') === false);
    }

    public function testGetAll()
    {
        $this->_sut->set('foo', 33);
        $this->_sut->set('bar', 'hi');
        $this->assertEquals(array('foo', 'bar'), $this->_sut->getAllNames());
    }

    public function testGet()
    {
        $this->_sut->set('foo', 33);
        $this->assertEquals(33, $this->_sut->get('foo'));
    }

    public function testGetNoExist()
    {
        $this->setExpectedException('InvalidArgumentException', 'No such property: foo');

        $this->_sut->get('foo');
    }
}