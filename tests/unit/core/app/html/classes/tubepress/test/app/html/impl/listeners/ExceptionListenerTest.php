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
 * @covers tubepress_app_html_impl_listeners_ExceptionListener
 */
class tubepress_test_app_feature_impl_listeners_ExceptionListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var tubepress_app_html_impl_listeners_ExceptionListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockLogger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockEvent  = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_sut        = new tubepress_app_html_impl_listeners_ExceptionListener(
            $this->_mockLogger
        );
    }

    public function testExceptionLogEnabled()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(new RuntimeException());
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('error')->atLeast(1);
        $this->_sut->onException($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testExceptionLogDisabled()
    {
        //$this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(new RuntimeException());
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(false);
        $this->_sut->onException($this->_mockEvent);
        $this->assertTrue(true);
    }
}