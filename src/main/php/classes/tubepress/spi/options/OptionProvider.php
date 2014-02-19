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
 * Provides options.
 */
interface tubepress_spi_options_OptionProvider
{
    const _ = 'tubepress_spi_options_OptionProvider';

    /**
     * Fetch all the option names from this provider.
     *
     * @return string[]
     */
    function getAllOptionNames();

    /**
     * @param $optionName string The option name.
     *
     * @return array An associative array of values to untranslated descriptions that the given
     *               option can accept. May be null if the option does not support discrete values.
     */
    function getDiscreteAcceptableValues($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return mixed The default value for this option. May be null.
     */
    function getDefaultValue($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return string The human-readable description of this option. May be empty or null.
     */
    function getDescription($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return string The short label for this option. 30 chars or less. May be null.
     */
    function getLabel($optionName);

    /**
     * Gets the failure message of a name/value pair that has failed validation.
     *
     * @param string $optionName The option name
     * @param mixed  $candidate  The candidate option value
     *
     * @return mixed Null if the option passes validation, otherwise a string failure message.
     */
    function getProblemMessage($optionName, $candidate);

    /**
     * @param $optionName string The option name to lookup.
     *
     * @return bool True if the option exists, false otherwise.
     */
    function hasOption($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option can be set via shortcode, false otherwise.
     */
    function isAbleToBeSetViaShortcode($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option takes on only boolean values, false otherwise.
     */
    function isBoolean($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return bool Should we store this option in persistent storage?
     */
    function isMeantToBePersisted($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return bool Is this option Pro only?
     */
    function isProOnly($optionName);

    /**
     * Validates an option value.
     *
     * @param string $optionName The option name
     * @param mixed  $candidate  The candidate option value
     *
     * @return boolean True if the option name exists and the value supplied is valid. False otherwise.
     */
    function isValid($optionName, $candidate);
}
