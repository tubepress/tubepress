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
 * Filesystem utilities.
 */
interface org_tubepress_api_filesystem_Explorer
{
    const _ = 'org_tubepress_api_filesystem_Explorer';

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

    /**
     * Find the directories contained in the given directory (non-recursive).
     *
     * @param string $dir    The absolute filesystem path of the directory to examine.
     * @param string $prefix The logging prefix.
     *
     * @return array The names of the directories in the given directory (non-recursive).
     */
    function getDirectoriesInDirectory($dir, $prefix);

    /**
     * Find the files contained in the given directory (non-recursive).
     *
     * @param string $dir    The absolute filesystem path of the directory to examine.
     * @param string $prefix The logging prefix.
     *
     * @return array The names of the files in the given directory (non-recursive).
     */
    function getFilenamesInDirectory($dir, $prefix);

    /**
     * Attempt to get temporary directory.
     *
     * @return string The absolute path of a temporary directory, preferably the system directory.
     */
    function getSystemTempDirectory();

    function copyDirectory($source, $dest);

    function ensureDirectoryExists($directory);
}
