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
tubepress_load_classes(array('org_tubepress_video_factory_VideoFactory',
    'org_tubepress_video_Video',
    'net_php_pear_Net_URL2'));

/**
 * Video factory for Vimeo
 */
class org_tubepress_video_factory_VimeoVideoFactory implements org_tubepress_video_factory_VideoFactory
{
    private $_log;
    private $_logPrefix;
    private $_tpom;
    
    public function __construct()
    {
        $this->_logPrefix = 'Vimeo Video Factory';
    }
    
    public function feedToVideoArray($rawFeed, $limit)
    {
        $results = array();
        $feed = unserialize($rawFeed);
        
        $this->_log->log($this->_logPrefix, 'Now parsing video(s)');

        $entries = $feed->videos->video;
        
        $index = 0;
        foreach ($entries as $entry) {
            
            if ($index > 0 && $index++ >= $limit) {
                $this->_log->log($this->_logPrefix, 'Reached limit of %d videos', $limit);
                break;
            }
            
            $results[] = $this->_createVideo($entry);
        }
        
        $this->_log->log($this->_logPrefix, 'Built %d video(s) from Vimeo\'s feed', sizeof($results));
        return $results;
    }
    
    public function convertSingleVideo($feed)
    {
       
    }

    /**
     * Creates a video from a single "entry" XML node
     *
     * @return org_tubepress_video_Video The org_tubepress_video_Video representation of this node
     */
    private function _createVideo($entry)
    {
        $vid = new org_tubepress_video_Video();

        $vid->setTitle($entry->title);
        $vid->setId($entry->id);
        $vid->setDescription($entry->description);
        $vid->setTimePublished($entry->upload_date);
        $vid->setViewCount($entry->number_of_plays);
        $vid->setDuration(org_tubepress_video_factory_VimeoVideoFactory::_seconds2HumanTime($entry->duration));
        $vid->setAuthor($entry->owner->display_name);
        $vid->setThumbnailUrl($entry->thumbnails->thumbnail[0]->_content);

        
        if (isset($entry->tags) && is_array($entry->tags->tag)) {
            $tags = array();
        
            foreach ($entry->tags->tag as $tag) {
                $tags[] = $tag->_content;
            }
            $vid->setKeywords($tags);
        }
        return $vid;
    }
    
    public function setLog(org_tubepress_log_Log $log) { $this->_log = $log; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom) { $this->_tpom = $tpom; }

    private static function _seconds2HumanTime($length_seconds)
    {
        $seconds         = $length_seconds;
        $length          = intval($seconds / 60);
        $leftOverSeconds = $seconds % 60;
        if ($leftOverSeconds < 10) {
            $leftOverSeconds = "0" . $leftOverSeconds;
        }
        $length .= ":" . $leftOverSeconds;
        return $length;
    }
    
}
