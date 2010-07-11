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
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array(
    'org_tubepress_video_feed_inspection_FeedInspectionService',
    'org_tubepress_util_FilesystemUtils'
));

/**
 * Examines the feed a directory
 *
 */
class org_tubepress_video_feed_inspection_LocalFeedInspectionService implements org_tubepress_video_feed_inspection_FeedInspectionService
{   
	private $_log;
    private $_logPrefix;
    private $_tpom;
	
	public function __construct()
    {
        $this->_logPrefix = 'Local Feed Inspection';   
    }
	
    public function getTotalResultCount($dir)
    {
    	return sizeof(org_tubepress_util_FilesystemUtils::findVideos($this->_assembleBaseDir() . '/' . $dir,
    	    $this->_log, $this->_logPrefix));
    }
    
    public function getQueryResultCount($dir)
    {
	    return $this->getTotalResultCount($dir);
    }
    
    private function _assembleBaseDir()
    {
    	$raw = $this->_tpom->get(org_tubepress_options_category_Uploads::VIDEO_UPLOADS_BASE_DIRECTORY);
    	$this->_log->log($this->_logPrefix, 'Raw base directory value is %s', $raw);
        if ($baseDir == '') {
        	$baseDir = realpath(dirname(__FILE__) . '/../../../../../../');
        	$this->_log->log($this->_logPrefix, 'No base directory specified, so using %s', $baseDir);
        } else {
            $baseDir = realpath($raw);	
            $this->_log->log($this->_logPrefix, 'Real path of base directory is %s', $baseDir);	
        }
        return $baseDir;
    }
    
    public function setLog(org_tubepress_log_Log $log) { $this->_log = $log; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom) { $this->_tpom = $tpom; }
}