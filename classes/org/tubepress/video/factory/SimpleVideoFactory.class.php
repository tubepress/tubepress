<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_video_Video'));

/**
 * Simple implementation of org_tubepress_video_factory_VideoFactory
 */
class org_tubepress_video_factory_SimpleVideoFactory implements org_tubepress_video_factory_VideoFactory
{
    private $_mediaGroup;

    /* shorthands for the namespaces */
    const NS_MEDIA = 'http://search.yahoo.com/mrss/';
    const NS_YT    = 'http://gdata.youtube.com/schemas/2007';
    const NS_GD    = 'http://schemas.google.com/g/2005';
    
    /**
     * Main method
     *
     * @param DOMDocument $rss   The raw XML of what we got from YouTube
     * @param int         $limit The maximum size of the array to return
     * 
     * @return array An array of TubePressVideos (may be empty)
     */
    public function dom2TubePressVideoArray(DOMDocument $rss, $limit)
    {   
        $results = array();
        $entries = $rss->getElementsByTagName('entry');
        
        /* create a org_tubepress_video_Video out of each "entry" node */
        for ($j = 0; $j < min($limit, $entries->length); $j++) {
            $results[] = $this->_createVideo($entries->item($j));
        }
        return $results;
    }

    /**
     * Creates a video from a single "entry" XML node
     *
     * @param DOMNode $entry The "entry" XML node
     * 
     * @return org_tubepress_video_Video The org_tubepress_video_Video representation of this node
     */
    private function _createVideo(DOMNode $entry)
    {
        $this->_mediaGroup = 
            $entry->getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_MEDIA, 
                'group')->item(0);
            
        /* everyone loves the builder pattern */
        $vid = new org_tubepress_video_Video();
        $vid->setAuthor($this->_getAuthor($entry));
        $vid->setCategory($this->_getCategory($entry));
        $vid->setDescription($this->_getDescription($entry));
        $vid->setId($this->_getId($entry));
        $vid->setLength($this->_getRuntime($entry));
        $vid->setRating($this->_getRatingAverage($entry));
        $vid->setRatings($this->_getRatingCount($entry));
        $vid->setTags($this->_getTags($entry));
        $vid->setThumbUrls($this->_getThumbUrls($entry));
        $vid->setTitle($this->_getTitle($entry));
        $vid->setUploadTime($this->_getUploadTime($entry));
        $vid->setViews($this->_getViewCount($entry));
        $vid->setYouTubeUrl($this->_getURL($entry));
        return $vid;
    }

    /**
     * Gets the YouTube author from the XML
     *
     * @param DOMElement $rss The "entry" XML element
     * 
     * @return string The YouTube author from the XML
     */
    private function _getAuthor(DOMElement $rss)
    {
        $authorNode = $rss->getElementsByTagName('author')->item(0);
        return $authorNode->getElementsByTagName('name')->item(0)->nodeValue;
    }
    
    /**
     * Gets the YouTube category from the XML
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The YouTube category from the XML
     */
    private function _getCategory(DOMElement $rss)
    {
        return trim($rss->
            getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_MEDIA,
                'category')->item(0)->nodeValue);
    }
    
    /**
     * Gets the video's description
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The video's description
     */
    private function _getDescription(DOMElement $rss)
    {
        return trim($this->_mediaGroup->
            getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_MEDIA,
                'description')->item(0)->nodeValue);
    }
    
    /**
     * Gets the video's ID from XML
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The video's ID from XML
     */
    private function _getId(DOMElement $rss)
    { 
        $thumb = 
             $rss->getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_MEDIA,
                 "thumbnail")->item(0);
        $id    = $thumb->getAttribute("url");
        $id    = substr($id, 0, strrpos($id, "/"));
        $id    = substr($id, strrpos($id, "/") + 1);
        return $id;
    }
    
    /**
     * Gets the average rating of the video
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The average rating of the video
     */
    private function _getRatingAverage(DOMElement $rss)
    { 
        $count = $rss->getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_GD,
            'rating')->item(0);
        if ($count != null) {
            return $count->getAttribute('average');
        }
        return "N/A";
    }
    
    /**
     * Gets the number of times this video has been rated
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The number of times this video has been rated
     */
    private function _getRatingCount(DOMElement $rss)
    { 
        $count = $rss->getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_GD,
            'rating')->item(0);
        if ($count != null) {
            return number_format($count->getAttribute('numRaters'));
        }
        return "0";
    }
    
    /**
     * Gets the runtime of this video
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The runtime of this video
     */
    private function _getRuntime(DOMElement $rss)
    { 
        $duration = 
            $this->_mediaGroup->
                getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_YT,
                    'duration')->item(0);
        return 
            org_tubepress_video_factory_SimpleVideoFactory::
                _seconds2HumanTime($duration->getAttribute('seconds'));
    }
    
    /**
     * Gets the tags of this video (space separated)
     *
     * @param DOMElement $rss The "entry" XML element
     * 
     * @return string The tags of this video (space separated)
     */
    private function _getTags(DOMElement $rss)
    { 
        $rawKeywords = 
            $this->_mediaGroup->
                getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_MEDIA,
                    'keywords')->item(0);
        return split(", ", trim($rawKeywords->nodeValue));
    }
    
    /**
     * Gets this video's thumbnail URLs
     *
     * @param DOMElement $rss The "entry" XML element
     * 
     * @return array An array of this video's thumbnail URLs
     */
    private function _getThumbUrls(DOMElement $rss)
    {
        $results = array();
        $thumbs  = 
            $this->_mediaGroup->
                getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_MEDIA, 
                    'thumbnail');
        for ($x = 0; $x < $thumbs->length; $x++) {
            array_push($results, $thumbs->item($x)->getAttribute('url'));
        }
        return $results;
    }
    
    /**
     * Gets this video's title
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string Get this video's title
     */
    private function _getTitle(DOMElement $rss)
    { 
                $title = 
            $rss->getElementsByTagName('title')->item(0)->nodeValue;
        return htmlspecialchars($title, ENT_QUOTES);
    }
    
    /**
     * Get this video's upload timestamp
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string This video's upload timestamp
     */
    private function _getUploadTime(DOMElement $rss)
    { 
                $publishedNode = $rss->getElementsByTagName('published');
        if ($publishedNode->length == 0) {
            return "N/A";
        }
        $views = $publishedNode->item(0);
        return org_tubepress_video_factory_SimpleVideoFactory::_rfc3339toHumanTime($views->nodeValue);
    }
    
    /**
     * Get this video's YouTube URL
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string This video's YouTube URL
     */
    private function _getURL(DOMElement $rss)
    { 
            $links = $rss->getElementsByTagName('link');
        for ($x = 0; $x < $links->length; $x++) {
            $link = $links->item($x);
            if ($link->getAttribute('rel') != 'alternate') {
                continue;
            }
            return $link->getAttribute('href');
        }
    }
    
    /**
     * Get the number of times this video has been viewed
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The number of times this video has been viewed
     */
    private function _getViewCount(DOMElement $rss) 
    { 
        $stats = $rss->getElementsByTagNameNS(org_tubepress_video_factory_SimpleVideoFactory::NS_YT,
            'statistics')->item(0);
        if ($stats != null) {
            return number_format($stats->getAttribute('viewCount'));
        } else {
            return "N/A";
        }
    }
  
    /**
     * Converts gdata timestamps to human readable
     * 
     * @param string $rfc3339 The RFC 3339 format of time
     * 
     * @return string Human time format
     */
    private static function _rfc3339toHumanTime($rfc3339)
    {
        $tmp = str_replace("T", " ", $rfc3339);
        $tmp = ereg_replace("(\.[0-9]{1,})?", "", $tmp);

        $datetime = substr($tmp, 0, 19);
        $timezone = str_replace(":", "", substr($tmp, 19, 6));
        return strtotime($datetime . " " . $timezone);
    }

    /**
     * Converts seconds to minutes and seconds
     * 
     * @param string $length_seconds The runtime of a video, in seconds
     * 
     * @return string Human time format
     */
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
?>
