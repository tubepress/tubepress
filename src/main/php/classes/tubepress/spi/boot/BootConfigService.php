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
     * @param string $element The element to look up.
     *
     * @return bool True if caching is enabled for this element, false otherwise.
     */
    function isCacheEnabledForElement($element);

    /**
     * @return bool True if the cache killer is on, false otherwise.
     */
    function isCacheKillerTurnedOn();

    /**
     * @return bool True if classloader registration is enabled.
     */
    function isClassLoaderEnabled();

    /**
     * @param string $element The element to look up.
     *
     * @return string The absolute path of the element's cache file.
     */
    function getAbsolutePathToCacheFileForElement($element);

    /**
     * @return array An array of names of add-ons that have been blacklisted.
     */
    function getAddonBlacklistArray();
}
