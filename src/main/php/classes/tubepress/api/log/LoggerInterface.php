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
 * Logger used in debugging.
 *
 * @package TubePress\Log
 */
interface tubepress_api_log_LoggerInterface
{
    const _ = 'tubepress_api_log_LoggerInterface';

    /**
     * @return bool True if debugging is active, false otherwise.
     */
    function isEnabled();

    /**
     * Log a normal message. Users *should* call isEnabled() before calling this
     * function to avoid unnecessary overhead.
     *
     * @param string $message The message to log.
     * @param array  $context Optional context variables.
     *
     * @return void
     */
    function debug($message, array $context = array());

    /**
     * Log a message. Users *should* call isEnabled() before calling this
     * function to avoid unnecessary overhead.
     *
     * @param string $message The message to log.
     * @param array  $context Optional context variables.
     *
     * @return void
     */
    function error($message, array $context = array());
}