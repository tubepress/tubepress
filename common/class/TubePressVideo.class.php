<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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

/**
 * A video that TubePress can play around with
 */
class TubePressVideo
{

    private $_domElement;
    private $_mediaGroup;
    private $_processedAlready = array();

    const NS_MEDIA = 'http://search.yahoo.com/mrss/';
    const NS_YT    = 'http://gdata.youtube.com/schemas/2007';
    const NS_GD    = 'http://schemas.google.com/g/2005';
    
    /**
     * Constructor
     *
     * @param DOMElement   $rss     The raw XML of what we got from YouTube
     * @param unknown_type $options ?
     * 
     * @return TubePressVideo
     */
    public function TubePressVideo($rss, $options = "")
    {   
        $this->_domElement = $rss;
        $this->_mediaGroup = 
            $this->_domElement->getElementsByTagNameNS(TubePressVideo::NS_MEDIA, 
                'group')->item(0);
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getAuthor()
    {
        return $this->_quickGet("author");
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getCategory()
    {
        return $this->_quickGet("category");
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getDefaultThumbURL()
    {
        return $this->_getSpecificThumbURL(0);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getDescription()
    {
        return $this->_quickGet("description");
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getId()
    { 
        return $this->_quickGet("id");
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getRandomThumbURL()
    {   
        $thumbs = 
            $this->_mediaGroup->getElementsByTagNameNS(TubePressVideo::NS_MEDIA, 
                'thumbnail');
        $random = rand(0, count($thumbs->length - 2));
        return $thumbs->item($random)->getAttribute('url');
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getRatingAverage()
    { 
        return $this->_quickGet("rating"); 
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getRatingCount()
    { 
        return $this->_quickGet("ratings"); 
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getRuntime()
    { 
        return $this->_quickGet("runtime"); 
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getTags()
    { 
        return $this->_quickGet("tags"); 
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getTitle()
    { 
        return $this->_quickGet("title"); 
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getUploadTime()
    { 
        return $this->_quickGet("uploaded"); 
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getURL()
    { 
        return $this->_quickGet("url"); 
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getViewCount() 
    { 
        return $this->_quickGet("views"); 
    }

    /*
     * -----------------------------------------------------------------------
     * PRIVATE METHODS -------------------------------------------------------
     * -----------------------------------------------------------------------
     */
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getAuthor()
    {
        $authorNode = $this->_domElement->getElementsByTagName('author')->item(0);
        return $authorNode->getElementsByTagName('name')->item(0)->nodeValue;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getCategory() 
    {
        return $this->_domElement->getElementsByTagNameNS(TubePressVideo::NS_MEDIA,
            'category')->item(0)->nodeValue;
        
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getDescription() 
    {
        return $this->_mediaGroup->getElementsByTagNameNS(TubePressVideo::NS_MEDIA,
            'description')->item(0)->nodeValue;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getId()
    {
        $player = 
            $this->_domElement->getElementsByTagNameNS(TubePressVideo::NS_MEDIA,
                'player')->item(0);
        $url    = $player->getAttribute('url');
        $pos    = strrpos($url, "=");
        return substr($url, $pos + 1);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getRating()
    {
        $count = $this->_domElement->getElementsByTagNameNS(TubePressVideo::NS_GD,
            'rating')->item(0);
        if ($count != null) {
            return $count->getAttribute('average');
        }
        return "N/A";
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getRatings() 
    {
        $count = $this->_domElement->getElementsByTagNameNS(TubePressVideo::NS_GD,
            'rating')->item(0);
        if ($count != null) {
            return number_format($count->getAttribute('numRaters'));
        }
        return "0";
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getRuntime() 
    {
        $duration = 
            $this->_mediaGroup->getElementsByTagNameNS(TubePressVideo::NS_YT,
                'duration')->item(0);
        return 
            TubePressVideo::_seconds2HumanTime($duration->getAttribute('seconds'));
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getTags() 
    {
        $rawKeywords = 
            $this->_mediaGroup->getElementsByTagNameNS(TubePressVideo::NS_MEDIA,
                'keywords')->item(0);
        return str_replace(',', '', $rawKeywords->nodeValue);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getTitle() 
    {
        $title = 
            $this->_domElement->getElementsByTagName('title')->item(0)->nodeValue;
        return htmlspecialchars($title, ENT_QUOTES);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getUploaded() 
    {
        $publishedNode = $this->_domElement->getElementsByTagName('published');
        if ($publishedNode->length == 0) {
            return "N/A";
        }
        $views = $publishedNode->item(0);
        return TubePressVideo::_rfc3339toHumanTime($views->nodeValue);
        
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getUrl() 
    {
        $links = $this->_mediaGroup->getElementsByTagName('link');
        for ($x = 0; $x < $links->length; $x++) {
            $link = $links->item($x);
            if ($link->getAttribute('rel') != 'alternate') {
                continue;
            }
            return $link->getAttribute('href');
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getViews()
    {
        $stats = $this->_domElement->getElementsByTagNameNS(TubePressVideo::NS_YT,
            'statistics')->item(0);
        if ($stats != null) {
            return number_format($stats->getAttribute('viewCount'));
        } else {
            return "N/A";
        }
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $which ?
     * 
     * @return unknown
     */
    private function _getSpecificThumbURL($which)
    {
        $thumbs = 
            $this->_mediaGroup->getElementsByTagNameNS(TubePressVideo::NS_MEDIA,
                'thumbnail');
        return $thumbs->item($which)->getAttribute('url');
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
    
    /**
     * Enter description here...
     *
     * @param unknown_type $member ?
     * 
     * @return unknown
     */
    private function _quickGet($member) 
    {
        if (!isset($this->_processedAlready[$member])) {
            $this->_processedAlready[$member] = 
                call_user_func(array($this, '_get' . ucwords($member)));
        }
        return $this->_processedAlready[$member];
    }
}
?>
