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
 * @covers tubepress_core_impl_listeners_StringMagicFilter
 */
class tubepress_test_core_impl_listeners_StringMagicFilterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_StringMagicFilter
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock('tubepress_core_api_event_EventDispatcherInterface');
        $this->_mockStringUtils     = $this->mock(tubepress_api_util_StringUtilsInterface::_);

        $this->_sut = new tubepress_core_impl_listeners_StringMagicFilter(

            $this->_mockStringUtils,
            $this->_mockEventDispatcher
        );
    }

    public function testBooleanVariations()
    {
        $this->_booleanConversion(true, 'true');
        $this->_booleanConversion(true, 'TRUE');
        $this->_booleanConversion(true, ' TRuE  ');

        $this->_booleanConversion(false, 'false  ');
        $this->_booleanConversion(false, 'FALSE');
        $this->_booleanConversion(false, ' faLSe  ');
    }

    public function testInt()
    {
        $event  = $this->_buildEvent(5, 5);

        $this->_sut->magic($event);

        $this->assertTrue(true);
    }

    public function testDeepArray()
    {
        $val = array('name' => 'a');

        $mockTopEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockTopEvent->shouldReceive('getSubject')->once()->andReturn($val);
        $mockTopEvent->shouldReceive('setSubject')->once()->with(array('name' => 'y'));

        $mockEvent2 = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent2->shouldReceive('getSubject')->twice()->andReturn('a', 'y');
        $mockEvent2->shouldReceive('setArgument')->once()->with('optionName', 'name');
        $mockEvent2->shouldReceive('setSubject')->once()->with('z');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('a')->andReturn($mockEvent2);

        $this->_mockStringUtils->shouldReceive('stripslashes_deep')->once()->with('a')->andReturn('z');

        $this->_sut->magic($mockTopEvent);

        $this->assertTrue(true);
    }

    private function _booleanConversion($expected, $val)
    {
        $event = $this->_buildEvent($val, $expected);

        $this->_mockStringUtils->shouldReceive('stripslashes_deep')->once()->with(trim($val))->andReturnUsing(function ($x) {

            $mockUtils = new tubepress_impl_util_StringUtils();
            return $mockUtils->stripslashes_deep($x);
        });

        $event->shouldReceive('setSubject')->once()->with($expected);

        $this->_sut->magic($event);

        $this->assertTrue(true);
    }

    private function _buildEvent($incomingValue, $expectedFinalValue)
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($incomingValue);

        return $mockEvent;
    }
}