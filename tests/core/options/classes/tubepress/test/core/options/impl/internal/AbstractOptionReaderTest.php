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

abstract class tubepress_test_core_options_impl_internal_AbstractOptionReaderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_options_impl_Context
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public final function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);

        $this->doSetup();
    }

    protected abstract function doSetup();

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
        $mockFirstEvent  = $this->mock('tubepress_core_event_api_EventInterface');
        $mockSecondEvent = $this->mock('tubepress_core_event_api_EventInterface');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array(), array(
            'optionName'  => $optionName,
            'optionValue' => $incomingValue
        ))->andReturn($mockFirstEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_core_options_api_Constants::EVENT_OPTION_SET, $mockFirstEvent
        );
        $mockFirstEvent->shouldReceive('getSubject')->once()->andReturn($errors);
        $mockFirstEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('abc');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($errors, array(

            'optionName' => $optionName,
            'optionValue' => 'abc'
        ))->andReturn($mockSecondEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_core_options_api_Constants::EVENT_OPTION_SET . '.' . $optionName,
            $mockSecondEvent
        );
        $mockSecondEvent->shouldReceive('getSubject')->once()->andReturn($errors);
        $mockSecondEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($finalValue);
    }
}

