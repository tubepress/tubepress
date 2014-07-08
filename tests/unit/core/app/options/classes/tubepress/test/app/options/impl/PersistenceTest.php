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
 * @covers tubepress_app_options_impl_Persistence<extended>
 */
class tubepress_test_app_options_impl_PersistenceTest extends tubepress_test_app_options_impl_internal_AbstractOptionReaderTest
{
    /**
     * @var tubepress_app_options_impl_Persistence
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBackend;

    public function doSetup()
    {
        $this->_mockOptionProvider  = $this->mock(tubepress_app_options_api_ReferenceInterface::_);
        $this->_mockBackend         = $this->mock(tubepress_app_options_api_PersistenceBackendInterface::_);

        $this->_sut = new tubepress_app_options_impl_Persistence(

            $this->_mockBackend,
            $this->_mockOptionProvider,
            $this->getMockEventDispatcher()
        );
    }

    public function testFetchNoSuchOption()
    {
        $this->setExpectedException('InvalidArgumentException', 'No such option: no such');

        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('name'));
        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->once()->andReturn(array('name' => 'value2'));

        $this->assertNull($this->_sut->fetch('no such'));
    }

    public function testQueueValidNonBooleanYesChange()
    {
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('name')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('name')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('name'));
        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->twice()->andReturn(array('name' => 'value2'), array('name' => 'value'));
        $this->_mockBackend->shouldReceive('saveAll')->once()->with(array('name' => 'VALUE'));

        $this->setupEventDispatcherToPass('name', 'value', 'VALUE');

        $result = $this->_sut->queueForSave('name', 'value');

        $this->assertNull($result);

        $result = $this->_sut->flushSaveQueue();

        $this->assertNull($result);
    }

    public function testQueueValidNonBooleanNoChange()
    {
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('name')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('name')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('name'));
        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->once()->andReturn(array('name' => 'VALUE'));

        $this->setupEventDispatcherToPass('name', 'value', 'VALUE');

        $result = $this->_sut->queueForSave('name', 'value');

        $this->assertNull($result);

        $result = $this->_sut->flushSaveQueue();

        $this->assertNull($result);

    }

    public function testFlushSaveQueueEmpty()
    {
        $this->assertNull($this->_sut->flushSaveQueue());
    }

    public function testFetchAllMissingNoPersist()
    {
        $arr = array('foo' => 'bar');
        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->once()->andReturn($arr);
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('foo', 'bla'));
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('bla')->andReturn(false);

        $result = $this->_sut->fetchAll();

        $this->assertEquals($arr, $result);
    }

    public function testFetchAllMissingYesPersist()
    {
        $arr = array('foo' => 'bar');
        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->twice()->andReturn($arr, array('foo' => 'bar', 'bla' => array(1,2,3)));
        $this->_mockBackend->shouldReceive('createEach')->once()->with(array('bla' => array(1,2,3)));
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->twice()->andReturn(array('foo', 'bla'));
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('bla')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('getDefaultValue')->once()->with('bla')->andReturn(array(1,2,3));

        $result = $this->_sut->fetchAll();

        $this->assertEquals(array('foo' => 'bar', 'bla' => array(1,2,3)), $result);
    }

    public function testFetchAllNoneMissing()
    {
        $arr = array('foo' => 'bar');
        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->once()->andReturn($arr);
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('foo'));

        $result = $this->_sut->fetchAll();

        $this->assertEquals($arr, $result);
    }
}

