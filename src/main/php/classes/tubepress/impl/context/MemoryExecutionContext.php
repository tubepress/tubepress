<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
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

    /** Logger. */
    private $_logger;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Memory Execution Context');
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

        return $optionStorageManagerService->get($optionName);
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
        $event = new tubepress_api_event_TubePressEvent($optionValue, array(

            'optionName' => $optionName
        ));
        $eventDispatcherService->dispatch(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET, $event);
        $filteredValue = $event->getSubject();

        if ($optionValidatorService->isValid($optionName, $filteredValue)) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug(sprintf('Accepted valid value: %s = %s', $optionName, $filteredValue));
            }

            $this->_customOptions[$optionName] = $filteredValue;

            return true;
        }

        $problemMessage = $optionValidatorService->getProblemMessage($optionName, $filteredValue);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Ignoring invalid value for "%s" (%s)', $optionName, $problemMessage));
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
}
