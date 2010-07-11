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
    'org_tubepress_log_Log'
));

/**
 * Some filesystem utilities
 *
 */
class org_tubepress_util_FilesystemUtils
{
	public static function findVideos($dir, org_tubepress_log_Log $log, $prefix)
	{	
		$filenames = org_tubepress_util_FilesystemUtils::getFilenamesInDirectory($dir, $log, $prefix);
    	$result = org_tubepress_util_FilesystemUtils::_findVideos($filenames, $log, $prefix);
    	$log->log($prefix, 'Found %d potential video(s) in %s.', sizeof($result), $dir);
    	return $result;
	}
	
	public static function getFilenamesInDirectory($dir, org_tubepress_log_Log $log, $prefix)
	{
		$log->log($prefix, 'Getting ready to examine directory at %s', $dir);
    	
    	if (!is_dir($dir)) {
    		$log->log($prefix, '%s is not a directory', $dir);
    		return array();	
    	}
    	if (!is_readable($dir)) {
    		$log->log($prefix, '%s is not a readable directory', $dir);
    		return array();	
    	}
    	
    	$toReturn = array();
        if ($handle = opendir($dir)) {
        	$log->log($prefix, 'Successfully opened %s to read contents.', $dir);	
	        while (($file = readdir($handle)) !== false) {
	            array_push($toReturn, $dir . '/' . $file);	      
	        }
	        closedir($handle);
	    } else {
	        $log->log($prefix, 'Could not open %s', $dir);	
	    }
	    return $toReturn;
	}
	
    private static function _findVideos($files, $log, $prefix)
    {
    	$toReturn = array();
        
        foreach ($files as $file) {
	        if (org_tubepress_util_FilesystemUtils::_isPossibleVideo($file, $log, $prefix)) {
	            $log->log($prefix, '%s looks like it could be a video.', $file);	
        		array_push($toReturn, $file);
	        }
        }
        return $toReturn;
    }
    
    private static function _isPossibleVideo($absPathToFile, $log, $prefix)
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
    
}
