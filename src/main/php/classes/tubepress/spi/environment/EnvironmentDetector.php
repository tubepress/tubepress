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
interface tubepress_spi_environment_EnvironmentDetector
{
    const _ = 'tubepress_spi_environment_EnvironmentDetector';

    /**
     * Detects if the user is running TubePress Pro.
     *
     * @return boolean True is the user is running TubePress Pro. False otherwise (or if there is a problem detecting the environment).
     */
    function isPro();

    /**
     * Detects if the user is running within WordPress
     *
     * @return boolean True is the user is running within WordPress (or if there is a problem detecting the environment). False otherwise.
     */
    function isWordPress();

    /**
     * Find the absolute path of the user's content directory. In WordPress, this will be
     * wp-content/tubepress-content. In standalone PHP, this will be tubepress/tubepress-content. Confusing, I know.
     *
     * @return string The absolute path of the user's content directory.
     */
    function getUserContentDirectory();

    /**
     * Get the current TubePress version.
     *
     * @return tubepress_spi_version_Version The current version.
     */
    function getVersion();

    /**
     * @return string The base TubePress URL.
     */
    function getBaseUrl();

    /**
     * Set the TubePress base URL.
     *
     * @param mixed $url The new base URL.
     *
     * @return void
     */
    function setBaseUrl($url);

    /**
     * @return string The user content URL.
     */
    function getUserContentUrl();

    /**
     * Set the user content URL.
     *
     * @param mixed $url The user content URL.
     *
     * @return void
     */
    function setUserContentUrl($url);
}
