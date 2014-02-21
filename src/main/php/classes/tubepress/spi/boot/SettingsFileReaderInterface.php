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
 * System settings interface.
 */
interface tubepress_spi_boot_SettingsFileReaderInterface
{
    const _ = 'tubepress_spi_boot_SettingsFileReaderInterface';

    /**
     * @return bool True if the cache killer key has been set by the user.
     */
    function shouldClearCache();

    /**
     * @return bool True if the container cache is enabled. False otherwise.
     */
    function isContainerCacheEnabled();

    /**
     * @return bool True if classloader registration is enabled.
     */
    function isClassLoaderEnabled();

    /**
     * @return array An array of names of add-ons that have been blacklisted.
     */
    function getAddonBlacklistArray();

    /**
     * @return string An absolute path to a readable and writable file where TubePress
     *                can store the compiled service container.
     */
    function getCachedContainerStoragePath();
}
