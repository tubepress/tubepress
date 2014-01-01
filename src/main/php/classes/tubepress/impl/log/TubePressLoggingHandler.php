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
 * epilog logging handler for TubePress.
 */
class tubepress_impl_log_TubePressLoggingHandler extends ehough_epilog_handler_AbstractProcessingHandler
{
    /**
     * @var bool
     */
    private $_isAllowedToPrint = false;

    /**
     * @var bool
     */
    private $_shouldThrowAwayRecords = false;

    /**
     * @var array Buffer of messages.
     */
    private $_messageBuffer = array();

    public function __construct()
    {
        parent::__construct();

        $this->setFormatter(new ehough_epilog_formatter_LineFormatter("[%datetime%] [%level_name%] %channel%: %message% <br />\n"));
        $this->setLevel(ehough_epilog_Logger::DEBUG);
    }

    /**
     * Write the record down to the log of the implementing handler.
     *
     * @param array $record The log record to write.
     *
     * @return void
     */
    protected function write(array $record)
    {
        if ($this->_shouldThrowAwayRecords) {

            return;
        }

        if ($this->_isAllowedToPrint) {

            echo $record['formatted'];

        } else {

            array_push($this->_messageBuffer, $record['formatted']);
        }
    }

    /**
     * Permanently enable or disable this logger.
     *
     * @param bool $status True or false indicating on or off.
     *
     * @throws RuntimeException If this function is called more than once.
     *
     * @return void
     */
    public final function setStatus($status)
    {
        if ($status) {

            $this->_isAllowedToPrint = true;

            //flush the message buffer
            foreach ($this->_messageBuffer as $message) {

                echo $message;
            }

        } else {

            $this->_shouldThrowAwayRecords = true;

            $this->setLevel(ehough_epilog_Logger::CRITICAL);
        }

        //clear up some memory
        $this->_messageBuffer = array();
    }
}
