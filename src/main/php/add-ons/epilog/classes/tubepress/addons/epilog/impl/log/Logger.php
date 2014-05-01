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
class tubepress_addons_epilog_impl_log_Logger implements tubepress_api_log_LoggerInterface
{
    /**
     * @var ehough_epilog_psr_LoggerInterface
     */
    private $_delegate;

    /**
     * @var bool
     */
    private $_enabled;

    public function __construct(ehough_epilog_psr_LoggerInterface $delegate, tubepress_api_options_ContextInterface $context)
    {
        $this->_delegate = $delegate;
    }

    /**
     * @return bool True if debugging is active, false otherwise.
     */
    public function isDebugEnabled()
    {

    }

    /**
     * Log a message. Users *should* call isDebugEnabled() before calling this
     * function to avoid unnecessary overhead.
     *
     * @param string $message The message to log.
     * @param array  $context Optional context variables.
     *
     * @return void
     */
    public function log($message, array $context = array())
    {

    }
}