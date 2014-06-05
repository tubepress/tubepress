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
 * @covers tubepress_core_options_impl_listeners_StringMagic
 */
class tubepress_test_core_options_impl_listeners_StringMagicTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_options_impl_listeners_StringMagic
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
        $this->_mockEventDispatcher = $this->mock('tubepress_core_event_api_EventDispatcherInterface');
        $this->_mockStringUtils     = $this->mock(tubepress_api_util_StringUtilsInterface::_);

        $this->_sut = new tubepress_core_options_impl_listeners_StringMagic(

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

        $event->shouldReceive('setArgument')->once()->with('optionValue', 5);

        $this->_sut->onSet($event);

        $this->assertTrue(true);
    }

    public function testDeepArray()
    {
        $mockTopEvent = $this->mock('tubepress_core_event_api_EventInterface');
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

        $this->_mockStringUtils->shouldReceive('stripslashes_deep')->once()->with('true')->andReturn('z');

        $this->_sut->onSet($mockTopEvent);

        $this->assertTrue(true);
    }

    private function _booleanConversion($expected, $val)
    {
        $event = $this->_buildEvent($val, $expected);

        $this->_mockStringUtils->shouldReceive('stripslashes_deep')->once()->with(trim($val))->andReturnUsing(function ($x) {

            $mockUtils = new tubepress_impl_util_StringUtils();
            return $mockUtils->stripslashes_deep($x);
        });

        $event->shouldReceive('setArgument')->once()->with('optionValue', $expected);

        $this->_sut->onSet($event);

        $this->assertTrue(true);
    }

    private function _buildEvent($incomingValue, $expectedFinalValue)
    {
        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        //$mockEvent->shouldReceive('getSubject')->once()->andReturn(array());
        //$mockEvent->shouldReceive('getArgument')->once()->with('optionName')->andReturn('name');
        $mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incomingValue);

        return $mockEvent;
    }
}