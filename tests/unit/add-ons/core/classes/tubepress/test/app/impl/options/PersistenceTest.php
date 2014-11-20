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
 * @covers tubepress_app_impl_options_Persistence<extended>
 */
class tubepress_test_app_options_PersistenceTest extends tubepress_test_app_impl_options_AbstractOptionReaderTest
{
    /**
     * @var tubepress_app_impl_options_Persistence
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBackend;

    protected function doSetup()
    {
        $this->_mockOptionsReference = $this->mock(tubepress_app_api_options_ReferenceInterface::_);
        $this->_mockBackend          = $this->mock(tubepress_app_api_options_PersistenceBackendInterface::_);

        $this->_sut = new tubepress_app_impl_options_Persistence(
            $this->_mockOptionsReference,
            $this->getMockEventDispatcher(),
            $this->_mockBackend
        );
    }

    public function testFetchNoSuchOption()
    {
        $this->setExpectedException('InvalidArgumentException', 'No such option: no such');

        $this->_mockOptionsReference->shouldReceive('getAllOptionNames')->once()->andReturn(array('name'));

        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->once()->andReturn(array('name' => 'value2'));

        $this->assertNull($this->_sut->fetch('no such'));
    }

    public function testQueueValidNonBooleanYesChange()
    {
        $this->_mockOptionsReference->shouldReceive('isMeantToBePersisted')->once()->with('name')->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with('name')->andReturn(false);
        $this->_mockOptionsReference->shouldReceive('getAllOptionNames')->once()->andReturn(array('name'));

        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->twice()->andReturn(array('name' => 'value2'), array('name' => 'VALUE'));
        $this->_mockBackend->shouldReceive('saveAll')->once()->with(array('name' => 'VALUE'))->andReturnNull();

        $this->setupEventDispatcherToPass('name', 'value', 'VALUE');

        $result = $this->_sut->queueForSave('name', 'value');

        $this->assertNull($result);

        $result = $this->_sut->flushSaveQueue();

        $this->assertNull($result);

        $this->assertEquals('VALUE', $this->_sut->fetch('name'));
    }

    public function testQueueValidNonBooleanNoChange()
    {
        $this->_mockOptionsReference->shouldReceive('isMeantToBePersisted')->once()->with('name')->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with('name')->andReturn(false);
        $this->_mockOptionsReference->shouldReceive('getAllOptionNames')->once()->andReturn(array('name'));

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

        $this->_mockOptionsReference->shouldReceive('getAllOptionNames')->once()->andReturn(array('foo', 'bla'));
        $this->_mockOptionsReference->shouldReceive('isMeantToBePersisted')->once()->with('bla')->andReturn(false);

        $result = $this->_sut->fetchAll();

        $this->assertEquals($arr, $result);
    }

    public function testFetchAllMissingYesPersist()
    {
        $arr = array('foo' => 'bar');
        $final = array('foo' => 'bar', 'bla' => array(1,2,3));

        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->twice()->andReturn($arr, $final);
        $this->_mockBackend->shouldReceive('createEach')->once()->with(array('bla' => array(1,2,3)))->andReturnNull();

        $this->_mockOptionsReference->shouldReceive('isMeantToBePersisted')->once()->with('bla')->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('getDefaultValue')->once()->with('bla')->andReturn(array(1,2,3));

        $this->_mockOptionsReference->shouldReceive('getAllOptionNames')->twice()->andReturn(array('foo', 'bla'));

        $result = $this->_sut->fetchAll();

        $this->assertEquals($final, $result);
    }

    public function testFetchAllNoneMissing()
    {
        $arr = array('foo' => 'bar');

        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->once()->andReturn($arr);

        $this->_mockOptionsReference->shouldReceive('getAllOptionNames')->once()->andReturn(array('foo'));

        $result = $this->_sut->fetchAll();

        $this->assertEquals($arr, $result);
    }
}