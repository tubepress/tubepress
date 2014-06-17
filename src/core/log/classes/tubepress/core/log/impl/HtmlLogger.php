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
 *
 */
class tubepress_core_log_impl_HtmlLogger extends ehough_epilog_handler_AbstractProcessingHandler implements tubepress_api_log_LoggerInterface
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_delegate;

    /**
     * @var boolean
     */
    private $_enabled;

    /**
     * @var string[]
     */
    private $_debugBuffer;

    /**
     * @var string[]
     */
    private $_errorBuffer;

    /**
     * @var bool
     */
    private $_shouldBuffer;

    public function __construct(tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_http_api_RequestParametersInterface $requestParams,
                                ehough_epilog_Logger                               $delegate,
                                ehough_epilog_formatter_FormatterInterface         $formatter) {

        parent::__construct();

        $loggingRequested    = $requestParams->hasParam('tubepress_debug') && $requestParams->getParamValue('tubepress_debug') === true;
        $loggingEnabled      = $context->get(tubepress_core_log_api_Constants::OPTION_DEBUG_ON);
        $this->_enabled      = $loggingRequested && $loggingEnabled;
        $this->_debugBuffer  = array();
        $this->_errorBuffer  = array();
        $this->_shouldBuffer = true;

        if ($this->_enabled) {

            $this->_delegate = $delegate;
            $this->_delegate->pushHandler($this);

            $this->setFormatter($formatter);
            $this->setLevel(ehough_epilog_Logger::DEBUG);
        }
    }

    public function onBootComplete()
    {
        if (!$this->_enabled) {

            unset($this->_errorBuffer);
            unset($this->_debugBuffer);
            return;
        }

        $this->_shouldBuffer = false;

        foreach ($this->_debugBuffer as $debugMessage => $debugContext) {

            $this->debug($debugMessage, $debugContext);
        }

        foreach ($this->_errorBuffer as $errorMessage => $errorContext) {

            $this->error($errorMessage, $errorContext);
        }
    }

    /**
     * @return bool True if debugging is active, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Log a normal message. Users *should* call isEnabled() before calling this
     * function to avoid unnecessary overhead.
     *
     * @param string $message The message to log.
     * @param array  $context Optional context variables.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function debug($message, array $context = array())
    {
        if ($this->_enabled) {

            if ($this->_shouldBuffer) {

                $this->_debugBuffer[$message] = $context;

            } else {

                $this->_delegate->debug($message, $context);
            }
        }
    }

    /**
     * Log a message. Users *should* call isEnabled() before calling this
     * function to avoid unnecessary overhead.
     *
     * @param string $message The message to log.
     * @param array  $context Optional context variables.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function error($message, array $context = array())
    {
        if ($this->_enabled) {

            if ($this->_shouldBuffer) {

                $this->_errorBuffer[$message] = $context;

            } else {

                $this->_delegate->error($message, $context);
            }
        }
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
        if (!$this->_enabled) {

            return;
        }

        $color   = $record['level_name'] === 'DEBUG' ? 'inherit' : 'red';
        $toPrint = "<span style=\"color: $color\">" . $record['formatted'] . "</span><br />\n";

        echo $toPrint;
    }

    /**
     * This is here for testing purposes.
     *
     * @param array $record
     */
    public function ___write(array $record)
    {
        $this->write($record);
    }
}