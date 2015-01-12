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
 * Holds the current options for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a shortcode
 */
class tubepress_app_impl_options_Context implements tubepress_app_api_options_ContextInterface
{
    /**
     * The user's "custom" options that differ from what's in storage.
     */
    private $_ephemeralOptions = array();

    /**
     * @var tubepress_app_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_app_api_options_ReferenceInterface
     */
    private $_optionReference;

    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * Constructor.
     */
    public function __construct(tubepress_app_api_options_PersistenceInterface   $persistence,
                                tubepress_lib_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_app_api_options_ReferenceInterface     $reference)
    {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_persistence     = $persistence;
        $this->_optionReference = $reference;
    }

    /**
     * Gets the value of an option. Memory will be checked first, then the option value
     * will be retrieved from persistent storage.
     *
     * @param string $optionName The name of the option to retrieve.
     *
     * @throws InvalidArgumentException If no option with the given name is known.
     *
     * @return mixed The option value.
     *
     * @since 4.0.0
     */
    public function get($optionName)
    {
        if (array_key_exists($optionName, $this->_ephemeralOptions)) {

            return $this->_ephemeralOptions[$optionName];
        }

        try {

            return $this->_persistence->fetch($optionName);

        } catch (InvalidArgumentException $e) {

            if ($this->_optionReference->optionExists($optionName) &&
                !$this->_optionReference->isMeantToBePersisted($optionName)) {

                return null;
            }

            throw $e;
        }
    }

    /**
     * Get options persisted in memory.
     *
     * @return array An associative array of options stored in memory. The array keys are option names
     *               and the values are the values stored in memory.
     *
     * @since 4.0.0
     */
    public function getEphemeralOptions()
    {
        return $this->_ephemeralOptions;
    }

    /**
     * Sets the value of an option in memory. This will *not* affect persistent storage.
     *
     * @param string $optionName  The name of the option
     * @param mixed  $optionValue The option value
     *
     * @return null|string A string error message if there was a problem with the option or value,
     *                     otherwise null.
     *
     * @api
     * @since 4.0.0
     */
    public function setEphemeralOption($optionName, $optionValue)
    {
        $errors = $this->getErrors($optionName, $optionValue);

        if (count($errors) === 0) {

            $this->_ephemeralOptions[$optionName] = $optionValue;

            return null;
        }

        return $errors[0];
    }

    /**
     * Sets all ephemeral option values, overwriting anything in memory. This will *not* affect persistent storage.
     *
     * @param array $customOpts An associative array of options. The array keys are option names
     *                          and the values are the values stored in memory.
     *
     * @return array An array of error messages. May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function setEphemeralOptions(array $customOpts)
    {
        $toReturn                = array();
        $this->_ephemeralOptions = array();

        foreach ($customOpts as $name => $value) {

            $error = $this->setEphemeralOption($name, $value);

            if ($error !== null) {

                $toReturn[] = $error;
            }
        }

        return $toReturn;
    }

    protected function getErrors($optionName, &$optionValue)
    {
        $externallyCleanedValue = $this->_dispatchForExternalInput($optionName, $optionValue);

        $event = $this->_dispatchForOptionSet(
            $optionName,
            $externallyCleanedValue,
            array(),
            tubepress_app_api_event_Events::OPTION_SET . '.' . $optionName
        );

        $event = $this->_dispatchForOptionSet(
            $optionName,
            $event->getArgument('optionValue'),
            $event->getSubject(),
            tubepress_app_api_event_Events::OPTION_SET
        );

        $optionValue = $event->getArgument('optionValue');

        return $event->getSubject();
    }

    private function _dispatchForExternalInput($optionName, $optionValue)
    {
        $event = $this->_eventDispatcher->newEventInstance($optionValue, array(

            'optionName' => $optionName
        ));

        $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::NVP_FROM_EXTERNAL_INPUT, $event);

        return $event->getSubject();
    }

    private function _dispatchForOptionSet($optionName, $optionValue, array $errors, $eventName)
    {
        $event = $this->_eventDispatcher->newEventInstance($errors, array(

            'optionName'  => $optionName,
            'optionValue' => $optionValue
        ));

        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event;
    }
}