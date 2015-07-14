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
 * @covers tubepress_api_listeners_options_TrimmingListener<extended>
 */
class tubepress_test_api_listeners_options_TrimmingListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_api_listeners_options_TrimmingListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_sut = new tubepress_api_listeners_options_TrimmingListener('#');
    }

    public function testTrim()
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('####5####');

        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', '5');

        $this->_sut->onOption($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testLtrim()
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('####5####');

        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', '5####');

        $this->_sut->setModeToLtrim();

        $this->_sut->onOption($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testRtrim()
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('####5####');

        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', '####5');

        $this->_sut->setModeToRtrim();

        $this->_sut->onOption($this->_mockEvent);

        $this->assertTrue(true);
    }
}