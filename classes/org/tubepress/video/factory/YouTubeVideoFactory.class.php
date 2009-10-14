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
 * Video factory for YouTube
 */
class org_tubepress_video_factory_YouTubeVideoFactory implements org_tubepress_video_factory_VideoFactory
{
    /* shorthands for the namespaces */
    const NS_APP   = 'http://www.w3.org/2007/app';
    const NS_ATOM  = 'http://www.w3.org/2005/Atom';
    const NS_MEDIA = 'http://search.yahoo.com/mrss/';
    const NS_YT    = 'http://gdata.youtube.com/schemas/2007';
    const NS_GD    = 'http://schemas.google.com/g/2005';
    
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
        $results = array();
	$doc = new DOMDocument();
	if ($doc->loadXML($feed) === FALSE) {
            throw new Exception("Could not parse XML from YouTube");
	}

        $xpath = $this->_buildXPath($doc);
        $entries = $xpath->query('/atom:feed/atom:entry');

        /* create a org_tubepress_video_Video out of each "entry" node */
        for ($j = 0; $j < min($limit, $entries->length); $j++) {
            $results[] = $this->_createVideo($xpath, $entries->item($j));
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
    private function _createVideo(DOMXPath $doc, DOMNode $entry)
    {
        $vid = new org_tubepress_video_Video();
        
        /* see if the video is actually available, not just a stub */
        if ($this->_videoNotAvailable($doc, $entry)) {
            $vid->setDisplayable(false);
            return $vid;
        }
        $vid->setDisplayable(true);

        /* everyone loves the builder pattern */
        $vid->setAuthor($this->_getAuthor($doc, $entry));
        $vid->setCategory($this->_getCategory($doc, $entry));
        $vid->setDescription($this->_getDescription($doc, $entry));
        $vid->setId($this->_getId($doc, $entry));
        $vid->setLength($this->_getRuntime($doc, $entry));
        $vid->setRating($this->_getRatingAverage($doc, $entry));
        $vid->setRatings($this->_getRatingCount($doc, $entry));
        $vid->setTags($this->_getTags($doc, $entry));
        $vid->setThumbUrls($this->_getThumbUrls($doc, $entry));
        $vid->setTitle($this->_getTitle($doc, $entry));
        $vid->setUploadTime($this->_getUploadTime($doc, $entry));
        $vid->setViews($this->_getViewCount($doc, $entry));
        $vid->setYouTubeUrl($this->_getURL($doc, $entry));
        return $vid;
    }

    /**
     * Gets the YouTube author from the XML
     *
     * @param DOMElement $rss The "entry" XML element
     * 
     * @return string The YouTube author from the XML
     */
    private function _getAuthor(DOMXPath $doc, DOMNode $entry)
    {
        return $doc->query('atom:author/atom:name', $entry)->item(0)->nodeValue;
    }
    
    /**
     * Gets the YouTube category from the XML
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The YouTube category from the XML
     */
    private function _getCategory(DOMXPath $doc, DOMNode $entry)
    {
        return trim($doc->query('media:group/media:category', $entry)->item(0)->getAttribute('label'));
    }
    
    /**
     * Gets the video's description
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The video's description
     */
    private function _getDescription(DOMXPath $doc, DOMNode $entry)
    {
        return trim($doc->query('media:group/media:description', $entry)->item(0)->nodeValue);
    }
    
    /**
     * Gets the video's ID from XML
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The video's ID from XML
     */
    private function _getId(DOMXPath $doc, DOMNode $entry)
    { 
        return $doc->query('media:group/yt:videoid', $entry)->item(0)->nodeValue;
    }
    
    /**
     * Gets the average rating of the video
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The average rating of the video
     */
    private function _getRatingAverage(DOMXPath $doc, DOMNode $entry)
    { 
        $count = $doc->query('gd:rating', $entry)->item(0);
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
    private function _getRatingCount(DOMXPath $doc, DOMNode $entry)
    { 
        $count = $doc->query('gd:rating', $entry)->item(0);
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
    private function _getRuntime(DOMXPath $doc, DOMNode $entry)
    {
        $duration = $doc->query('media:group/yt:duration', $entry)->item(0);
        return org_tubepress_video_factory_YouTubeVideoFactory::_seconds2HumanTime($duration->getAttribute('seconds'));
    }
    
    /**
     * Gets the tags of this video (space separated)
     *
     * @param DOMElement $rss The "entry" XML element
     * 
     * @return string The tags of this video (space separated)
     */
    private function _getTags(DOMXPath $doc, DOMNode $entry)
    { 
        $rawKeywords = $doc->query('media:group/media:keywords')->item(0);
        return split(", ", trim($rawKeywords->nodeValue));
    }
    
    /**
     * Gets this video's thumbnail URLs
     *
     * @param DOMElement $rss The "entry" XML element
     * 
     * @return array An array of this video's thumbnail URLs
     */
    private function _getThumbUrls(DOMXPath $doc, DOMNode $entry)
    {
        $results = array();
        $thumbs  = $doc->query('media:group/media:thumbnail', $entry);
        for ($x = 0; $x < $thumbs->length; $x++) {
            $url = $thumbs->item($x)->getAttribute('url');
            if (strpos($url, 'hqdefault') === FALSE) {
               array_push($results, $url);
            }
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
    private function _getTitle(DOMXPath $doc, DOMNode $entry)
    { 
        $title = $doc->query('atom:title', $entry)->item(0)->nodeValue;
        return htmlspecialchars($title, ENT_QUOTES);
    }
    
    /**
     * Get this video's upload timestamp
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string This video's upload timestamp
     */
    private function _getUploadTime(DOMXPath $doc, DOMNode $entry)
    { 
        $publishedNode = $doc->query('atom:published', $entry);
        if ($publishedNode->length == 0) {
            return "N/A";
        }
        $views = $publishedNode->item(0);
        return org_tubepress_video_factory_YouTubeVideoFactory::_rfc3339toHumanTime($views->nodeValue);
    }
    
    /**
     * Get this video's YouTube URL
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string This video's YouTube URL
     */
    private function _getURL(DOMXPath $doc, DOMNode $entry)
    { 
        return $doc->query("atom:link[@rel='alternate']", $entry)->item(0)->getAttribute('href');
    }
    
    /**
     * Get the number of times this video has been viewed
     * 
     * @param DOMElement $rss The "entry" XML element
     *
     * @return string The number of times this video has been viewed
     */
    private function _getViewCount(DOMXPath $doc, DOMNode $entry) 
    { 
        $stats = $doc->query('yt:statistics', $entry)->item(0);
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
    
    private function _videoNotAvailable(DOMXPath $doc, DOMNode $entry)
    {
        $states = $doc->query("app:control/yt:state", $entry);

        /* no state applied? we're good to go */
        if ($states->length == 0) {
            return false;
        }	

        /* if state is other than limitedSyndication, it's not available */
        return $doc->query("app:control/yt:state[@reasonCode='limitedSyndication']", $entry)->length == 0;
    }

    private function _buildXPath(DOMDocument $doc)
    {
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('atom',  org_tubepress_video_factory_YouTubeVideoFactory::NS_ATOM);
        $xpath->registerNamespace('yt',    org_tubepress_video_factory_YouTubeVideoFactory::NS_YT);
        $xpath->registerNamespace('gd',    org_tubepress_video_factory_YouTubeVideoFactory::NS_GD);
        $xpath->registerNamespace('media', org_tubepress_video_factory_YouTubeVideoFactory::NS_MEDIA);
        $xpath->registerNamespace('app',   org_tubepress_video_factory_YouTubeVideoFactory::NS_APP);
        return $xpath;
    }

}
