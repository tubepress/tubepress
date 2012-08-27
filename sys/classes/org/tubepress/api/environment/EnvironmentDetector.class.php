<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Detects TubePress's environment
 */
interface org_tubepress_api_environment_EnvironmentDetector
{
    const _ = 'org_tubepress_api_environment_EnvironmentDetector';

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
     * Attempt to get temporary directory.
     *
     * @return string The absolute path of a temporary directory, preferably the system directory.
     */
    function getSystemTempDirectory();

    /**
     * Finds the absolute path of the TubePress installation on the filesystem.
     *
     * @return string The absolute filesystem path of this TubePress installation.
     */
    function getTubePressBaseInstallationPath();

    /**
     * Find the directory name of the TubePress base installation.
     *
     * @return string The base name of the TubePress installation directory.
     */
    function getTubePressInstallationDirectoryBaseName();
}
