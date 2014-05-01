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
 * @covers tubepress_addons_coreapiservices_impl_options_Persistence<extended>
 */
class tubepress_test_addons_coreapiservices_options_PersistenceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_coreapiservices_impl_options_Persistence
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBackend;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockOptionProvider  = $this->createMockSingletonService(tubepress_spi_options_OptionProvider::_);
        $this->_mockBackend         = ehough_mockery_Mockery::mock(tubepress_api_options_PersistenceBackendInterface::_);

        $this->_sut = new tubepress_addons_coreapiservices_impl_options_Persistence($this->_mockEventDispatcher, $this->_mockBackend);
    }

    public function testFetchNoSuchOption()
    {
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('name'));
        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->once()->andReturn(array('name' => 'value2'));

        $this->assertNull($this->_sut->fetch('no such'));
    }

    public function testQueueInValid()
    {
        $this->_setupFilters('name', 'value');
        $this->_setupValidationServiceToFail('name', 'value');

        $result = $this->_sut->queueForSave('name', 'value');

        $this->assertEquals('value was a bad value', $result);
    }

    public function testQueueValidNonBooleanYesChange()
    {
        $this->_setupFilters('name', 'value');
        $this->_setupValidationServiceToPass('name', 'value');
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('name')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('name')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('name'));
        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->twice()->andReturn(array('name' => 'value2'), array('name' => 'value'));
        $this->_mockBackend->shouldReceive('saveAll')->once()->with(array('name' => 'value'));

        $result = $this->_sut->queueForSave('name', 'value');

        $this->assertNull($result);

        $result = $this->_sut->flushSaveQueue();
    }

    public function testQueueValidNonBooleanNoChange()
    {
        $this->_setupFilters('name', 'value');
        $this->_setupValidationServiceToPass('name', 'value');
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('name')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('name')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('name'));
        $this->_mockBackend->shouldReceive('fetchAllCurrentlyKnownOptionNamesToValues')->once()->andReturn(array('name' => 'value'));

        $result = $this->_sut->queueForSave('name', 'value');

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

    private function _setupValidationServiceToFail($name, $value)
    {
        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with($name, $value)->andReturn(false);

        $this->_mockOptionProvider->shouldReceive('getProblemMessage')->once()->with($name, $value)->andReturnUsing(function ($n, $v) {

            return "$v was a bad value";
        });
    }

    private function _setupValidationServiceToPass($name, $value)
    {
        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with($name, $value)->andReturn(true);
    }

    private function _setupFilters($name, $value)
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . ".$name", ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
    }
}

