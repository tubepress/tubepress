<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_internal_collection_Map<extended>
 */
class tubepress_test_internal_collection_MapTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_internal_collection_Map
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut  = new tubepress_internal_collection_Map();
    }

    public function testHas()
    {
        $this->assertFalse($this->_sut->containsKey('foo'));

        $this->_sut->put('foo', 'bar');

        $this->assertTrue($this->_sut->containsKey('foo'));
    }

    public function testGetAsBoolean()
    {
        $this->_sut->put('foo', '0');
        $this->assertTrue($this->_sut->getAsBoolean('foo') === false);
    }

    public function testGetAll()
    {
        $this->_sut->put('foo', 33);
        $this->_sut->put('bar', 'hi');
        $this->assertEquals(array('foo', 'bar'), $this->_sut->keySet());
    }

    public function testGet()
    {
        $this->_sut->put('foo', 33);
        $this->assertEquals(33, $this->_sut->get('foo'));
    }

    public function testGetNoExist()
    {
        $this->setExpectedException('InvalidArgumentException', 'No such key: foo');

        $this->_sut->get('foo');
    }
}