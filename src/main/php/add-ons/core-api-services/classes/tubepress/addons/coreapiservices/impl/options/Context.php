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
class tubepress_addons_coreapiservices_impl_options_Context implements tubepress_api_options_ContextInterface
{
    /**
     * The user's "custom" options that differ from what's in storage.
     */
    private $_customOptions = array();

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var tubepress_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * Constructor.
     */
    public function __construct(tubepress_api_options_PersistenceInterface $persistence)
    {
        $this->_logger      = ehough_epilog_LoggerFactory::getLogger('Memory Execution Context');
        $this->_persistence = $persistence;
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
        $eventDispatcherService = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $optionProvider         = tubepress_impl_patterns_sl_ServiceLocator::getOptionProvider();

        /** First run it through the filters. */
        $event = new tubepress_spi_event_EventBase($optionValue, array(

            'optionName' => $optionName
        ));
        $eventDispatcherService->dispatch(tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, $event);
        $filteredValue = $event->getSubject();

        $event = new tubepress_spi_event_EventBase($filteredValue);
        $eventDispatcherService->dispatch(tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . ".$optionName", $event);
        $filteredValue = $event->getSubject();

        if ($optionProvider->isValid($optionName, $filteredValue)) {

            if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

                $this->_logger->debug(sprintf('Accepted valid value: %s = %s', $optionName, $this->_normalizeForStringOutput($filteredValue)));
            }

            $this->_customOptions[$optionName] = $filteredValue;

            return true;
        }

        $problemMessage = $optionProvider->getProblemMessage($optionName, $filteredValue);

        if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            $this->_logger->warn(sprintf('Ignoring invalid value for "%s" (%s)', $optionName, $this->_normalizeForStringOutput($problemMessage)));
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

        return tubepress_impl_util_StringUtils::redactSecrets((string) $candidate);
    }
}
