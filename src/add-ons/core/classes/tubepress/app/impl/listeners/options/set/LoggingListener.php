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
class tubepress_app_impl_listeners_options_set_LoggingListener
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_platform_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_platform_api_log_LoggerInterface       $logger,
                                tubepress_platform_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_logger      = $logger;
        $this->_stringUtils = $stringUtils;
    }

    public function onOptionSet(tubepress_lib_api_event_EventInterface $event)
    {
        if (!$this->_logger->isEnabled()) {

            return;
        }

        $errors      = $event->getSubject();
        $optionName  = $event->getArgument('optionName');
        $optionValue = $event->getArgument('optionValue');

        if (count($errors) === 0) {

            $this->_logger->debug(sprintf("Accepted valid value: '%s' = '%s'", $optionName, $this->_stringUtils->redactSecrets($optionValue)));

        } else {

            $this->_logger->error(sprintf("Rejecting invalid value: '%s' = '%s' (%s)",
                $optionName, $this->_stringUtils->redactSecrets($optionValue),
                $errors[0]));
        }
    }
}