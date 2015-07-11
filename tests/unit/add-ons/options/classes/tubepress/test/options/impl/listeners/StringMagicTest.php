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
 * @covers tubepress_options_impl_listeners_StringMagicListener
 */
class tubepress_test_options_impl_listeners_StringMagicListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_options_impl_listeners_StringMagicListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock('tubepress_lib_api_event_EventDispatcherInterface');

        $this->_sut = new tubepress_options_impl_listeners_StringMagicListener(

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

        $event->shouldReceive('setArgument')->once()->with('optionValue', 5);

        $this->_sut->onSet($event);

        $this->assertTrue(true);
    }

    public function testDeepArray()
    {
        $mockTopEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockTopEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn(array(
            'deep' => array(
                'deeper' => array('true')
            )
        ));
        $mockTopEvent->shouldReceive('setArgument')->once()->with('optionValue', array(
            'deep' => array(
                'deeper' => array(true)
            )
        ));

        $this->_sut->onSet($mockTopEvent);

        $this->assertTrue(true);
    }

    private function _booleanConversion($expected, $val)
    {
        $event = $this->_buildEvent($val, $expected);

        $event->shouldReceive('setArgument')->once()->with('optionValue', $expected);

        $this->_sut->onSet($event);

        $this->assertTrue(true);
    }

    private function _buildEvent($incomingValue, $expectedFinalValue)
    {
        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        //$mockEvent->shouldReceive('getSubject')->once()->andReturn(array());
        //$mockEvent->shouldReceive('getArgument')->once()->with('optionName')->andReturn('name');
        $mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incomingValue);

        return $mockEvent;
    }
}