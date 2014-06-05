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
class tubepress_core_html_impl_listeners_ErrorLogger
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    public function __construct(tubepress_api_log_LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    public function onException(tubepress_core_event_api_EventInterface $event)
    {
        if (!$this->_logger->isEnabled()) {

            return;
        }

        /**
         * @var $exception Exception
         */
        $exception = $event->getSubject();
        $traceData = $exception->getTraceAsString();
        $traceData = explode("\n", $traceData);

        foreach ($traceData as $line) {

            $this->_logger->error("<tt>$line</tt><br />");
        }
    }
}