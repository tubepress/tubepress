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
 * @covers tubepress_impl_log_HtmlLogger
 */
class tubepress_test_impl_log_HtmlLoggerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_log_HtmlLogger
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFormatter;

    public function onSetup()
    {
        $this->_mockLogger = ehough_mockery_Mockery::mock('ehough_epilog_Logger');
        $this->_mockFormatter = ehough_mockery_Mockery::mock('ehough_epilog_formatter_FormatterInterface');

        $this->_mockLogger->shouldReceive('pushHandler')->once()->with(ehough_mockery_Mockery::type('tubepress_impl_log_HtmlLogger'));

        $this->_sut = new tubepress_impl_log_HtmlLogger(
            $this->_mockLogger,
            $this->_mockFormatter
        );
    }

    public function testEnabled()
    {
        $this->assertTrue($this->_sut->isEnabled());
    }

    public function testError()
    {
        $this->_mockLogger->shouldReceive('error')->once()->with('some message', array('foo' => 'bar'));
        $this->_sut->error('some message', array('foo' => 'bar'));
        $this->assertTrue(true);
    }

    public function testDebug()
    {
        $this->_mockLogger->shouldReceive('debug')->once()->with('some message', array('foo' => 'bar'));
        $this->_sut->debug('some message', array('foo' => 'bar'));
        $this->assertTrue(true);
    }


}

