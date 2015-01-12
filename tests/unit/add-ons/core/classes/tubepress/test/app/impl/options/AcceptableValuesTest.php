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
 * @covers tubepress_app_impl_options_AcceptableValues<extended>
 */
class tubepress_test_app_impl_options_AcceptableValuesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_options_AcceptableValues
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $this->_sut = new tubepress_app_impl_options_AcceptableValues($this->_mockEventDispatcher);
    }

    public function testGetAcceptableValues()
    {
        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('foo'));
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(null, array('optionName' => 'name'))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.name',
            $mockEvent
        );
        $result = $this->_sut->getAcceptableValues('name');

        $this->assertEquals(array('foo'), $result);
    }
}