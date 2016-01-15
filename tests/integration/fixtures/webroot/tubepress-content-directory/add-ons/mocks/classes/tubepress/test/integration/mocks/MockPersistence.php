<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_test_integration_mocks_MockPersistence implements tubepress_spi_options_PersistenceBackendInterface
{
    /**
     * @var array
     */
    private $_mockOptions;

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
        $this->saveAll($optionNamesToValuesMap);
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
        $this->_mockOptions = array_merge($this->_mockOptions, $optionNamesToValues);

        return null;
    }

    /**
     * @return array An associative array of all option names to values. May be empty but never null.
     *
     * @api
     * @since 4.0.0
     */
    public function fetchAllCurrentlyKnownOptionNamesToValues()
    {
        if (!isset($this->_mockOptions)) {

            $file               = TUBEPRESS_CONTENT_DIRECTORY . '/config/database.json';
            $contents           = file_get_contents($file);
            $this->_mockOptions = json_decode($contents, true);
        }

        return $this->_mockOptions;
    }
}