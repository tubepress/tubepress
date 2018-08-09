<?php
/**
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_internal_boot_helper_FatalErrorHandler<extended>
 */
class tubepress_test_internal_boot_helper_FatalErrorHandlerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var tubepress_internal_boot_helper_FatalErrorHandler
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockLogger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_sut        = new tubepress_internal_boot_helper_FatalErrorHandler();
    }

    /**
     * @dataProvider getDataFatalErrors
     */
    public function testFatalErrors($type, $shouldRun)
    {
        $error = array(
            'line'    => 33,
            'file'    => 'some-file.php',
            'message' => 'hi < there!',
            'type'    => $type,
        );

        if ($shouldRun) {

            $this->_mockLogger->shouldReceive('error')->once()->with(sprintf(
                'Fatal error (type <code>%s</code>) detected on line <code>33</code> of <code>some-file.php</code>: <code>hi &lt; there!</code>',
                $type
            ));
        }

        $this->_sut->onFatalError($this->_mockLogger, $error);
    }

    public function getDataFatalErrors()
    {
        return array(
            array(E_ERROR, true),
            array(E_WARNING, false),
            array(E_PARSE, true),
            array(E_NOTICE, false),
            array(E_CORE_ERROR, true),
            array(E_CORE_WARNING, false),
            array(E_COMPILE_ERROR, true),
            array(E_COMPILE_WARNING, false),
            array(E_USER_ERROR, false),
            array(E_USER_WARNING, false),
            array(E_USER_NOTICE, false),
            array(E_STRICT, false),
            array(E_RECOVERABLE_ERROR, false),
            array(E_DEPRECATED, false),
            array(E_USER_DEPRECATED, false),
        );
    }
}