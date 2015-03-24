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
 * Detects TubePress's environment
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_api_environment_EnvironmentInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_api_environment_EnvironmentInterface';

    /**
     * Detects if the user is running TubePress Pro.
     *
     * @return boolean True is the user is running TubePress Pro. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function isPro();

    /**
     * Get the current TubePress version.
     *
     * @return tubepress_platform_api_version_Version The current version.
     *
     * @api
     * @since 4.0.0
     */
    function getVersion();

    /**
     * @return tubepress_platform_api_url_UrlInterface The base TubePress URL.
     *
     * @throws RuntimeException If the base URL was not set or cannot be determined.
     *
     * @api
     * @since 4.0.0
     */
    function getBaseUrl();

    /**
     * @return tubepress_platform_api_url_UrlInterface The user content URL.
     *
     * @throws RuntimeException If the user content URL was not set or cannot be determined.
     *
     * @api
     * @since 4.0.0
     */
    function getUserContentUrl();

    /**
     * @return tubepress_platform_api_url_UrlInterface The Ajax endpoint URL.
     *
     * @throws RuntimeException If the Ajax endpoint URL was not set or cannot be determined.
     *
     * @api
     * @since 4.0.9
     */
    function getAjaxEndpointUrl();

    /**
     * @return tubepress_platform_api_collection_MapInterface
     *
     * @api
     * @since 4.0.0
     */
    function getProperties();





    /**
     * Set the user content URL.
     *
     * @deprecated Use settings.php instead.
     *
     * @param string|tubepress_platform_api_url_UrlInterface $url The user content URL.
     *
     * @throws InvalidArgumentException If unable to parse URL.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function setUserContentUrl($url);


    /**
     * Set the TubePress base URL.
     *
     * @deprecated Use settings.php instead.
     *
     * @param string|tubepress_platform_api_url_UrlInterface $url The new base URL.
     *
     * @throws InvalidArgumentException If unable to parse URL.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function setBaseUrl($url);
}