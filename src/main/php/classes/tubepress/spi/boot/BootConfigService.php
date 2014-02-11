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
 * Retrieves options for boot.
 */
interface tubepress_spi_boot_BootConfigService
{
    const _ = 'tubepress_spi_boot_BootConfigService';

    /**
     * @return bool True if classloader registration is enabled.
     */
    function isClassLoaderEnabled();

    /**
     * @return array An array of names of add-ons that have been blacklisted.
     */
    function getAddonBlacklistArray();

    /**
     * @return ehough_stash_interfaces_PoolInterface A functioning boot cache.
     */
    function getBootCache();
}
