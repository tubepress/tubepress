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
 * Video factory for Vimeo
 */
class org_tubepress_video_factory_VimeoVideoFactory extends org_tubepress_video_factory_AbstractVideoFactory
{
    private $_logPrefix;
    
    public function __construct()
    {
        $this->_logPrefix = 'Vimeo Video Factory';
    }
    
    public function feedToVideoArray($rawFeed, $limit)
    {
        $feed = unserialize($rawFeed);
        
        $this->getLog()->log($this->_logPrefix, 'Now parsing video(s)');

        $entries = $feed->videos->video;
        
        return $this->_buildVideos($entries);
    }
    
    public function convertSingleVideo($rawFeed)
    {
        $feed = unserialize($rawFeed);
        return $this->_buildVideos($feed->video);
    }

    private function _buildVideos($entries)
    {
        $results = array();
        $index = 0;
        
        if (is_array($entries) && sizeof($entries) > 0) {
	        foreach ($entries as $entry) {
	            
	            if ($this->isVideoBlackListed($entry->id)) {
	                $this->getLog()->log($this->_logPrefix, 'Video with ID %s is blacklisted. Skipping it.', $entry->id);
	                continue;
	            }
	            
	            if ($index > 0 && $index++ >= $limit) {
	                $this->getLog()->log($this->_logPrefix, 'Reached limit of %d videos', $limit);
	                break;
	            }
            
            	$results[] = $this->_createVideo($entry);
        	}
        }
        
        $this->getLog()->log($this->_logPrefix, 'Built %d video(s) from Vimeo\'s feed', sizeof($results));
        return $results;
    }
    
    /**
     * Creates a video from a single "entry" XML node
     *
     * @return org_tubepress_video_Video The org_tubepress_video_Video representation of this node
     */
    private function _createVideo($entry)
    {
        $vid = new org_tubepress_video_Video();

        $vid->setAuthorDisplayName($entry->owner->display_name);
        $vid->setAuthorUid($entry->owner->username);
        $vid->setDescription($this->_getDescription($entry));
        $vid->setDuration(self::_seconds2HumanTime($entry->duration));
        $vid->setHomeUrl('http://vimeo.com/' . $entry->id);
        $vid->setId($entry->id); 
        $vid->setThumbnailUrl($this->_getThumbnailUrl($entry));
        $vid->setTimePublished($this->_getTimePublished($entry));
        $vid->setTitle($entry->title);
        $vid->setViewCount($this->_getViewCount($entry));
        $vid->setLikesCount($entry->number_of_likes);
        
        if (isset($entry->tags) && is_array($entry->tags->tag)) {
            $tags = array();
        
            foreach ($entry->tags->tag as $tag) {
                $tags[] = $tag->_content;
            }
            $vid->setKeywords($tags);
        } else {
            $vid->setKeywords(array());
        }
        return $vid;
    }
    
    protected function _getDescription($entry)
    {
        $limit = $this->getOptionsManager()->get(org_tubepress_options_category_Display::DESC_LIMIT);
        $desc = $entry->description;
        if ($limit > 0 && strlen($desc) > $limit) {
            $desc = substr($desc, 0, $limit) . '...';
        }
        return $desc;
    }
    
    protected function _getThumbnailUrl($entry)
    {
        return $entry->thumbnails->thumbnail[0]->_content;
    }
    
    private function _getTimePublished($entry)
    { 
        $date = $entry->upload_date;
        $seconds = strtotime($date);
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Display::RELATIVE_DATES)) {
            return $this->_relativeTime($seconds);
        }
        return date($this->getOptionsManager()->get(org_tubepress_options_category_Advanced::DATEFORMAT), $seconds);
    }
    
    private function _getViewCount($entry)
    {
        return number_format($entry->number_of_plays);
    }
}
