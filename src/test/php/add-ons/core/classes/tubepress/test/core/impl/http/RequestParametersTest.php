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
 * @covers tubepress_core_impl_http_RequestParameters<extended>
 */
class tubepress_test_core_impl_http_RequestParametersTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_http_RequestParameters
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_core_api_event_EventDispatcherInterface::_);

        $this->_sut = new tubepress_core_impl_http_RequestParameters($this->_mockEventDispatcher);
    }

    public function testParamExists()
    {
        $_GET['something'] = 5;

        $this->assertTrue($this->_sut->hasParam('something') === true);
    }

    public function testParamNotExists()
    {
        $this->assertTrue($this->_sut->hasParam('something') === false);
    }

    public function testGetParamValueNoExist()
    {
        $this->assertTrue($this->_sut->getParamValue('something') === null);
    }

    public function testGetParam()
    {
        $_POST['something'] = array(1, 2, 3);

        $mockExternalEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockExternalEvent->shouldReceive('getSubject')->once()->andReturn('syz');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array(1, 2, 3), array('optionName' => 'something'))->andReturn($mockExternalEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT, $mockExternalEvent);

        $mockSingleEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockSingleEvent->shouldReceive('getSubject')->once()->andReturn('abc');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('syz')->andReturn($mockSingleEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT . '.something', $mockSingleEvent);


        $result = $this->_sut->getParamValue('something');

        $this->assertEquals('abc', $result);
    }

    public function testGetParamAsIntActuallyInt()
    {
        $_POST['something'] = array(1, 2, 3);

        $mockExternalEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockExternalEvent->shouldReceive('getSubject')->once()->andReturn(44);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array(1, 2, 3), array('optionName' => 'something'))->andReturn($mockExternalEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT, $mockExternalEvent);

        $mockSingleEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockSingleEvent->shouldReceive('getSubject')->once()->andReturn(444);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(44)->andReturn($mockSingleEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT . '.something', $mockSingleEvent);

        $result = $this->_sut->getParamValueAsInt('something', 1);

        $this->assertTrue($result === 444);
    }

    public function testGetParamAsIntNotActuallyInt()
    {
        $_GET['something'] = array(1, 2, 3);

        $mockExternalEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockExternalEvent->shouldReceive('getSubject')->once()->andReturn('44vb');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array(1, 2, 3), array('optionName' => 'something'))->andReturn($mockExternalEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT, $mockExternalEvent);

        $mockSingleEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockSingleEvent->shouldReceive('getSubject')->once()->andReturn('44vb777ert');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('44vb')->andReturn($mockSingleEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT . '.something', $mockSingleEvent);

        $result = $this->_sut->getParamValueAsInt('something', 33);

        $this->assertTrue($result === 33);
    }
}