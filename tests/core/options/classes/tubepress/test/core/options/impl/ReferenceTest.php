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
 * @covers tubepress_core_options_impl_Reference<extended>
 */
class tubepress_test_core_options_impl_ReferenceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_options_impl_Reference
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_sut = new tubepress_core_options_impl_Reference(

            array('foo' => 'bar', 'fuzz' => true, 'nopersist' => null, 'noshortcode' => 4, 'pro' => 'hi'),
            array('foo' => 'foo label'),
            array('fuzz' => 'fuzz desc'),
            array('nopersist'),
            array('noshortcode'),
            array('pro'),
            $this->_mockEventDispatcher
        );
    }

    public function testBasics()
    {
        $this->assertTrue($this->_sut->optionExists('foo'));
        $this->assertFalse($this->_sut->optionExists('food'));

        $this->assertEquals(array('foo', 'fuzz', 'nopersist', 'noshortcode', 'pro'), $this->_sut->getAllOptionNames());

        $this->_setupEventDispatcher('foo', 'foo label', tubepress_core_options_api_Constants::EVENT_OPTION_GET_LABEL);
        $this->assertEquals('foo label', $this->_sut->getUntranslatedLabel('foo'));

        $this->_setupEventDispatcher('fuzz', 'fuzz desc', tubepress_core_options_api_Constants::EVENT_OPTION_GET_DESCRIPTION);
        $this->assertEquals('fuzz desc', $this->_sut->getUntranslatedDescription('fuzz'));

        $this->assertTrue($this->_sut->isProOnly('pro'));
        $this->assertFalse($this->_sut->isProOnly('foo'));
        $this->assertFalse($this->_sut->isProOnly('nope'));

        $this->assertTrue($this->_sut->isMeantToBePersisted('foo'));
        $this->assertFalse($this->_sut->isMeantToBePersisted('nopersist'));
        $this->assertTrue($this->_sut->isMeantToBePersisted('nope'));

        $this->_setupEventDispatcher('pro', 'hi', tubepress_core_options_api_Constants::EVENT_OPTION_GET_DEFAULT_VALUE);
        $this->assertEquals('hi', $this->_sut->getDefaultValue('pro'));

        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode('pro'));
        $this->assertFalse($this->_sut->isAbleToBeSetViaShortcode('noshortcode'));
        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode('nope'));

        $this->assertTrue($this->_sut->isBoolean('fuzz'));
        $this->assertFalse($this->_sut->isBoolean('noshortcode'));
        $this->assertFalse($this->_sut->isBoolean('nope'));
    }

    private function _setupEventDispatcher($optionName, $subject, $eventName)
    {
        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($subject, array('optionName' => $optionName))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with("$eventName.$optionName", $mockEvent);

        $mockEvent->shouldReceive('getSubject')->once()->andReturn($subject);
    }
}