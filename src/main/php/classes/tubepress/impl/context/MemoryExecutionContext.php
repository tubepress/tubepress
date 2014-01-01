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
class tubepress_impl_context_MemoryExecutionContext implements tubepress_spi_context_ExecutionContext
{
    /**
     * The user's "custom" options that differ from what's in storage.
     */
    private $_customOptions = array();

    /**
     * The actual shortcode used.
     */
    private $_actualShortcodeUsed;

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Memory Execution Context');
    }

    /**
     * Resets the context.
     *
     * @return void
     */
    public final function reset()
    {
        $this->_customOptions       = array();
        $this->_actualShortcodeUsed = '';
    }

    /**
     * Gets the value of an option
     *
     * @param string $optionName The name of the option
     *
     * @return mixed The option value
     */
    public final function get($optionName)
    {
        /* get the value, either from the shortcode or the db */
        if (array_key_exists($optionName, $this->_customOptions)) {

            return $this->_customOptions[$optionName];
        }

        $optionStorageManagerService = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();

        return $optionStorageManagerService->fetch($optionName);
    }

    /**
     * Sets the value of an option
     *
     * @param string $optionName  The name of the option
     * @param mixed  $optionValue The option value
     *
     * @return mixed True if the option was set normally, otherwise a string error message.
     */
    public final function set($optionName, $optionValue)
    {
        $eventDispatcherService = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $optionValidatorService = tubepress_impl_patterns_sl_ServiceLocator::getOptionValidator();

        /** First run it through the filters. */
        /** Run it through the filters. */
        $event = new tubepress_spi_event_EventBase($optionValue, array(

            'optionName' => $optionName
        ));
        $eventDispatcherService->dispatch(tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, $event);
        $filteredValue = $event->getSubject();

        if ($optionValidatorService->isValid($optionName, $filteredValue)) {

            if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

                $this->_logger->debug(sprintf('Accepted valid value: %s = %s', $optionName, $this->_normalizeForStringOutput($filteredValue)));
            }

            $this->_customOptions[$optionName] = $filteredValue;

            return true;
        }

        $problemMessage = $optionValidatorService->getProblemMessage($optionName, $filteredValue);

        if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            $this->_logger->warn(sprintf('Ignoring invalid value for "%s" (%s)', $optionName, $problemMessage));
        }

        return $problemMessage;
    }

    /**
     * Sets the options that differ from the default options.
     *
     * @param array $customOpts The custom options.
     *
     * @return array An array of error messages. May be empty, never null.
     */
    public final function setCustomOptions(array $customOpts)
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

    /**
     * Gets the options that differ from the default options.
     *
     * @return array The options that differ from the default options.
     */
    public final function getCustomOptions()
    {
        return $this->_customOptions;
    }

    /**
     * Set the current shortcode.
     *
     * @param string $newTagString The current shortcode
     *
     * @return void
     */
    public final function setActualShortcodeUsed($newTagString)
    {
        $this->_actualShortcodeUsed = $newTagString;
    }

    /**
     * Get the current shortcode
     *
     * @return string The current shortcode
     */
    public final function getActualShortcodeUsed()
    {
        return $this->_actualShortcodeUsed;
    }

    public static function convertBooleans($map)
    {
        $optionDescriptorReference = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();

        foreach ($map as $key => $value) {

            $optionDescriptor = $optionDescriptorReference->findOneByName($key);

            if ($optionDescriptor === null || !$optionDescriptor->isBoolean()) {

                continue;
            }

            $map[$key] = $value ? true : false;
        }

        return $map;
    }

    private function _normalizeForStringOutput($candidate)
    {
        if (is_array($candidate)) {

            return json_encode($candidate);
        }

        return (string) $candidate;
    }
}
