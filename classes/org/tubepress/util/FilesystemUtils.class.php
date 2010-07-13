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
	
	public static function getFilenamesInDirectory($dir, $prefix)
	{
		$realDir = realpath($dir);
		org_tubepress_log_Log::log($prefix, 'Getting ready to examine directory at %s', $realDir);
    	    	
    	if (!is_dir($dir)) {
    		org_tubepress_log_Log::log($prefix, '%s is not a directory', $realDir);
    		return array();	
    	}
    	if (!is_readable($dir)) {
    		org_tubepress_log_Log::log($prefix, '%s is not a readable directory', $realDir);
    		return array();	
    	}
    	
    	$toReturn = array();
        if ($handle = opendir($dir)) {
        	org_tubepress_log_Log::log($prefix, 'Successfully opened %s to read contents.', $realDir);	
	        while (($file = readdir($handle)) !== false) {
	            array_push($toReturn, $dir . '/' . $file);	      
	        }
	        closedir($handle);
	    } else {
	        org_tubepress_log_Log::log($prefix, 'Could not open %s', $realDir);	
	    }
	    return $toReturn;
	}    
}
