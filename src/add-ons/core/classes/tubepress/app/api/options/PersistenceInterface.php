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
 * Handles persistent storage of TubePress options.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_api_options_PersistenceInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_api_options_PersistenceInterface';

    /**
     * Retrieve the current value of an option from this storage manager.
     *
     * @param string $optionName The name of the option
     *
     * @return mixed|null The option's stored value.
     *
     * @throws InvalidArgumentException If no option with the given name is known.
     *
     * @api
     * @since 4.0.0
     */
    function fetch($optionName);

    /**
     * @return array An associative array of all the options known by this manager. The keys are option
     *               names and the values are the stored option values.
     *
     * @api
     * @since 4.0.0
     */
    function fetchAll();

    /**
     * Queue a name-value pair for storage. No validation is performed.
     *
     * @param string $optionName  The option name.
     * @param mixed  $optionValue The option value.
     *
     * @return string|null Null if the option was accepted for storage, otherwise a string error message.
     *
     * @api
     * @since 4.0.0
     */
    function queueForSave($optionName, $optionValue);

    /**
     * Flush the save queue. This function will empty the queue regardless of whether or not an error occurred during
     * save.
     *
     * @return null|string Null if the flush succeeded and all queued options were saved, otherwise a string error message.
     *
     * @api
     * @since 4.0.0
     */
    function flushSaveQueue();
}