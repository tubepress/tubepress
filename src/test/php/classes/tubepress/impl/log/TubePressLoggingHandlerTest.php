<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_log_TubePressLoggingHandlerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_log_TubePressLoggingHandler
     */
    private $_sut;

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_log_TubePressLoggingHandler();

        $logger = new ehough_epilog_Logger('test');
        $logger->pushHandler($this->_sut);

        $this->_logger = $logger;
    }

    public function testLogNotEnabled()
    {
        $this->_logger->critical('hey!!!');
        $this->_logger->warn('yoo!!');

        $this->assertTrue(true);
    }

    public function testLogEnabled2()
    {
        $this->expectOutputRegex('/^\[20[0-9]{2}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\] \[CRITICAL\] test: hello!!! <br \/>\\n\[20[0-9]{2}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\] \[CRITICAL\] test: hey!!! <br \/>$/');

        $this->_logger->critical('hello!!!');

        $this->_sut->setStatus(true);

        $this->_logger->critical('hey!!!');

        $this->assertTrue(true);
    }

    public function testLogEnabled1()
    {
        $this->expectOutputRegex('/^\[20[0-9]{2}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\] \[CRITICAL\] test: hey!!! <br \/>$/');

        $this->_sut->setStatus(true);

        $this->_logger->critical('hey!!!');

        $this->assertTrue(true);
    }
}

