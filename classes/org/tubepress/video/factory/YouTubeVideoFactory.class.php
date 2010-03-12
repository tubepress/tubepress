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
    'net_php_pear_Net_URL2'));

/**
 * Video factory for YouTube
 */
class org_tubepress_video_factory_YouTubeVideoFactory extends org_tubepress_video_factory_AbstractVideoFactory
{
    /* shorthands for the namespaces */
    const NS_APP   = 'http://www.w3.org/2007/app';
    const NS_ATOM  = 'http://www.w3.org/2005/Atom';
    const NS_MEDIA = 'http://search.yahoo.com/mrss/';
    const NS_YT    = 'http://gdata.youtube.com/schemas/2007';
    const NS_GD    = 'http://schemas.google.com/g/2005';
    
    private $_logPrefix;
    
    private $_xpath;
    private $_currentNode;
    
    public function __construct()
    {
        $this->_logPrefix = 'YouTube Video Factory';
    }
    
    protected function getXpath() { return $this->_xpath; }
    protected function getCurrentNode() { return $this->_currentNode; }
    
    /**
     * Converts raw video feeds to TubePress videos
     *
     * @param unknown    $rss   The raw feed result from the video provider
     * @param int        $limit The max number of videos to return
     * 
     * @return array an array of TubePress videos generated from the feed
     */
    public function feedToVideoArray($feed, $limit)
    {
        $this->_xpath = $this->_createXPath($this->_createDomDocument($feed));
        return $this->_buildVideos($limit, '/atom:feed/atom:entry');
    }
    
    public function convertSingleVideo($feed)
    {
        $this->_xpath = $this->_createXPath($this->_createDomDocument($feed));
        return $this->_buildVideos(1, '/atom:entry');
    }

    private function _createXPath(DOMDocument $doc)
    {
        $this->getLog()->log($this->_logPrefix, 'Building xpath to parse XML');
        
        if (!class_exists('DOMXPath')) {
            throw new Exception('Class DOMXPath not found');
        }
        
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('atom',  org_tubepress_video_factory_YouTubeVideoFactory::NS_ATOM);
        $xpath->registerNamespace('yt',    org_tubepress_video_factory_YouTubeVideoFactory::NS_YT);
        $xpath->registerNamespace('gd',    org_tubepress_video_factory_YouTubeVideoFactory::NS_GD);
        $xpath->registerNamespace('media', org_tubepress_video_factory_YouTubeVideoFactory::NS_MEDIA);
        $xpath->registerNamespace('app',   org_tubepress_video_factory_YouTubeVideoFactory::NS_APP);
        return $xpath;
    }
    
    private function _createDomDocument($feed)
    {
        $this->getLog()->log($this->_logPrefix, 'Attempting to load XML from YouTube');
        
        if (!class_exists('DOMDocument')) {
            throw new Exception('DOMDocument class not found');
        }
        
        $doc = new DOMDocument();
        if ($doc->loadXML($feed) === FALSE) {
            throw new Exception('Could not parse XML from YouTube');
        }
        $this->getLog()->log($this->_logPrefix, 'Successfully loaded XML from YouTube');
        return $doc;
    }
    
    private function _buildVideos($limit, $entryXpath)
    {
        $results = array();
        
        $this->getLog()->log($this->_logPrefix, 'Now parsing video(s). Limit is %d.', $limit);
        $entries = $this->_xpath->query($entryXpath);  

        $index = 0;
        foreach ($entries as $entry) {
            
            $this->_currentNode = $entry;
            
            if ($this->_videoNotAvailable()) {
                $this->getLog()->log($this->_logPrefix, 'Video not available. Skipping it.');
                continue;
            }
            
            if ($this->isVideoBlackListed($this->_getId())) {
                $this->getLog()->log($this->_logPrefix, 'Video with ID %s is blacklisted. Skipping it.', $this->_getId());
                continue;
            }
            
            if ($index > 0 && $index >= $limit) {
                $this->getLog()->log($this->_logPrefix, 'Reached limit of %d videos', $limit);
                break;
            }
            $index++;
            
            $results[] = $this->_createVideo();
        }
        
        $this->getLog()->log($this->_logPrefix, 'Built %d video(s) from YouTube\'s XML', sizeof($results));
        return $results;
    }
    
    /**
     * Creates a video from a single "entry" XML node
     *
     * @return org_tubepress_video_Video The org_tubepress_video_Video representation of this node
     */
    private function _createVideo()
    {
        $vid = new org_tubepress_video_Video();

        /* these three properties must always be present */
        $vid->setId($this->_getId());
        $vid->setTitle($this->_getTitle());
        $vid->setThumbnailUrl($this->_getThumbnailUrl());
        
        /* the rest of these are optional */
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::AUTHOR)) {
            $uid = $this->_getAuthorUid();
            $vid->setAuthorUid($uid);
            $vid->setAuthorDisplayName($uid);
            
        }
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::CATEGORY)) {
            $vid->setCategory($this->_getCategory());
        }
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::DESCRIPTION)) {
            $vid->setDescription($this->_getDescription());
        }
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::LENGTH)) {
            $vid->setDuration($this->_getDuration());
        }
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::URL)) {
            $vid->setHomeUrl($this->_getHomeUrl());
        }
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::TAGS)) {
            $vid->setKeywords($this->_getKeywords());
        }
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::RATING)) {
            $vid->setRatingAverage($this->_getRatingAverage());
        }
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::RATINGS)) {
            $vid->setRatingCount($this->_getRatingCount());
        }
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::UPLOADED)) {
            $vid->setTimePublished($this->_getTimePublished());
        }
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Meta::VIEWS)) {
            $vid->setViewCount($this->_getViewCount());
        }
        
        return $vid;
    }
    
    private function _getAuthorUid()
    {
        return $this->_xpath->query('atom:author/atom:name', $this->_currentNode)->item(0)->nodeValue;
    }

    private function _getCategory()
    {
        return trim($this->_xpath->query('media:group/media:category', $this->_currentNode)->item(0)->getAttribute('label'));
    }
    
    private function _getDescription()
    {
        $limit = $this->getOptionsManager()->get(org_tubepress_options_category_Display::DESC_LIMIT);
        $desc = trim($this->_xpath->query('media:group/media:description', $this->_currentNode)->item(0)->nodeValue);
        if ($limit > 0 && strlen($desc) > $limit) {
            $desc = substr($desc, 0, $limit) . '...';
        }
        return $desc;
    }
    
    
    /**
     * Gets the runtime of this video
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The runtime of this video
     */
    private function _getDuration()
    {
        $duration = $this->_xpath->query('media:group/yt:duration', $this->_currentNode)->item(0);
        return org_tubepress_video_factory_YouTubeVideoFactory::_seconds2HumanTime($duration->getAttribute('seconds'));
    }
    
    private function _getHomeUrl()
    {
        $rawUrl = $this->_xpath->query("atom:link[@rel='alternate']", $this->_currentNode)->item(0)->getAttribute('href');
        $url = new net_php_pear_Net_URL2($rawUrl);
        return $url->getURL(true);
    }
    
    /**
     * This is a bullshit function because YouTube is stupid as f*ck about how they present their video IDs
     * 
    */
    private function _getId()
    {
        $link = $this->_xpath->query("atom:link[@type='text/html']", $this->_currentNode)->item(0);
        $matches = array();
        preg_match('/.*v=(.{11}).*/', $link->getAttribute('href'), $matches);
        return $matches[1];
    }
    
    /**
     * Gets the tags of this video (space separated)
     *
     * @param DOMElement $rss The "entry" XML element
     * 
     * @return string The tags of this video (space separated)
     */
    private function _getKeywords()
    {
        $rawKeywords = $this->_xpath->query('media:group/media:keywords', $this->_currentNode)->item(0);
        $raw = trim($rawKeywords->nodeValue);
        return split(", ", $raw);
    }
    
    /**
     * Gets the average rating of the video
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The average rating of the video
     */
    private function _getRatingAverage()
    { 
        $count = $this->_xpath->query('gd:rating', $this->_currentNode)->item(0);
        if ($count != null) {
            return number_format($count->getAttribute('average'), 2);
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
    private function _getRatingCount()
    { 
        $count = $this->_xpath->query('gd:rating', $this->_currentNode)->item(0);
        if ($count != null) {
            return number_format($count->getAttribute('numRaters'));
        }
        return "0";
    }
    
    protected function _getThumbnailUrl()
    {
        $thumbs  = $this->_xpath->query('media:group/media:thumbnail', $this->_currentNode);
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Display::RANDOM_THUMBS)) {
            do {
                $node = $thumbs->item(rand(0, $thumbs->length - 1));
            } while (strpos($node->getAttribute('url'), 'hqdefault') !== FALSE);
            return $node->getAttribute('url');
        }
        $node = $thumbs->item(0);
        return $node->getAttribute('url');
    }
    
    /**
     * Get this video's upload timestamp
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string This video's upload timestamp
     */
    private function _getTimePublished()
    { 
        $publishedNode = $this->_xpath->query('atom:published', $this->_currentNode);
        if ($publishedNode->length == 0) {
            return "N/A";
        }
        $rawTime = $publishedNode->item(0)->nodeValue;
        $seconds = org_tubepress_video_factory_YouTubeVideoFactory::_rfc3339toHumanTime($rawTime);
        
        if ($this->getOptionsManager()->get(org_tubepress_options_category_Display::RELATIVE_DATES)) {
            return $this->_relativeTime($seconds);
        }
        return date($this->getOptionsManager()->get(org_tubepress_options_category_Advanced::DATEFORMAT), $seconds);
    }
    
    /**
     * Gets this video's title
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string Get this video's title
     */
    private function _getTitle()
    { 
        return $this->_xpath->query('atom:title', $this->_currentNode)->item(0)->nodeValue;
    }
    
    /**
     * Get the number of times this video has been viewed
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The number of times this video has been viewed
     */
    private function _getViewCount() 
    { 
        $stats = $this->_xpath->query('yt:statistics', $this->_currentNode)->item(0);
        if ($stats != null) {
            return number_format($stats->getAttribute('viewCount'));
        } else {
            return "N/A";
        }
    }
  
    private function _videoNotAvailable()
    {
        $states = $this->_xpath->query("app:control/yt:state", $this->_currentNode);

        /* no state applied? we're good to go */
        if ($states->length == 0) {
            return false;
        }    

        /* if state is other than limitedSyndication, it's not available */
        return $this->_xpath->query("app:control/yt:state[@reasonCode='limitedSyndication']", $this->_currentNode)->length == 0;
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
}
