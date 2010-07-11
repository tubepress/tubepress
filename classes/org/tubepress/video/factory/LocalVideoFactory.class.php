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
    'org_tubepress_options_category_Display',
    'org_tubepress_util_LocalVideoUtils',
    'org_tubepress_util_FilesystemUtils',
    'com_googlecode_spyc_Spyc'));

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
    	$baseDir = org_tubepress_util_LocalVideoUtils::getBaseVideoDirectory($this->getOptionsManager(), $this->getLog(), $this->_logPrefix);
        $videoNames = org_tubepress_util_LocalVideoUtils::findVideos("$baseDir/$dir", $this->getLog(), $this->_logPrefix);

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
    	
    	$infoArray = $this->_getInfoArray($filename);
    	$video->setTitle($infoArray['title']);
    	$video->setDescription($infoArray['description']);
    	$video->setTimePublished($infoArray['uploaded']);
    	$video->setAuthorDisplayName($infoArray['author']);
    	
    	$keywords = $infoArray['tags'];
    	$tags = explode(',', $keywords);
        $video->setKeywords($tags);
    	return $video;	
    }
    
    private function _getInfoArray($filename)
    {
    	$yamlFile = basename(substr($filename, 0, strlen($filename) - 4)) . '.yml';
    	$basePath = dirname(__FILE__) . '/../../../../../';
    	$galleryName = org_tubepress_util_LocalVideoUtils::getGalleryName($filename, 
    	    $this->getOptionsManager(), $this->getLog(), $this->_logPrefix);
    	$yamlFile = "$basePath/uploads/$galleryName/$yamlFile";
    	
    	$realYamlPath = realpath($yamlFile);
    	if (!is_readable($yamlFile)) {
    		$this->getLog()->log($this->_logPrefix, '%s does not exist.', $realYamlPath);
    	    return array();
    	}
    	$this->getLog()->log($this->_logPrefix, 'Loading YML file at %s.', $realYamlPath);
    	$contents = file_get_contents($yamlFile);
    	$this->getLog()->log($this->_logPrefix, 'YML file at %s has the following contents: %s', $realYamlPath, $contents);
    	$result = com_googlecode_spyc_Spyc::YAMLLoadString($contents);
    	$this->getLog()->log($this->_logPrefix, 'YML file at %s was parsed to %s.', $realYamlPath, var_export($result, TRUE));
    	return $result;
    }
    
    private function _getThumbnailUrl($filename)
    {
    	global $tubepress_base_url;
    	
    	$thumbname = basename(substr($filename, 0, strlen($filename) - 4));
    	$thumbname = preg_replace('/[^a-zA-Z0-9]/', '', $thumbname);
    	
    	$height = $this->getOptionsManager()->get(org_tubepress_options_category_Display::THUMB_HEIGHT);
    	$width = $this->getOptionsManager()->get(org_tubepress_options_category_Display::THUMB_WIDTH);
    	
    	$galleryName = org_tubepress_util_LocalVideoUtils::getGalleryName($filename, 
    	    $this->getOptionsManager(), $this->getLog(), $this->_logPrefix);
    	
    	$postfix = $thumbname . "_thumb_$height" . 'x' . "$width_";
    	$basePath = dirname(__FILE__) . '/../../../../../';
    	
    	$thumbs = $this->_getExistingThumbs($basePath, "uploads/$galleryName/generated_thumbnails/", $postfix);
    	
    	if (sizeof($thumbs) === 0) {
    		$this->getLog()->log($this->_logPrefix, 'No potential thumbs for %s. Using filler thumbnail.', $filename);
    		return "$tubepress_base_url/ui/gallery/missing_thumb.png";
    	}
    	
    	$prefix = "$tubepress_base_url/uploads/$galleryName/generated_thumbnails/";
    	
    	if ($this->getOptionsManager()->get(org_tubepress_options_category_Display::RANDOM_THUMBS)) {
    		$this->getLog()->log($this->_logPrefix, 'Using a random thumbnail for %s.', $filename);
    	    return $prefix . $thumbs[array_rand($thumbs)];
    	}
    	
    	return $prefix . $thumbs[0];
    }
    
    private function _getExistingThumbs($basePath, $relativePart, $postfix)
    {
        $toReturn = array();
        
        $files = org_tubepress_util_FilesystemUtils::getFilenamesInDirectory($basePath . '/' . $relativePart, 
            $this->getLog(), $this->_logPrefix);
            
        foreach ($files as $file) {
        	if (strpos($file, $postfix) !== FALSE) {
        		$this->getLog()->log($this->_logPrefix, 'Found a thumbnail we can use at %s', realpath($file));
        	    array_push($toReturn, basename($file));	
        	}
        }
        
        return $toReturn;	
    }
}