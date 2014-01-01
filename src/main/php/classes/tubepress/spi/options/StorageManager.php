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
 * Handles persistent storage of TubePress options.
 */
interface tubepress_spi_options_StorageManager
{
    const _ = 'tubepress_spi_options_StorageManager';

    /**
     * Retrieve the current value of an option from this storage manager.
     *
     * @param string $optionName The name of the option
     *
     * @return mixed|null The option's stored value, or null if no such option.
     */
    function fetch($optionName);

    /**
     * @return array An associative array of all the options known by this manager. The keys are option
     *               names and the values are the stored option values.
     */
    function fetchAll();

    /**
     * Queue a name-value pair for storage.
     *
     * @param string $optionName  The option name.
     * @param mixed  $optionValue The option value.
     *
     * @return string|null Null if the option was accepted for storage, otherwise a string error message.
     */
    function queueForSave($optionName, $optionValue);

    /**
     * Flush the save queue. This function will empty the queue regardless of whether or not an error occurred during
     * save.
     *
     * @return null|string Null if the flush succeeded and all queued options were saved, otherwise a string error message.
     */
    function flushSaveQueue();

    /**
     * Creates one or more options in storage, if they don't already exist. This function is called on TubePress's boot.
     *
     * @param array $optionNamesToValuesMap An associative array of option names to option values. For each
     *                                      element in the array, the storage manager will create the option if it does
     *                                      not already exist.
     *
     * @return void
     */
    function createEachIfNotExists(array $optionNamesToValuesMap);
}
