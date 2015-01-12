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
 * Logger that stores messages in memory. Used during boot.
 */
class tubepress_platform_impl_log_BootLogger implements tubepress_platform_api_log_LoggerInterface
{
    /**
     * @var bool
     */
    private $_isEnabled = false;

    /**
     * @var array Buffer of messages.
     */
    private $_buffer = array();

    public function __construct($enabled)
    {
        $this->_isEnabled = (bool) $enabled;
    }

    /**
     * @return bool True if debugging is active, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isEnabled()
    {
        return $this->_isEnabled;
    }

    /**
     * @return void
     */
    public function handleBootException(Exception $e)
    {
        $this->error('Caught exception while booting: '.  $e->getMessage());

        $traceData = $e->getTraceAsString();
        $traceData = explode("\n", $traceData);

        foreach ($traceData as $line) {
            $this->error("<tt>$line</tt>");
        }

        foreach ($this->_buffer as $message => $context) {

            $message = sprintf('%s [%s]', $message, print_r($context, true));
            echo "$message<br />\n";
        }
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
        $this->_store($message, $context, 'normal');
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
        $this->_store($message, $context, 'error');
    }

    public function flushTo(tubepress_platform_api_log_LoggerInterface $logger)
    {
        foreach ($this->_buffer as $message => $context) {

            $error = false;

            if (isset($context['__level'])) {

                $error = $context['__level'] === 'error';

                unset($context['__level']);
            }

            if ($error) {

                $logger->error($message, $context);

            } else {

                $logger->debug($message, $context);
            }
        }
    }

    private function _store($message, $context, $level)
    {
        if (!$this->_isEnabled) {

            return;
        }

        $message            = sprintf('%s %s', $this->_udate(), $message);
        $context['__level'] = $level;

        $this->_buffer[$message] = $context;
    }

    //http://www.php.net/manual/en/datetime.format.php#113607
    private function _udate()
    {
        $utimestamp   = microtime(true);
        $timestamp    = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, 'i:s.u'), $timestamp);
    }

    /**
     * @return void
     */
    public function onBootComplete()
    {
        $this->_isEnabled = false;
        unset($this->_buffer);
    }
}