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
 * Handles persistent storage of TubePress options
 */
abstract class tubepress_impl_options_AbstractStorageManager implements tubepress_spi_options_StorageManager
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var array
     */
    private $_saveQueue;

    /**
     * @var array
     */
    private $_cachedOptions;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Abstract Storage Manager');
    }

    /**
     * @return array An associative array of all the options known by this manager. The keys are option
     *               names and the values are the stored option values.
     */
    public final function fetchAll()
    {
        if (!isset($this->_cachedOptions)) {

            $this->_cachedOptions = $this->fetchAllCurrentlyKnownOptionNamesToValues();
        }

        return $this->_cachedOptions;
    }

    /**
     * Retrieve the current value of an option from this storage manager.
     *
     * @param string $optionName The name of the option
     *
     * @return mixed|null The option's stored value, or null if no such option.
     */
    public final function fetch($optionName)
    {
        $allOptions = $this->fetchAll();

        if (isset($allOptions[$optionName])) {

            return $allOptions[$optionName];
        }

        return null;
    }

    /**
     * Queue a name-value pair for storage.
     *
     * @param string $optionName  The option name.
     * @param mixed  $optionValue The option value.
     *
     * @return string|null Null if the option was accepted for storage, otherwise a string error message.
     */
    public final function queueForSave($optionName, $optionValue)
    {
        /**
         * Init the queue if need be.
         */
        if (!isset($this->_saveQueue)) {

            $this->_saveQueue = array();
        }

        /**
         * First filter the incoming option.
         */
        $filteredValue = $this->_getFilteredValue($optionName, $optionValue);

        /**
         * Now validate it.
         */
        $validationResult = $this->_validateOneForStorage($optionName, $filteredValue);

        if ($validationResult !== true) {

            return $validationResult;
        }

        $optionDescriptorReferenceService = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();
        $optionDescriptor                 = $optionDescriptorReferenceService->findOneByName($optionName);

        if (!$optionDescriptor->isMeantToBePersisted()) {

            /**
             * Not meant to be persisted. Just ignore.
             */
            return null;
        }

        if ($this->_noChangeBetweenIncomingAndCurrent($filteredValue, $optionDescriptor)) {

            /**
             * No change. Ignore.
             */
            return null;
        }

        /**
         * Option passed validation and is meant to be persisted.
         */
        $this->_saveQueue[$optionName] = $filteredValue;

        return null;
    }

    /**
     * Flush the save queue. This function will empty the queue regardless of whether or not an error occurred during
     * save.
     *
     * @return null|string Null if the flush succeeded and all queued options were saved, otherwise a string error message.
     */
    public final function flushSaveQueue()
    {
        if (!isset($this->_saveQueue) || count($this->_saveQueue) === 0) {

            return null;
        }

        $result = $this->saveAll($this->_saveQueue);

        unset($this->_saveQueue);

        /**
         * Rebuild cache of option values.
         */
        $this->_forceReloadOfOptionsCache();

        return $result;
    }

    /**
     * Creates one or more options in storage, if they don't already exist. This function is called on TubePress's boot.
     *
     * @param array $optionNamesToValuesMap An associative array of option names to option values. For each
     *                                      element in the array, the storage manager will create the option if it does
     *                                      not already exist.
     *
     * @return void
     */
    public final function createEachIfNotExists(array $optionNamesToValuesMap)
    {
        $options             = $this->fetchAll();
        $allKnowOptionNames  = array_keys($options);
        $incomingOptionNames = array_keys($optionNamesToValuesMap);
        $missingOptionNames  = array_diff($incomingOptionNames, $allKnowOptionNames);

        if (count($missingOptionNames) === 0) {

            //common case
            return;
        }

        $this->createEach($optionNamesToValuesMap);

        $this->_forceReloadOfOptionsCache();
    }

    private function _forceReloadOfOptionsCache()
    {
        unset($this->_cachedOptions);
        $this->fetchAll();
    }

    /**
     * Creates one or more options in storage, if they don't already exist. This function is called on TubePress's boot.
     *
     * @param array $optionNamesToValuesMap An associative array of option names to option values. For each
     *                                      element in the array, the storage manager will create the option if it does
     *                                      not already exist.
     *
     * @return void
     */
    protected abstract function createEach(array $optionNamesToValuesMap);

    /**
     * @param array $optionNamesToValues An associative array of option names to values.
     *
     * @return null|string Null if the save succeeded and all queued options were saved, otherwise a string error message.
     */
    protected abstract function saveAll(array $optionNamesToValues);

    /**
     * @return array An associative array of all option names to values.
     */
    protected abstract function fetchAllCurrentlyKnownOptionNamesToValues();

    /**
     * @param $optionName
     * @param $optionValue
     *
     * @return bool True if the option is OK for storage, otherwise a string error message.
     */
    private function _validateOneForStorage($optionName, $optionValue)
    {
        $optionValidatorService = tubepress_impl_patterns_sl_ServiceLocator::getOptionValidator();
        $shouldLog              = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        /** OK, let's see if it's valid. */
        if ($optionValidatorService->isValid($optionName, $optionValue)) {

            if ($shouldLog) {

                $this->_logger->info(sprintf("Accepted valid value: '%s' = '%s'", $optionName, $optionValue));
            }

            return true;
        }

        $problemMessage = $optionValidatorService->getProblemMessage($optionName, $optionValue);

        if ($shouldLog) {

            $this->_logger->info(sprintf("Ignoring invalid value: '%s' = '%s'", $optionName, $optionValue));
        }

        return $problemMessage;
    }

    private function _getFilteredValue($optionName, $optionValue)
    {
        $eventDispatcherService = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        /** Run it through the filters. */
        $event = new tubepress_spi_event_EventBase($optionValue, array(

            'optionName' => $optionName
        ));
        $eventDispatcherService->dispatch(tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, $event);

        return $event->getSubject();
    }

    private function _noChangeBetweenIncomingAndCurrent($filteredValue, tubepress_spi_options_OptionDescriptor $descriptor)
    {
        $boolean      = $descriptor->isBoolean();
        $currentValue = $this->fetch($descriptor->getName());

        if ($boolean) {

            return ((boolean) $filteredValue) === ((boolean) $currentValue);
        }

        return $currentValue == $filteredValue;
    }
}
