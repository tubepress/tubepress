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
 * Some filesystem utilities
 *
 */
class org_tubepress_util_LocalVideoUtils
{
	public static function findVideos($dir, org_tubepress_log_Log $log, $prefix)
	{	
		$filenames = org_tubepress_util_FilesystemUtils::getFilenamesInDirectory($dir, $log, $prefix);
    	$result = org_tubepress_util_LocalVideoUtils::_findVideos($filenames, $log, $prefix);
    	$log->log($prefix, 'Found %d potential video(s) in %s.', sizeof($result), $dir);
    	return $result;
	}
	
	public static function isPossibleVideo($absPathToFile, $log, $prefix)
    {
    	if (!is_file($absPathToFile)) {
    	    return FALSE;	
    	}
    	$lstat = lstat($absPathToFile);
    	$size = $lstat['size'];	
    	if ($size < 10240) {
    		$this->_log->log($this->_logPrefix, '%s is smaller than 10K', $absPathToFile);
    		return FALSE;	
    	}
    	$log->log($prefix, '%s is %d bytes in size', $absPathToFile, $size);
        return TRUE;	
    }
    
    public static function getGalleryName($filename, org_tubepress_options_manager_OptionsManager $tpom, org_tubepress_log_Log $log, $prefix)
    {
        $topDir = dirname($filename);
        $baseDir = org_tubepress_util_LocalVideoUtils::getBaseVideoDirectory($tpom, $log, $prefix);
        return org_tubepress_util_StringUtils::replaceFirst('/', '', str_replace($baseDir, '', $topDir));
    }
    
    public static function getBaseVideoDirectory(org_tubepress_options_manager_OptionsManager $tpom, org_tubepress_log_Log $log, $prefix)
    {
    	$raw = $tpom->get(org_tubepress_options_category_Uploads::VIDEO_UPLOADS_BASE_DIRECTORY);
    	$log->log($prefix, 'Raw base directory value is %s', $raw);
        if ($baseDir == '') {
        	$baseDir = realpath(dirname(__FILE__) . '/../../../../');
        	$log->log($prefix, 'No base directory specified, so using %s', $baseDir);
        } else {
            $baseDir = realpath($raw);	
            $log->log($prefix, 'Real path of base directory is %s', $baseDir);	
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
