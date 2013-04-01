<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles persistent storage of TubePress options
 *
 */
interface tubepress_spi_options_StorageManager
{
    const _ = 'tubepress_spi_options_StorageManager';

    /**
     * Retrieve the current value of an option
     *
     * @param string $optionName The name of the option
     *
     * @return mixed The option's value
     */
    function get($optionName);

    /**
     * Sets an option value
     *
     * @param string $optionName  The option name
     * @param mixed  $optionValue The option value
     *
     * @return boolean True on success, otherwise a string error message.
     */
    function set($optionName, $optionValue);

    /**
     * Creates an option in storage
     *
     * @param mixed $optionName  The name of the option to create
     * @param mixed $optionValue The default value of the new option
     *
     * @return void
     */
    function createIfNotExists($optionName, $optionValue);
}
