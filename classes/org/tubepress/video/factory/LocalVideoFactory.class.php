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
tubepress_load_classes(array('org_tubepress_video_factory_AbstractVideoFactory',
    'org_tubepress_video_Video',
    'org_tubepress_options_category_Display'));

/**
 * Video factory for uploads
 */
class org_tubepress_video_factory_LocalVideoFactory extends org_tubepress_video_factory_AbstractVideoFactory
{
    private $_logPrefix;
    
    public function __construct()
    {
        $this->_logPrefix = 'Local Video Factory';
    }
    
    public function feedToVideoArray($dir, $limit)
    {
        $videoNames = org_tubepress_util_FilesystemUtils::findVideos($this->_assembleBaseDir() . '/' . $dir,
        	$this->getLog(), $this->_logPrefix);

        $toReturn = array();
		$index = 0;
        
        foreach ($videoNames as $filename) {
        
        	$basename = basename($filename);
        
            if ($this->isVideoBlackListed($basename)) {
	                $this->getLog()->log($this->_logPrefix, 'Video with ID %s is blacklisted. Skipping it.', $entry->id);
	                continue;
	            }
	            
	            if ($index > 0 && $index++ >= $limit) {
	                $this->getLog()->log($this->_logPrefix, 'Reached limit of %d videos', $limit);
	                break;
	            }
            
            	$toReturn[] = $this->_createVideo($filename);
        	
        }
        
        return $toReturn;
    }
    
    public function convertSingleVideo($rawFeed)
    {
    
    }
    
    private function _createVideo($filename)
    {
    	$this->getLog()->log($this->_logPrefix, 'Assembling video for %s', $filename);
    	$video = new org_tubepress_video_Video();
    	$video->setId(md5($filename));
    	$video->setThumbnailUrl($this->_getThumbnailUrl($filename));
    	
    	return $video;	
    }
    
    private function _getThumbnailUrl($filename)
    {
    	global $tubepress_base_url;
    	
    	$thumbname = basename(substr($filename, 0, strlen($filename) - 4));
    	$thumbname = preg_replace('/[^a-zA-Z0-9]/', '', $thumbname);
    	
    	$height = $this->getOptionsManager()->get(org_tubepress_options_category_Display::THUMB_HEIGHT);
    	$width = $this->getOptionsManager()->get(org_tubepress_options_category_Display::THUMB_WIDTH);
    	
    	$postfix = "uploads/generated_thumbnails/$thumbname" . "_thumb_$height" . 'x' . "$width.jpg";
    	
    	$basePath = dirname(__FILE__) . '/../../../../../';
    	if (!is_readable($basePath . $postfix)) {
    		return "$tubepress_base_url/ui/gallery/missing_thumb.png";
    	}
    	
    	return "$tubepress_base_url/$postfix";
    }
    
    private function _assembleBaseDir()
    {
    	$raw = $this->getOptionsManager()->get(org_tubepress_options_category_Uploads::VIDEO_UPLOADS_BASE_DIRECTORY);
    	$this->getLog()->log($this->_logPrefix, 'Raw base directory value is %s', $raw);
        if ($baseDir == '') {
        	$baseDir = realpath(dirname(__FILE__) . '/../../../../../');
        	$this->getLog()->log($this->_logPrefix, 'No base directory specified, so using %s', $baseDir);
        } else {
            $baseDir = realpath($raw);	
            $this->getLog()->log($this->_logPrefix, 'Real path of base directory is %s', $baseDir);	
        }
        return $baseDir;
    }
}