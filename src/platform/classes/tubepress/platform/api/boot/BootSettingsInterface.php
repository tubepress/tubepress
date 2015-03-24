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
     * @return bool True if the system cache is enabled. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function isSystemCacheEnabled();

    /**
     * @return string An absolute path on the filesystem where TubePress can store its cache data.
     *
     * @api
     * @since 4.0.0
     */
    function getPathToSystemCacheDirectory();

    /**
     * @return string The absolute path of the user's content directory. In WordPress, this will be
     *                wp-content/tubepress-content. In standalone PHP, this will be tubepress/tubepress-content.
     *
     * @api
     * @since 4.0.0
     */
    function getUserContentDirectory();

    /**
     * Gets the encoding that should be applied to the add-ons and theme data before it is persisted
     * to file.
     *
     * @return string One of base64, urlencode, gzip-then-base64, or none
     *
     * @api
     * @since 4.0.0
     */
    function getSerializationEncoding();

    /**
     * @api
     * @since 4.0.9
     *
     * @return tubepress_platform_api_url_UrlInterface|null
     */
    function getUrlBase();

    /**
     * @api
     * @since 4.0.9
     *
     * @return tubepress_platform_api_url_UrlInterface|null
     */
    function getUrlUserContent();

    /**
     * @api
     * @since 4.0.9
     *
     * @return tubepress_platform_api_url_UrlInterface|null
     */
    function getUrlAjaxEndpoint();
}
