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
 * Used by the persistence manager to implement a storage backend.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_api_options_PersistenceBackendInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_api_options_PersistenceBackendInterface';

    /**
     * Creates one or more options in storage, if they don't already exist.
     *
     * @param array $optionNamesToValuesMap An associative array of option names to option values. For each
     *                                      element in the array, the storage manager will create the option if it does
     *                                      not already exist.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function createEach(array $optionNamesToValuesMap);

    /**
     * @param array $optionNamesToValues An associative array of option names to values.
     *
     * @return null|string Null if the save succeeded and all queued options were saved, otherwise a string error message.
     *
     * @api
     * @since 4.0.0
     */
    function saveAll(array $optionNamesToValues);

    /**
     * @return array An associative array of all option names to values.
     *
     * @api
     * @since 4.0.0
     */
    function fetchAllCurrentlyKnownOptionNamesToValues();
}