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
class tubepress_core_impl_log_HtmlLogger extends ehough_epilog_handler_AbstractProcessingHandler implements tubepress_api_log_LoggerInterface
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_delegate;

    /**
     * @var boolean
     */
    private $_enabled;

    public function __construct(tubepress_impl_log_BootLogger               $bootLogger,
                                ehough_epilog_Logger                        $delegate,
                                ehough_epilog_formatter_FormatterInterface  $formatter,
                                tubepress_core_api_options_ContextInterface $context) {

        parent::__construct();

        $loggingRequested = $context->get(tubepress_core_api_const_options_Names::DEBUG_ON);
        $loggingEnabled   = $bootLogger->isEnabled();
        $this->_enabled   = $loggingRequested && $loggingEnabled;
        $this->_delegate  = $delegate;

        if ($this->_enabled) {

            $this->setFormatter($formatter);
            $this->setLevel(ehough_epilog_Logger::DEBUG);

            $this->_delegate->pushHandler($this);

            /**
             * Flush out any boot messages.
             */
            $bootLogger->flushTo($this);
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

            $this->_delegate->debug($message, $context);
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

            $this->_delegate->error($message, $context);
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