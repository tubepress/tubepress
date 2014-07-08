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
 * Detects TubePress's environment
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_environment_api_EnvironmentInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_environment_api_EnvironmentInterface';

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
     * @return tubepress_lib_version_api_Version The current version.
     *
     * @api
     * @since 4.0.0
     */
    function getVersion();

    /**
     * @return tubepress_lib_url_api_UrlInterface The base TubePress URL. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getBaseUrl();

    /**
     * Set the TubePress base URL.
     *
     * @param string|tubepress_lib_url_api_UrlInterface $url The new base URL.
     *
     * @throws InvalidArgumentException If unable to parse URL.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function setBaseUrl($url);

    /**
     * @return tubepress_lib_url_api_UrlInterface The user content URL. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getUserContentUrl();

    /**
     * Set the user content URL.
     *
     * @param string|tubepress_lib_url_api_UrlInterface $url The user content URL.
     *
     * @throws InvalidArgumentException If unable to parse URL.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function setUserContentUrl($url);
}