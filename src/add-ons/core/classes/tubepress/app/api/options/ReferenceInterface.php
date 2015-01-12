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
interface tubepress_app_api_options_ReferenceInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_api_options_ReferenceInterface';

    /**
     * Fetch all the option names from this provider.
     *
     * @return string[]
     *
     * @api
     * @since 4.0.0
     */
    function getAllOptionNames();

    /**
     * @param $optionName string The option name.
     *
     * @return mixed The default value for this option. May be null.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    function getDefaultValue($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return string The human-readable description of this option. May be empty or null.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    function getUntranslatedDescription($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return string The short label for this option. 30 chars or less. May be null.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    function getUntranslatedLabel($optionName);

    /**
     * @param $optionName string The option name to lookup.
     *
     * @return bool True if the option exists, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function optionExists($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option can be set via shortcode, false otherwise.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    function isAbleToBeSetViaShortcode($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option takes on only boolean values, false otherwise.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    function isBoolean($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return bool Should we store this option in persistent storage?
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    function isMeantToBePersisted($optionName);

    /**
     * @param $optionName string The option name.
     *
     * @return bool Is this option Pro only?
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    function isProOnly($optionName);

    /**
     * Get a property for the given option.
     *
     * @param string $optionName   The option name.
     * @param string $propertyName The property name.
     *
     * @return tubepress_platform_api_collection_MapInterface
     *
     * @throws InvalidArgumentException If the option name does not exist, or no such property for the option.
     *
     * @api
     * @since 4.0.0
     */
    function getProperty($optionName, $propertyName);

    /**
     * @param string $optionName   The option name.
     * @param string $propertyName The property name.
     *
     * @return bool True if this object contains a property with the given name, false otherwise.
     *
     * @throws InvalidArgumentException If the option name does not exist
     *
     * @api
     * @since 4.0.0
     */
    function hasProperty($optionName, $propertyName);

    /**
     * @param string $optionName   The option name.
     * @param string $propertyName The property name.
     *
     * @return bool The property value as converted to boolean.
     *
     * @throws InvalidArgumentException If the option name does not exist, or no such property for the option.
     *
     * @api
     * @since 4.0.0
     */
    function getPropertyAsBoolean($optionName, $propertyName);
}