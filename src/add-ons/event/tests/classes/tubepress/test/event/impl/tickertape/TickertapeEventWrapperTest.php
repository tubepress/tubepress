<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_event_impl_tickertape_TickertapeEventWrapper
 */
class tubepress_test_event_impl_tickertape_TickertapeEventWrapperTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_event_impl_tickertape_TickertapeEventWrapper
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_sut = new tubepress_event_impl_tickertape_TickertapeEventWrapper($this->_mockEvent);
    }

    public function testPropagation()
    {
        $this->_mockEvent->shouldReceive('isPropagationStopped')->twice()->andReturn(false, true);
        $this->_mockEvent->shouldReceive('stopPropagation')->once();

        $this->assertFalse($this->_sut->isPropagationStopped());

        $this->_sut->stopPropagation();

        $this->assertTrue($this->_sut->isPropagationStopped());
    }

    public function testGetDelegate()
    {
        $this->_sut = new tubepress_event_impl_tickertape_TickertapeEventWrapper();

        $disp = $this->_sut->getDispatcher();

        $this->assertNull($disp);
    }

    public function testSetGetName()
    {
        $this->_mockEvent->shouldReceive('setName')->once()->with('xyz');
        $this->_mockEvent->shouldReceive('getName')->once()->andReturn('xyz');

        $this->_sut->setName('xyz');
        $this->assertEquals('xyz', $this->_sut->getName());
    }

    public function testSetSubject()
    {
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array('y'));

        $this->_sut->setSubject(array('y'));

        $this->assertTrue(true);
    }

    public function testSetArguments()
    {
        $this->_mockEvent->shouldReceive('setArguments')->once()->with(array('y' => 'z'));

        $this->_sut->setArguments(array('y' => 'z'));

        $this->assertTrue(true);
    }

    public function testSetArgument()
    {
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('foo', 'bar');

        $this->_sut->setArgument('foo', 'bar');

        $this->assertTrue(true);
    }

    public function testHasArgument()
    {
        $this->_mockEvent->shouldReceive('hasArgument')->once()->with('foo')->andReturn(true);

        $this->assertTrue($this->_sut->hasArgument('foo'));
    }

    public function testGetSubject()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('xyz');

        $this->assertEquals('xyz', $this->_sut->getSubject());
    }

    public function testGetArgument()
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('foo')->andReturn('xyz');

        $this->assertEquals('xyz', $this->_sut->getArgument('foo'));
    }

    public function testGetArguments()
    {
        $this->_mockEvent->shouldReceive('getArguments')->once()->andReturn('xyz');

        $this->assertEquals('xyz', $this->_sut->getArguments());
    }
}
