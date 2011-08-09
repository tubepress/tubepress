<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_impl_log_Log',
));

/**
 * Some filesystem utilities
 *
 */
class org_tubepress_impl_filesystem_FsExplorer implements org_tubepress_api_filesystem_Explorer
{
	/**
	 * Finds the absolute path of the TubePress installation on the filesystem.
	 *
	 * @return string The absolute filesystem path of this TubePress installation.
     */
    public function getTubePressBaseInstallationPath()
    {
        return realpath(dirname(__FILE__) . '/../../../../../../');
    }
    
    /**
     * Find the directory name of the TubePress base installation.
     *
     * @return string The base name of the TubePress installation directory.
     */
    function getTubePressInstallationDirectoryBaseName()
    {
    	return basename($this->getTubePressBaseInstallationPath());
    }

    /**
     * Find the directories contained in the given directory (non-recursive).
     *
     * @param string $dir    The absolute filesystem path of the directory to examine.
     * @param string $prefix The logging prefix.
     *
     * @return array The names of the directories in the given directory (non-recursive).
     */
    public function getDirectoriesInDirectory($dir, $prefix)
    {
        $realDir = $dir;

        if (!is_dir($dir)) {
            org_tubepress_impl_log_Log::log($prefix, '<tt>%s</tt> is not a directory', $realDir);
            return array();
        }

        $toReturn = array();
        if ($handle = opendir($dir)) {
            org_tubepress_impl_log_Log::log($prefix, 'Successfully opened <tt>%s</tt> to read contents.', $realDir);
            while (($file = readdir($handle)) !== false) {

                if ($file === '.' || $file === '..' || strpos($file, ".") === 0) {
                    continue;
                }

                if (!is_dir($dir . '/' . $file)) {
                    continue;
                }

                array_push($toReturn, realpath($dir . '/' . $file));
            }
            closedir($handle);
        } else {
            org_tubepress_impl_log_Log::log($prefix, 'Could not open <tt>%s</tt>', $realDir);
        }
        return $toReturn;
    }

    /**
     * Find the files contained in the given directory (non-recursive).
     *
     * @param string $dir    The absolute filesystem path of the directory to examine.
     * @param string $prefix The logging prefix.
     *
     * @return array The names of the files in the given directory (non-recursive).
     */
    public function getFilenamesInDirectory($dir, $prefix)
    {
        $realDir = $dir;

        if (!is_dir($dir)) {
            org_tubepress_impl_log_Log::log($prefix, '<tt>%s</tt> is not a directory', $realDir);
            return array();
        }

        $toReturn = array();
        if ($handle = opendir($dir)) {
            org_tubepress_impl_log_Log::log($prefix, 'Successfully opened <tt>%s</tt> to read contents.', $realDir);
            while (($file = readdir($handle)) !== false) {

                if ($file === '.' || $file === '..') {
                    continue;
                }
                if (is_dir($dir . '/' . $file)) {
                    continue;
                }

                array_push($toReturn, realpath($dir . '/' . $file));
            }
            closedir($handle);
        } else {
            org_tubepress_impl_log_Log::log($prefix, 'Could not open <tt>%s</tt>', $realDir);
        }
        return $toReturn;
    }

    /**
     * Attempt to get temporary directory.
     *
     * @return string The absolute path of a temporary directory, preferably the system directory.
     */
    public function getSystemTempDirectory()
    {
        if (function_exists('sys_get_temp_dir')) {
            return sys_get_temp_dir();
        }

        // Try to get from environment variable
        if (!empty($_ENV['TMP'])) {
            return realpath($_ENV['TMP']);
        } else if (!empty($_ENV['TMPDIR'])) {
            return realpath($_ENV['TMPDIR']);
        } else if (!empty($_ENV['TEMP'])) {
            return realpath($_ENV['TEMP']);
        } else {
            // Detect by creating a temporary file
            // Try to use system's temporary directory
            // as random name shouldn't exist
            $tempfile = @tempnam(md5(uniqid(rand(), true)), '');
            if ( $tempfile ) {
                $tempdir = realpath(dirname($tempfile));
                @unlink($tempfile);
                return $tempdir;
            } else {
                return false;
            }
        }
    }
}
