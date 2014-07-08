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
 * @covers tubepress_lib_event_impl_tickertape_EventDispatcher
 */
class tubepress_test_lib_event_impl_tickertape_EventDispatcherTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_lib_event_impl_tickertape_EventDispatcher
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockDispatcher;

    public function onSetup()
    {
        $this->_mockDispatcher = $this->mock('ehough_tickertape_ContainerAwareEventDispatcher');

        $this->_sut = new tubepress_lib_event_impl_tickertape_EventDispatcher($this->_mockDispatcher);
    }

    public function testAddListener()
    {
        $this->_mockDispatcher->shouldReceive('addListener')->once()->with('some event', array('x', 'y', 'z'), 99);

        $this->_sut->addListener('some event', array('x', 'y', 'z'), 99);

        $this->assertTrue(true);
    }

    public function testAddListenerService()
    {
        $this->_mockDispatcher->shouldReceive('addListenerService')->once()->with('some event', array('x', 'y', 'z'), 99);

        $this->_sut->addListenerService('some event', array('x', 'y', 'z'), 99);

        $this->assertTrue(true);
    }

    public function testGetListeners()
    {
        $this->_mockDispatcher->shouldReceive('getListeners')->once()->with('some event')->andReturn(array('z'));

        $this->assertEquals(array('z'), $this->_sut->getListeners('some event'));
    }

    public function testHasListeners()
    {
        $this->_mockDispatcher->shouldReceive('hasListeners')->once()->with('some event')->andReturn(true);

        $this->assertTrue($this->_sut->hasListeners('some event'));
    }

    public function testRemoveListener()
    {
        $this->_mockDispatcher->shouldReceive('removeListener')->once()->with('some event', array('x', 'y', 'z'));

        $this->_sut->removeListener('some event', array('x', 'y', 'z'));

        $this->assertTrue(true);
    }

    public function testDispatchNonTickertapeEvent()
    {
        $event = $this->mock('tubepress_lib_event_api_EventInterface');

        $this->_mockDispatcher->shouldReceive('dispatch')->once()->with('some event', ehough_mockery_Mockery::on(function ($event) {

            return $event instanceof tubepress_lib_event_impl_tickertape_TickertapeEventWrapper;

        }))->andReturn(array('x'));

        $result = $this->_sut->dispatch('some event', $event);

        $this->assertEquals(array('x'), $result);
    }

    public function testDispatchTickertapeEvent()
    {
        $event = new tubepress_lib_event_impl_tickertape_EventBase();

        $this->_mockDispatcher->shouldReceive('dispatch')->once()->with('some event', ehough_mockery_Mockery::on(function ($event) {

            return $event instanceof tubepress_lib_event_impl_tickertape_EventBase;
        }))->andReturn(array('x'));

        $result = $this->_sut->dispatch('some event', $event);

        $this->assertEquals(array('x'), $result);
    }

    public function testNewEventInstance()
    {
        $result = $this->_sut->newEventInstance('foo', array('bar' => 'fuzz'));

        $this->assertInstanceOf('tubepress_lib_event_impl_tickertape_EventBase', $result);

        $this->assertEquals('foo', $result->getSubject());
        $this->assertEquals(array('bar' => 'fuzz'), $result->getArguments());

    }
}
