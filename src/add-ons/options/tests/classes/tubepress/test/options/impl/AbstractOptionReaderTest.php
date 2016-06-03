<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

abstract class tubepress_test_options_impl_AbstractOptionReaderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    final public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_api_event_EventDispatcherInterface::_);

        $this->doSetup();
    }

    abstract protected function doSetup();

    protected function getMockEventDispatcher()
    {
        return $this->_mockEventDispatcher;
    }

    protected function setupEventDispatcherToFail($optionName, $incomingValue, $finalValue, $message)
    {
        $this->_setupEventDispatcher($optionName, $incomingValue, $finalValue, array($message));
    }

    protected function setupEventDispatcherToPass($optionName, $incomingValue, $finalValue)
    {
        $this->_setupEventDispatcher($optionName, $incomingValue, $finalValue, array());
    }

    private function _setupEventDispatcher($optionName, $incomingValue, $finalValue, array $errors)
    {
        $mockFirstEvent  = $this->mock('tubepress_api_event_EventInterface');
        $mockSecondEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockThirdEvent  = $this->mock('tubepress_api_event_EventInterface');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($incomingValue, array(
            'optionName' => $optionName,
        ))->andReturn($mockFirstEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_api_event_Events::NVP_FROM_EXTERNAL_INPUT, $mockFirstEvent
        );
        $mockFirstEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array(), array(

            'optionName'  => $optionName,
            'optionValue' => 'abc',
        ))->andReturn($mockSecondEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_api_event_Events::OPTION_SET . '.' . $optionName,
            $mockSecondEvent
        );
        $mockSecondEvent->shouldReceive('getSubject')->once()->andReturn($errors);
        $mockSecondEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('xyz');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($errors, array(

            'optionName'  => $optionName,
            'optionValue' => 'xyz',
        ))->andReturn($mockThirdEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_api_event_Events::OPTION_SET,
            $mockThirdEvent
        );
        $mockThirdEvent->shouldReceive('getSubject')->once()->andReturn($errors);
        $mockThirdEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($finalValue);
    }
}
