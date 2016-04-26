<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_options_ui_impl_MultiSourcePersistenceBackend implements tubepress_spi_options_PersistenceBackendInterface
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
     * {@inheritdoc}
     */
    public function createEach(array $optionNamesToValuesMap)
    {
        //do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(array $optionNamesToValues)
    {
        $this->_toPersist = array_merge($this->_toPersist, $optionNamesToValues);
    }

    /**
     * {@inheritdoc}
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
