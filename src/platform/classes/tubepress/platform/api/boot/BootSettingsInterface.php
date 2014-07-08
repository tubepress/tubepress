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
 * Retrieves settings from a PHP file.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_platform_api_boot_BootSettingsInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_platform_api_boot_BootSettingsInterface';

    /**
     * @return bool True if the user has properly requested to clear the system cache.
     *
     * @api
     * @since 4.0.0
     */
    function shouldClearCache();

    /**
     * @return array An array of names of add-ons that have been blacklisted. May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    function getAddonBlacklistArray();

    /**
     * @return bool True if the internal classloader is enabled. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function isClassLoaderEnabled();

    /**
     * @return bool True if the container cache is enabled. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function isContainerCacheEnabled();

    /**
     * @return string An absolute path on the filesystem where TubePress should store the compiled service
     *                container.
     *
     * @api
     * @since 4.0.0
     */
    function getPathToContainerCacheFile();

    /**
     * @return string The absolute path of the user's content directory. In WordPress, this will be
     *                wp-content/tubepress-content. In standalone PHP, this will be tubepress/tubepress-content.
     *
     * @api
     * @since 4.0.0
     */
    function getUserContentDirectory();
}
