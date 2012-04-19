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
 * Holds the current execution context for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a shortcode.
 */
interface org_tubepress_api_exec_ExecutionContext
{
    const _ = 'org_tubepress_api_exec_ExecutionContext';

    /**
     * Gets the value of an option
     *
     * @param string $optionName The name of the option
     *
     * @return unknown The option value
     */
    function get($optionName);

    /**
     * Sets the value of an option
     *
     * @param string  $optionName  The name of the option
     * @param unknown $optionValue The option value
     *
     * @return True if the option was set normally, otherwise a string error message.
     */
    function set($optionName, $optionValue);

    /**
     * Sets the options that differ from the default options.
     *
     * @param array $customOpts The custom options.
     *
     * @return An array of error messages. May be empty, never null.
     */
    function setCustomOptions($customOpts);

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

    function toShortcode();

    /**
     * Resets the context for fresh execution.
     */
    function reset();
}
