<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_options_impl_listeners_LoggingListener
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_api_log_LoggerInterface       $logger,
                                tubepress_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_logger      = $logger;
        $this->_stringUtils = $stringUtils;
    }

    public function onOptionSet(tubepress_api_event_EventInterface $event)
    {
        if (!$this->_logger->isEnabled()) {

            return;
        }

        $errors      = $event->getSubject();
        $optionName  = $event->getArgument('optionName');
        $optionValue = $event->getArgument('optionValue');

        if (count($errors) === 0) {

            $this->_logger->debug(sprintf('(Option Logger) Accepted valid value: <code>%s</code> = <code>%s</code>',
                $optionName, $this->_stringUtils->redactSecrets($optionValue)));

        } else {

            $this->_logger->error(sprintf('(Option Logger) Rejecting invalid value: <code>%s</code> = <code>%s</code> (%s)',
                $optionName, $this->_stringUtils->redactSecrets($optionValue),
                $errors[0]));
        }
    }
}
