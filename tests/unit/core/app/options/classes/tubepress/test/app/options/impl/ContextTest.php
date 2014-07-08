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
 * @covers tubepress_app_options_impl_Context<extended>
 */
class tubepress_test_app_options_impl_ContextTest extends tubepress_test_app_options_impl_internal_AbstractOptionReaderTest
{
    /**
     * @var tubepress_app_options_impl_Context
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockReference;

    protected function doSetup()
    {
        $this->_mockStorageManager  = $this->mock(tubepress_app_options_api_PersistenceInterface::_);
        $this->_mockReference      = $this->mock(tubepress_app_options_api_ReferenceInterface::_);

        $this->_sut = new tubepress_app_options_impl_Context(

            $this->_mockStorageManager,
            $this->getMockEventDispatcher(),
            $this->_mockReference
        );
    }

    public function testGetFromPersistence()
    {
        $this->_mockStorageManager->shouldReceive('fetch')->once()->with('a')->andReturn('b');

        $result = $this->_sut->get('a');

        $this->assertEquals('b', $result);
    }

    public function testSetGet()
    {
        $this->assertEquals(array(), $this->_sut->getEphemeralOptions());

        $this->setupEventDispatcherToPass('theme', 'crazytheme', 'CRAZYTHEME');

        $this->_sut->setEphemeralOption('theme', 'crazytheme');

        $this->assertEquals('CRAZYTHEME', $this->_sut->get('theme'));

        $this->assertEquals(array('theme' => 'CRAZYTHEME'), $this->_sut->getEphemeralOptions());

        $this->setupEventDispatcherToPass('foo', 'bar', 'BAR');

        $this->_sut->setEphemeralOptions(array('foo' => 'bar'));

        $this->assertEquals(array('foo' => 'BAR'), $this->_sut->getEphemeralOptions());
    }

    public function testSetSingleBadValue()
    {
        $this->setupEventDispatcherToFail('theme', 'hi', 'HI', 'something bad');

        $result = $this->_sut->setEphemeralOption('theme', 'hi');

        $this->assertEquals('something bad', $result);
    }

    public function testSetMultipleBadValues()
    {
        $this->setupEventDispatcherToFail('theme', 'hi', 'HI', 'something bad');

        $result = $this->_sut->setEphemeralOptions(array('theme' => 'hi'));

        $this->assertEquals(array('something bad'), $result);
    }


}

