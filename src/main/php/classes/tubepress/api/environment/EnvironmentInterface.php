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
 */
interface tubepress_api_environment_EnvironmentInterface
{
    const _ = 'tubepress_api_environment_EnvironmentInterface';

    /**
     * Detects if the user is running TubePress Pro.
     *
     * @return boolean True is the user is running TubePress Pro. False otherwise.
     */
    function isPro();

    /**
     * Detects if the user is running within WordPress
     *
     * @return boolean True is the user is running within WordPress. False otherwise.
     */
    function isWordPress();

    /**
     * Find the absolute path of the user's content directory. In WordPress, this will be
     * wp-content/tubepress-content. In standalone PHP, this will be tubepress/tubepress-content.
     *
     * @return string The absolute path of the user's content directory.
     */
    function getUserContentDirectory();

    /**
     * Get the current TubePress version.
     *
     * @return tubepress_api_version_Version The current version.
     */
    function getVersion();

    /**
     * @return tubepress_api_url_UrlInterface The base TubePress URL. May be null.
     */
    function getBaseUrl();

    /**
     * Set the TubePress base URL.
     *
     * @param string|tubepress_api_url_UrlInterface $url The new base URL.
     *
     * @throws InvalidArgumentException If unable to parse URL.
     *
     * @return void
     */
    function setBaseUrl($url);

    /**
     * @return tubepress_api_url_UrlInterface The user content URL. May be null.
     */
    function getUserContentUrl();

    /**
     * Set the user content URL.
     *
     * @param string|tubepress_api_url_UrlInterface $url The user content URL.
     *
     * @throws InvalidArgumentException If unable to parse URL.
     *
     * @return void
     */
    function setUserContentUrl($url);
}
