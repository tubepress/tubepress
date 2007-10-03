<?php
/**
 * TubePressVideo.php
 * 
 * This class represents a video pulled from YouTube. It's really
 * just a glorified wrapper for an associated array.
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class TubePressVideo
{
    private $_rawArray;
    private $author;
    private $category;
    private $description;
    private $id;
    private $rating;
    private $ratings;
    private $runtime;
    private $tags;
    private $url;
    private $views;
    
    /**
     * Simple constructor
     *
     * @param unknown_type $videoXML
     * @param unknown_type $options
     * @return TubePressVideo
     */
    public function TubePressVideo($videoXML, $options = "")
    {   
        if (!is_array($videoXML)) {
            
            $vidRequest = TubePressXML::generateVideoRequest($videoXML);
            if (PEAR::isError($vidRequest)) {
            	return;
            }
            $videoXML = TubePressXML::fetch($vidRequest, $options);
            if (PEAR::isError($videoXML)) {
            	return;
            }
            $videoXML = TubePressXML::toArray($videoXML);
            
        }
        $this->_rawArray = (array) $videoXML;
    }

  	/**
     * The video's author
     */
    public function getAuthor() {
        return $this->quickGet('author');
    }

    /**
     * Which category this video is in
     */
    public function getCategory() { return $this->quickGet('category'); }

    public function getDefaultThumbURL() {
        return $this->getSpecificThumbURL(0);;
    }
    
    /**
     * Returns the video's textual description
     */
    public function getDescription() { return $this->quickGet('description'); }
    
    /**
     * This video's YouTube ID
     */
    public function getId() { return $this->quickGet('id'); }
    
    /**
     * The URL to this video's thumbnail. Typically there are
     * multiple to choose from.
     */
    public function getRandomThumbURL()
    {   
        $random = rand(0, count($this->_videoXML['media:group']['media:thumbnail']) - 1);
        return $this->getSpecificThumbURL($random);
    }
    
    /**
     * The average rating for this video. I HATE this method!!
     */
    public function getRatingAverage() { return $this->quickGet('rating'); }
    
    /**
     * How many people have rated the video. I hate this function
     * even more than the previous.
     */
    public function getRatingCount() { return $this->quickGet('ratings'); }
    
    /**
     * The video's runtime in minutes and seconds
     */
    public function getRuntime() { return $this->quickGet('runtime'); }
    
    /**
     * Gets a space-separated list of tags for this video
     */
    public function getTags() { return $this->quickGet('tags'); }
    
    /**
     * The video's title
     */
    public function getTitle() { return $this->quickGet('title'); }
    
    /**
     * When was this video uploaded?
     */
    public function getUploadTime() { return $this->quickGet('uploaded'); }
    
    /**
     * The URL to this video on YouTube.com
     */
    public function getURL() { return $this->quickGet('url'); }
    
    /**
     * Returns the view count
     */
    public function getViewCount() { return $this->quickGet('views'); }

    /*
     * -----------------------------------------------------------------------
     * PRIVATE METHODS -------------------------------------------------------
     * -----------------------------------------------------------------------
     */
    
    private function _getAuthor() {
        return $this->_videoXML['author']['name'];
    }
    
    private function _getCategory() {
        $keywords = array();
        foreach ($this->_videoXML['category'] as $cat) {
            if (substr_count($cat['scheme'], "categories.cat") == 1) {
                return $cat['label'];
            }
        }
        return "";
    }
    
    private function _getDescription() {
        $this->_videoXML['media:group']['media:description']['_content'];
    }
    
    private function _getId() {
        $url = $this->_videoXML['media:group']['media:player']['url'];
        $pos = strrpos($url, "=");
        return substr($url, $pos + 1);
    }
    
    private function _getRatings() {
        $crappyHTML = $this->_videoXML['content']['_content'];
 
    	$first = strpos($crappyHTML, '<div style="font-size: 11px;">');
    	$last = strpos($crappyHTML, '>', $first);
    	
    	$ratingString = substr($crappyHTML, $last + 1, strpos($crappyHTML, '<', $last + 1) - $last - 1);
        return number_format($ratingString);
    }
    
    private function _getRuntime() {
        return TubePressVideo::_seconds2HumanTime($this->_videoXML['media:group']['yt:duration']['seconds']);
    }
    
    private function _getTags() {
        $keywords = array();
        foreach ($this->_videoXML['category'] as $cat) {
            if (substr_count($cat['scheme'], "keywords.cat") == 1) {
                array_push($keywords, $cat['term']);
            }
        }
       return implode(" ", $keywords);
    }
    
    private function _getTitle() {
        return htmlspecialchars($this->_videoXML['title']['_content'], ENT_QUOTES);
    }
    
    private function _getUploaded() {
        return TubePressVideo::rfc3339_2_humanTime($this->_videoXML['published']);
    }
    
    private function _getUrl() {
        foreach ($this->_videoXML['link'] as $link) {
            if (!is_array($link)) {
                continue;
            }
            if ($link['rel'] == "alternate") {
                return $link['href'];
            }
        }
    	return "";
    }
    
    private function _getViews() {
        return number_format($this->_videoXML['yt:statistics']['viewCount']);
    }
    
    private function getSpecificThumbURL(int $which) {
        $this->_videoXML['media:group']['media:thumbnail'][$which]['url'];
    }


    /**
     * Converts gdata timestamps to human readable
     * 
     * @param length_seconds The runtime of a video, in seconds
     */
    private static function rfc3339_2_humanTime($rfc3339)
    {
        $tmp = str_replace( "T", " ", $rfc3339);
        $tmp = ereg_replace("(\.[0-9]{1,})?", "", $tmp);

        $datetime = substr($tmp, 0, 19);  // Grab the datetime part of the string
        $timezone = str_replace(":", "", substr($tmp, 19, 6)); // Grab the timezone, (-/+0000) PHP 4 doesn't support the colon
        return date("M d, Y, h:i A", strtotime($datetime . " " . $timezone));
    }

    /**
     * Converts seconds to minutes and seconds
     * 
     * @param length_seconds The runtime of a video, in seconds
     */
    private static function seconds2HumanTime($length_seconds)
    {
        $seconds = $length_seconds;
        $length = intval($seconds / 60);
        $leftOverSeconds = $seconds % 60;
        if ($leftOverSeconds < 10) {
            $leftOverSeconds = "0" . $leftOverSeconds;
        }
        $length .=     ":" . $leftOverSeconds;
        return $length;
    }
    
    private function quickGet($member) {
        if (!isset($this->$member)) {
            $this->$member = call_user_func(array($this, '_get' . ucwords($member)));
        }
        return $this->$member;
    }
}
?>
