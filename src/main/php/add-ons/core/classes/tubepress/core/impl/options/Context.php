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
 * Holds the current options for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a shortcode
 */
class tubepress_core_impl_options_Context implements tubepress_core_api_options_ContextInterface
{
    /**
     * The user's "custom" options that differ from what's in storage.
     */
    private $_customOptions = array();

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_api_options_ProviderInterface
     */
    private $_optionProvider;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * Constructor.
     */
    public function __construct(tubepress_api_log_LoggerInterface                 $logger,
                                tubepress_core_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_core_api_options_PersistenceInterface   $persistence,
                                tubepress_core_api_options_ProviderInterface      $optionProvider,
                                tubepress_api_util_StringUtilsInterface           $stringUtils)
    {
        $this->_logger          = $logger;
        $this->_persistence     = $persistence;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_optionProvider  = $optionProvider;
        $this->_stringUtils     = $stringUtils;
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
        /* get the value, either from the shortcode or the db */
        if (array_key_exists($optionName, $this->_customOptions)) {

            return $this->_customOptions[$optionName];
        }

        return $this->_persistence->fetch($optionName);
    }

    /**
     * Get options persisted in memory.
     *
     * @return array An associative array of options stored in memory. The array keys are option names
     *               and the values are the values stored in memory.
     *
     * @since 4.0.0
     */
    public function getAllInMemory()
    {
        return $this->_customOptions;
    }

    /**
     * Sets the value of an option in memory. This will *not* affect persistent storage.
     *
     * @param string $optionName  The name of the option
     * @param mixed  $optionValue The option value
     *
     * @return boolean|string True if the option was set normally, otherwise a string error message.
     *
     * @since 4.0.0
     */
    public function set($optionName, $optionValue)
    {
        /** First run it through the filters. */
        $event = $this->_eventDispatcher->newEventInstance($optionValue, array(

            'optionName' => $optionName
        ));
        $this->_eventDispatcher->dispatch(tubepress_core_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, $event);
        $filteredValue = $event->getSubject();

        $event = $this->_eventDispatcher->newEventInstance($filteredValue);
        $this->_eventDispatcher->dispatch(tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . ".$optionName", $event);
        $filteredValue = $event->getSubject();

        if ($this->_optionProvider->isValid($optionName, $filteredValue)) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->debug(sprintf('Accepted valid value: %s = %s', $optionName, $this->_normalizeForStringOutput($filteredValue)));
            }

            $this->_customOptions[$optionName] = $filteredValue;

            return true;
        }

        $problemMessage = $this->_optionProvider->getProblemMessage($optionName, $filteredValue);

        if ($this->_logger->isEnabled()) {

            $this->_logger->error(sprintf('Ignoring invalid value for "%s" (%s)', $optionName, $this->_normalizeForStringOutput($problemMessage)));
        }

        return $problemMessage;
    }

    /**
     * Sets all ephemeral option values, overwriting anything in memory. This will *not* affect persistent storage.
     *
     * @param array $customOpts An associative array of options. The array keys are option names
     *                          and the values are the values stored in memory.
     *
     * @return string[] An array of error messages. May be empty, never null.
     *
     * @since 4.0.0
     */
    public function setAll(array $customOpts)
    {
        $this->_customOptions = array();
        $problemMessages      = array();

        foreach ($customOpts as $key => $value) {

            $result = $this->set($key, $value);

            if ($result === true) {

                continue;
            }

            $problemMessages[] = $result;
        }

        return $problemMessages;
    }

    private function _normalizeForStringOutput($candidate)
    {
        if (is_array($candidate)) {

            $candidate = json_encode($candidate);
        }

        return $this->_stringUtils->redactSecrets((string) $candidate);
    }
}
