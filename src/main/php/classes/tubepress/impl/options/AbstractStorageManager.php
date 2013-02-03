<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
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
    private $_logger;

    private $_knownOptionNames = array();

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Abstract Storage Manager');

        $this->_knownOptionNames = $this->getAllOptionNames();
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

        $descriptor = $optionDescriptorReferenceService->findOneByName($optionName);

        /** Do we even know about this option? */
        if ($descriptor === null) {

            $this->_logger->warn("Could not find descriptor for option with name '$optionName''");

            return true;
        }

        /** Ignore any options that aren't meant to be persisted. */
        if (! $descriptor->isMeantToBePersisted()) {

            return true;
        }

        $eventDispatcherService = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $optionValidatorService = tubepress_impl_patterns_sl_ServiceLocator::getOptionValidator();

        /** Run it through the filters. */
        $event = new tubepress_api_event_TubePressEvent($optionValue, array(

            'optionName' => $optionName
        ));
        $eventDispatcherService->dispatch(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET, $event);
        $filteredValue = $event->getSubject();

        /** OK, let's see if it's valid. */
        if ($optionValidatorService->isValid($optionName, $filteredValue)) {

            $this->_logger->info(sprintf("Accepted valid value: '%s' = '%s'", $optionName, $filteredValue));

            $this->setOption($optionName, $filteredValue);

            return true;
        }

        $problemMessage = $optionValidatorService->getProblemMessage($optionName, $filteredValue);

        $this->_logger->info(sprintf("Ignoring invalid value: '%s' = '%s'", $optionName, $filteredValue));

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
    protected abstract function setOption($optionName, $optionValue);

    /**
     * @return array All the option names currently in this storage manager.
     */
    protected abstract function getAllOptionNames();

    protected abstract function create($optionName, $optionValue);

    /**
     * Creates an option in storage
     *
     * @param mixed $optionName  The name of the option to create
     * @param mixed $optionValue The default value of the new option
     *
     * @return void
     */
    public final function createIfNotExists($optionName, $optionValue)
    {
        if (in_array($optionName, $this->_knownOptionNames)) {

            return;
        }

        $this->create($optionName, $optionValue);

        $this->_knownOptionNames[] = $optionName;
    }
}
