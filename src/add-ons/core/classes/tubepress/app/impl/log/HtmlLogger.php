<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_app_impl_log_HtmlLogger implements tubepress_platform_api_log_LoggerInterface
{
    /**
     * @var boolean
     */
    private $_enabled;

    /**
     * @var string[]
     */
    private $_bootMessageBuffer;

    /**
     * @var bool
     */
    private $_shouldBuffer;

    /**
     * @var string
     */
    private $_timezone;

    public function __construct(tubepress_app_api_options_ContextInterface        $context,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams)
    {
        $loggingRequested         = $requestParams->hasParam('tubepress_debug') && $requestParams->getParamValue('tubepress_debug') === true;
        $loggingEnabled           = $context->get(tubepress_app_api_options_Names::DEBUG_ON);
        $this->_enabled           = $loggingRequested && $loggingEnabled;
        $this->_bootMessageBuffer = array();
        $this->_shouldBuffer      = true;

        $this->_timezone = new DateTimeZone(@date_default_timezone_get() ? @date_default_timezone_get() : 'UTC');
    }

    public function onBootComplete()
    {
        if (!$this->_enabled) {

            unset($this->_bootMessageBuffer);
            return;
        }

        $this->_shouldBuffer = false;

        foreach ($this->_bootMessageBuffer as $message) {

            echo $message;
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
        $this->_write($message, $context, false);
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
        $this->_write($message, $context, true);
    }

    private function _write($message, array $context, $error)
    {
        if (!$this->_enabled) {

            return;
        }

        $finalMessage = $this->_getFormattedMessage($message, $context, $error);

        if ($this->_shouldBuffer) {

            $this->_bootMessageBuffer[] = $finalMessage;

        } else {

            echo $finalMessage;
        }
    }

    private function _getFormattedMessage($message, array $context, $error)
    {
        $dateTime      = $this->_createDateTimeFromFormat();
        $formattedTime = $dateTime->format('i:s.u');
        $level         = $error ? 'ERROR' : 'INFO';
        $color         = $error ? 'red' : 'inherit';

        if (!empty($context)) {

            $message .= ' ' . json_encode($context);
        }

        return "<span style=\"color: $color\">[$formattedTime - $level] $message</span><br />\n";
    }

    /**
     * This is here for testing purposes.
     */
    public function ___write($message, array $context, $error)
    {
        $this->_write($message, $context, $error);
    }

    /**
     * @return DateTime
     */
    private function _createDateTimeFromFormat()
    {
        if (version_compare(PHP_VERSION, '5.3') >= 0) {

            return DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), $this->_timezone)->setTimezone($this->_timezone);
        }

        $time = new DateTime('@' . time());
        $time->setTimezone($this->_timezone);

        return $time;
    }
}