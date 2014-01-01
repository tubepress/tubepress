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
 * Holds the current execution context for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a shortcode.
 */
interface tubepress_spi_context_ExecutionContext
{
    const _ = 'tubepress_spi_context_ExecutionContext';

    /**
     * Gets the value of an option
     *
     * @param string $optionName The name of the option
     *
     * @return mixed The option value
     */
    function get($optionName);

    /**
     * Sets the value of an option
     *
     * @param string $optionName  The name of the option
     * @param mixed  $optionValue The option value
     *
     * @return boolean True if the option was set normally, otherwise a string error message.
     */
    function set($optionName, $optionValue);

    /**
     * Sets the options that differ from the default options.
     *
     * @param array $customOpts The custom options.
     *
     * @return array An array of error messages. May be empty, never null.
     */
    function setCustomOptions(array $customOpts);

    /**
     * Gets the options that differ from the default options.
     *
     * @return array The options that differ from the default options.
     */
    function getCustomOptions();

    /**
     * Set the current shortcode.
     *
     * @param string $newTagString The current shortcode
     *
     * @return void
     */
    function setActualShortcodeUsed($newTagString);

    /**
     * Get the current shortcode
     *
     * @return string The current shortcode
     */
    function getActualShortcodeUsed();

    /**
     * Resets the context for fresh execution.
     */
    function reset();
}
