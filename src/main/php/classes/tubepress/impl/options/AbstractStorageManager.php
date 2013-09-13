<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles persistent storage of TubePress options
 */
abstract class tubepress_impl_options_AbstractStorageManager implements tubepress_spi_options_StorageManager
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Abstract Storage Manager');
    }

    /**
     * Sets an option value
     *
     * @param string $optionName  The option name
     * @param mixed  $optionValue The option value
     *
     * @return boolean True on success, otherwise a string error message.
     */
    public final function set($optionName, $optionValue)
    {
        $optionDescriptorReferenceService = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();
        $descriptor                       = $optionDescriptorReferenceService->findOneByName($optionName);
        $shouldLog                        = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        /** Do we even know about this option? */
        if ($descriptor === null) {

            if ($shouldLog) {

                $this->_logger->warn("Could not find descriptor for option with name '$optionName''");
            }

            return true;
        }

        /** Ignore any options that aren't meant to be persisted. */
        if (! $descriptor->isMeantToBePersisted()) {

            return true;
        }

        $eventDispatcherService = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $optionValidatorService = tubepress_impl_patterns_sl_ServiceLocator::getOptionValidator();

        /** Run it through the filters. */
        $event = new tubepress_spi_event_EventBase($optionValue, array(

            'optionName' => $optionName
        ));
        $eventDispatcherService->dispatch(tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, $event);
        $filteredValue = $event->getSubject();

        /** OK, let's see if it's valid. */
        if ($optionValidatorService->isValid($optionName, $filteredValue)) {

            if ($shouldLog) {

                $this->_logger->info(sprintf("Accepted valid value: '%s' = '%s'", $optionName, $filteredValue));
            }

            $this->saveValidatedOption($optionName, $filteredValue);

            return true;
        }

        $problemMessage = $optionValidatorService->getProblemMessage($optionName, $filteredValue);

        if ($shouldLog) {

            $this->_logger->info(sprintf("Ignoring invalid value: '%s' = '%s'", $optionName, $filteredValue));
        }

        return $problemMessage;
    }

    /**
     * Sets an option to a new value, without validation
     *
     * @param string $optionName  The name of the option to update
     * @param mixed  $optionValue The new option value
     *
     * @return void
     */
    protected abstract function saveValidatedOption($optionName, $optionValue);
}
