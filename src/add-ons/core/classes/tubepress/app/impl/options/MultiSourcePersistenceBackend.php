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
class tubepress_app_impl_options_MultiSourcePersistenceBackend implements tubepress_app_api_options_PersistenceBackendInterface
{
    /**
     * @var array
     */
    private $_originalOptions;

    /**
     * @var array
     */
    private $_toPersist;

    public function __construct(array $options)
    {
        $this->_originalOptions = $options;
        $this->_toPersist       = array();
    }

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
    public function createEach(array $optionNamesToValuesMap)
    {
        //do nothing
    }

    /**
     * @param array $optionNamesToValues An associative array of option names to values.
     *
     * @return null|string Null if the save succeeded and all queued options were saved, otherwise a string error message.
     *
     * @api
     * @since 4.0.0
     */
    public function saveAll(array $optionNamesToValues)
    {
        $this->_toPersist = array_merge($this->_toPersist, $optionNamesToValues);
    }

    /**
     * @return array An associative array of all option names to values. May be empty but never null.
     *
     * @api
     * @since 4.0.0
     */
    public function fetchAllCurrentlyKnownOptionNamesToValues()
    {
        return $this->_originalOptions;
    }

    /**
     * @return array
     */
    public function getPersistenceQueue()
    {
        return $this->_toPersist;
    }
}
