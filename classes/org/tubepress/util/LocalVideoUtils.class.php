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
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array(
    'org_tubepress_log_Log',
    'org_tubepress_options_manager_OptionsManager'
));

/**
 * Utilities for dealing with local/uploaded video galleries.
 *
 */
class org_tubepress_util_LocalVideoUtils
{
    /**
     * Finds potential videos in the given directory.
     *
     * @param dir    string                An absolute path to the directory to search
     * @param log    org_tubepress_log_Log A log to write to
     * @param prefix string                Logging prefix
     * 
     * @return array An array of absolute paths to potential videos in this directory.
     */
    public static function findVideos($dir, org_tubepress_log_Log $log, $prefix)
    {    
        $filenames = org_tubepress_util_FilesystemUtils::getFilenamesInDirectory($dir, $log, $prefix);

        $result = org_tubepress_util_LocalVideoUtils::_findVideos($filenames, $log, $prefix);

        $log->log($prefix, 'Found %d potential video(s) in %s.', sizeof($result), $dir);

        return $result;
    }
    
    /**
     * Determines if the given file could potentially be a video.
     *
     * @param absPathToFile string                The absolute path of the file to check.
     * @param log           org_tubepress_log_Log A log to write to
     * @param prefix        string                Logging prefix
     *
     * @return boolean TRUE if the file could be a video, FALSE otherwise.
     */     
    public static function isPossibleVideo($absPathToFile, $log, $prefix)
    {
        /* if it's not a file, it's definitely not a video */
        if (!is_file($absPathToFile)) {
            return FALSE;    
        }

        /* get the file size */
        $lstat = lstat($absPathToFile);
        $size = $lstat['size'];    

        /* somewhat safe assumption that if a file is under 10K than it's not a video */
        if ($size < 10240) {
            $this->_log->log($this->_logPrefix, '%s is smaller than 10K', $absPathToFile);
            return FALSE;    
        }

        $log->log($prefix, '%s is %d bytes in size', $absPathToFile, $size);

        return TRUE;    
    }
    
    public static function getGalleryName($filename, org_tubepress_options_manager_OptionsManager $tpom, org_tubepress_log_Log $log, $prefix)
    {
        /* chop off the filename bit */
        $topDir = dirname($filename);

        /* calculate video uploads base directory */
        $baseDir = org_tubepress_util_LocalVideoUtils::getBaseVideoDirectory($tpom, $log, $prefix);

        /* now remove the base directory from the full path */
        $topDir = str_replace($baseDir, '', $topDir);

        /* finally, chop off the leading '/' */
        return org_tubepress_util_StringUtils::replaceFirst('/', '', $topDir);
    }
    
    public static function getBaseVideoDirectory(org_tubepress_options_manager_OptionsManager $tpom, org_tubepress_log_Log $log, $prefix)
    {
        /* first see what the user has set as their uploads base directory */
        $raw = $tpom->get(org_tubepress_options_category_Uploads::VIDEO_UPLOADS_BASE_DIRECTORY);
        $log->log($prefix, 'User-defined base upload directory value is "%s"', $raw);

        /* if they don't specify one, just use TubePress's base path + /uploads */
        if ($baseDir == '') {
            $baseDir = realpath(dirname(__FILE__) . '/../../../../uploads');
            $log->log($prefix, 'No user-defined base upload directory specified, so using "%s"', $baseDir);
        } else {
            $baseDir = realpath($raw);    
            $log->log($prefix, 'Sanitized path of user-defined base upload directory is "%s"', $baseDir);    
        }
        return $baseDir;
    }
    
    private static function _findVideos($files, $log, $prefix)
    {
        $toReturn = array();
        
        foreach ($files as $file) {
            if (org_tubepress_util_LocalVideoUtils::isPossibleVideo($file, $log, $prefix)) {
                $log->log($prefix, '%s looks like it could be a video.', $file);    
                array_push($toReturn, $file);
            }
        }
        return $toReturn;
    }    
}
