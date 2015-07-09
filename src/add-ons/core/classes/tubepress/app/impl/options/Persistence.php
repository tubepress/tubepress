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
 * @api
 * @since 4.0.0
 */
class tubepress_app_impl_options_Persistence implements tubepress_app_api_options_PersistenceInterface
{
    /**
     * @var array
     */
    private $_saveQueue;

    /**
     * @var array
     */
    private $_cachedOptions;

    /**
     * @var bool
     */
    private $_flagCheckedForMissingOptions = false;

    /**
     * @var tubepress_app_api_options_ReferenceInterface
     */
    private $_optionsReference;

    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    private $_backend;

    public function __construct(tubepress_app_api_options_ReferenceInterface          $reference,
                                tubepress_lib_api_event_EventDispatcherInterface      $eventDispatcher,
                                tubepress_app_api_options_PersistenceBackendInterface $backend)
    {
        $this->_eventDispatcher  = $eventDispatcher;
        $this->_optionsReference = $reference;
        $this->_backend          = $backend;
    }

    public function getCloneWithCustomBackend(tubepress_app_api_options_PersistenceBackendInterface $persistenceBackend)
    {
        return new self($this->_optionsReference, $this->_eventDispatcher, $persistenceBackend);
    }

    /**
     * Retrieve the current value of an option from this storage manager.
     *
     * @param string $optionName The name of the option
     *
     * @return mixed|null The option's stored value.
     *
     * @throws InvalidArgumentException If no option with the given name is known.
     *
     * @api
     * @since 4.0.0
     */
    public function fetch($optionName)
    {
        $allOptions = $this->fetchAll();

        if (array_key_exists($optionName, $allOptions)) {

            return $allOptions[$optionName];
        }

        throw new InvalidArgumentException('No such option: ' . $optionName);
    }

    /**
     * @return array An associative array of all the options known by this manager. The keys are option
     *               names and the values are the stored option values.
     */
    public function fetchAll()
    {
        if (!isset($this->_cachedOptions)) {

            $this->_cachedOptions = $this->_backend->fetchAllCurrentlyKnownOptionNamesToValues();

            $this->_addAnyMissingOptions($this->_cachedOptions);
        }

        return $this->_cachedOptions;
    }

    /**
     * Queue a name-value pair for storage. No validation is performed.
     *
     * @param string $optionName  The option name.
     * @param mixed  $optionValue The option value.
     *
     * @return string|null Null if the option was accepted for storage, otherwise a string error message.
     *
     * @api
     * @since 4.0.0
     */
    public function queueForSave($optionName, $optionValue)
    {
        /**
         * Init the queue if need be.
         */
        if (!isset($this->_saveQueue)) {

            $this->_saveQueue = array();
        }

        if (!$this->_optionsReference->isMeantToBePersisted($optionName)) {

            /**
             * Not meant to be persisted. Just ignore.
             */
            return null;
        }

        $errors = $this->_getErrors($optionName, $optionValue);

        if (count($errors) > 0) {

            return $errors[0];
        }

        if ($this->_noChangeBetweenIncomingAndCurrent($optionName, $optionValue)) {

            /**
             * No change. Ignore.
             */
            return null;
        }

        /**
         * Option passed validation and is meant to be persisted.
         */
        $this->_saveQueue[$optionName] = $optionValue;

        return null;
    }

    /**
     * Flush the save queue. This public function will empty the queue regardless of whether or not an error occurred during
     * save.
     *
     * @return null|string Null if the flush succeeded and all queued options were saved, otherwise a string error message.
     */
    public function flushSaveQueue()
    {
        if (!isset($this->_saveQueue) || count($this->_saveQueue) === 0) {

            return null;
        }

        $result = $this->_backend->saveAll($this->_saveQueue);

        unset($this->_saveQueue);

        /**
         * Rebuild cache of option values.
         */
        $this->_forceReloadOfOptionsCache();

        return $result;
    }

    private function _forceReloadOfOptionsCache()
    {
        unset($this->_cachedOptions);

        $this->fetchAll();
    }

    private function _addAnyMissingOptions(array $optionsInThisStorageManager)
    {
        if ($this->_flagCheckedForMissingOptions) {

            return;
        }

        $optionNamesFromProvider = $this->_optionsReference->getAllOptionNames();
        $toPersist               = array();
        $missingOptions          = array_diff($optionNamesFromProvider, array_keys($optionsInThisStorageManager));

        /**
         * @var $optionName string
         */
        foreach ($missingOptions as $optionName) {

            if ($this->_optionsReference->isMeantToBePersisted($optionName)) {

                $toPersist[$optionName] = $this->_optionsReference->getDefaultValue($optionName);
            }
        }

        if (!empty($toPersist)) {

            $this->_backend->createEach($toPersist);
            $this->_forceReloadOfOptionsCache();
        }

        $this->_flagCheckedForMissingOptions = true;
    }

    private function _noChangeBetweenIncomingAndCurrent($optionName, $filteredValue)
    {
        $boolean      = $this->_optionsReference->isBoolean($optionName);
        $currentValue = $this->fetch($optionName);

        if ($boolean) {

            return ((boolean) $filteredValue) === ((boolean) $currentValue);
        }

        return $currentValue == $filteredValue;
    }

    private function _getErrors($optionName, &$optionValue)
    {
        $externallyCleanedValue = $this->_dispatchForExternalInput($optionName, $optionValue);

        $event = $this->_dispatch(
            $optionName,
            $externallyCleanedValue,
            array(),
            tubepress_app_api_event_Events::OPTION_SET . '.' . $optionName
        );
        $event = $this->_dispatch($optionName,
            $event->getArgument('optionValue'),
            $event->getSubject(), tubepress_app_api_event_Events::OPTION_SET
        );

        $optionValue = $event->getArgument('optionValue');

        return $event->getSubject();
    }

    /**
     * @param $optionName
     * @param $optionValue
     * @param array $errors
     * @param $eventName
     * @return tubepress_lib_api_event_EventInterface
     */
    private function _dispatch($optionName, $optionValue, array $errors, $eventName)
    {
        $event = $this->_eventDispatcher->newEventInstance($errors, array(

            'optionName'  => $optionName,
            'optionValue' => $optionValue
        ));

        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event;
    }

    private function _dispatchForExternalInput($optionName, $optionValue)
    {
        $event = $this->_eventDispatcher->newEventInstance($optionValue, array(

            'optionName' => $optionName
        ));

        $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::NVP_FROM_EXTERNAL_INPUT, $event);

        return $event->getSubject();
    }
}
