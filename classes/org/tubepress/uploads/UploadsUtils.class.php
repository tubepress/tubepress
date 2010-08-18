<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../tubepress_classloader.php';
tubepress_load_classes(array(
    'org_tubepress_log_Log',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_util_FilesystemUtils'
));

/**
 * Utilities for dealing with local/uploaded video galleries.
 *
 */
class org_tubepress_uploads_UploadsUtils
{
    public static function getGalleryNameFromVideoId($videoId)
    {
        $lastSlashIndex = strrpos($videoId, '/');
        if ($lastSlashIndex === false) {
            return '';
        }
        $trailingPortion = substr($videoId, $lastSlashIndex);
        return str_replace($trailingPortion, '', $videoId);
    }

    /**
     * Finds potential videos in the given directory.
     *
     * @param string $dir    An absolute path to the directory to search
     * @param string $prefix Logging prefix
     * 
     * @return array An array of absolute paths to potential videos in this directory.
     */
    public static function findVideos($dir, $prefix)
    {
        $filenames = org_tubepress_util_FilesystemUtils::getFilenamesInDirectory($dir, $prefix);

        $result = self::_findVideos($filenames, $prefix);

        org_tubepress_log_Log::log($prefix, 'Found %d potential video(s) in <tt>%s</tt>.', sizeof($result), $dir);

        return $result;
    }

    /**
     * Get the absolute path of the video uploads directory.
     *
     * @return string The absolute path of the video uploads directory.
     */
    public static function getBaseVideoDirectory()
    {
        return org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath() . '/content/uploads';
    }

    /**
     * Determines if the given file could potentially be a video.
     *
     * @param string $absPathToFile The absolute path of the file to check.
     * @param string $prefix        Logging prefix
     *
     * @return boolean TRUE if the file could be a video, FALSE otherwise.
     */
    public static function isPossibleVideo($absPathToFile, $prefix)
    {
        /* if it's not a file, it's definitely not a video */
        if (!is_file($absPathToFile)) {
            return false;
        }

        /* get the file size */
        $lstat = lstat($absPathToFile);
        $size  = $lstat['size'];

        /* somewhat safe assumption that if a file is under 10K than it's not a video */
        if ($size < 10240) {
            org_tubepress_log_Log::log($prefix, '<tt>%s</tt> is smaller than 10K', $absPathToFile);
            return false;
        }

        org_tubepress_log_Log::log($prefix, '<tt>%s</tt> is %s bytes in size', $absPathToFile, number_format($size));

        return true;
    }

    private static function _findVideos($files, $prefix)
    {
        $toReturn = array();

        foreach ($files as $file) {
            if (self::isPossibleVideo($file, $prefix)) {
                org_tubepress_log_Log::log($prefix, '<tt>%s</tt> looks like it could be a video.', $file);
                array_push($toReturn, $file);
            }
        }
        return $toReturn;
    }
}
