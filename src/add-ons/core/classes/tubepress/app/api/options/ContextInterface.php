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
 * Provides access to the TubePress options mechanism.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_api_options_ContextInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_api_options_ContextInterface';

    /**
     * Gets the value of an option. Memory will be checked first, then the option value
     * will be retrieved from persistent storage.
     *
     * @param string $optionName The name of the option to retrieve.
     *
     * @throws InvalidArgumentException If no option with the given name is known.
     *
     * @return mixed The option value. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function get($optionName);

    /**
     * Get options persisted in memory.
     *
     * @return array An associative array of options stored in memory. The array keys are option names
     *               and the values are the values stored in memory. May be empty but never null.
     *
     * @api
     * @since 4.0.0
     */
    function getEphemeralOptions();

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
    function setEphemeralOption($optionName, $optionValue);

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
    function setEphemeralOptions(array $customOpts);
}