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

    private $domElement;
    private $mediaGroup;
    private $processedAlready = array();
    
    const author = "author";
    const category =  "category";
    const description = "description";
    const id = "id";
    const rating = "rating";
    const ratings = "ratings";
    const runtime = "runtime";
    const tags = "tags";
    const title = "title";
    const uploaded = "uploaded";
    const url = "url";
    const views = "views";
    
    const ns_media = 'http://search.yahoo.com/mrss/';
    const ns_yt = 'http://gdata.youtube.com/schemas/2007';
    const ns_gd = 'http://schemas.google.com/g/2005';
    
    /**
     * Simple constructor
     *
     * @param unknown_type $videoXML
     * @param unknown_type $options
     * @return TubePressVideo
     */
    public function TubePressVideo($rss, $options = "")
    {   
        if (!($rss instanceof DOMElement)) {
            
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
        $this->domElement = $rss;
        $this->mediaGroup = $this->domElement->getElementsByTagNameNS(TubePressVideo::ns_media,'group')->item(0);
    }

    public function getAuthor() { return $this->quickGet(TubePressVideo::author); }
    public function getCategory() { return $this->quickGet(TubePressVideo::category); }
    public function getDefaultThumbURL() { return $this->getSpecificThumbURL(0);}
    public function getDescription() { return $this->quickGet(TubePressVideo::description); }
    public function getId() { return $this->quickGet(TubePressVideo::id); }
    public function getRandomThumbURL()
    {   
        $thumbs = $this->mediaGroup->getElementsByTagNameNS(TubePressVideo::ns_media, 'thumbnail');
        $random = rand(0, count($thumbs->length - 2));
        return $thumbs->item($random)->getAttribute('url');
    }
    public function getRatingAverage() { return $this->quickGet(TubePressVideo::rating); }
    public function getRatingCount() { return $this->quickGet(TubePressVideo::ratings); }
    public function getRuntime() { return $this->quickGet(TubePressVideo::runtime); }
    public function getTags() { return $this->quickGet(TubePressVideo::tags); }
    public function getTitle() { return $this->quickGet(TubePressVideo::title); }
    public function getUploadTime() { return $this->quickGet(TubePressVideo::uploaded); }
    public function getURL() { return $this->quickGet(TubePressVideo::url); }
    public function getViewCount() { return $this->quickGet(TubePressVideo::views); }

    /*
     * -----------------------------------------------------------------------
     * PRIVATE METHODS -------------------------------------------------------
     * -----------------------------------------------------------------------
     */
    
    private function _getAuthor() {
        $authorNode = $this->domElement->getElementsByTagName('author')->item(0);
        return $authorNode->getElementsByTagName('name')->item(0)->nodeValue;
    }
    
    private function _getCategory() {
        return $this->domElement->getElementsByTagNameNS(TubePressVideo::ns_media, 'category')->item(0)->nodeValue;
    	
    }
    
    private function _getDescription() {
    	return $this->mediaGroup->getElementsByTagNameNS(TubePressVideo::ns_media, 'description')->item(0)->nodeValue;
    }
    
    private function _getId() {
    	$player = $this->domElement->getElementsByTagNameNS(TubePressVideo::ns_media, 'player')->item(0);
        $url = $player->getAttribute('url');
        $pos = strrpos($url, "=");
        return substr($url, $pos + 1);
    }
    
    private function _getRating() {
        $count = $this->domElement->getElementsByTagNameNS(TubePressVideo::ns_gd, 'rating')->item(0);
		if ($count != NULL) {
        	return $count->getAttribute('average');
		}
		return "N/A";
    }
    
    private function _getRatings() {
    	$count = $this->domElement->getElementsByTagNameNS(TubePressVideo::ns_gd, 'rating')->item(0);
    	if ($count != NULL) {
        	return $count->getAttribute('numRaters');
		}
        return "0";
    }
    
    private function _getRuntime() {
    	$duration = $this->mediaGroup->getElementsByTagNameNS(TubePressVideo::ns_yt, 'duration')->item(0);
        return TubePressVideo::seconds2HumanTime($duration->getAttribute('seconds'));
    }
    
    private function _getTags() {
        $rawKeywords = $this->mediaGroup->getElementsByTagNameNS(TubePressVideo::ns_media, 'keywords')->item(0);
		return str_replace(',', '', $rawKeywords->nodeValue);
    }
    
    private function _getTitle() {
    	$title = $this->domElement->getElementsByTagName('title')->item(0)->nodeValue;
        return htmlspecialchars($title, ENT_QUOTES);
    }
    
    private function _getUploaded() {
    	$views = $this->domElement->getElementsByTagName('published')->item(0);
        return TubePressVideo::rfc3339_2_humanTime($views->nodeValue);
    }
    
    private function _getUrl() {
    	$links = $this->mediaGroup->getElementsByTagName('link');
    	for ($x = 0; $x < $links->length; $x++) {
    		$link = $links->item($x);
    		if ($link->getAttribute('rel') != 'alternate') {
    			continue;
    		}
    		return $link->getAttribute('href');
    	}
    }
    
    private function _getViews() {
    	$stats = $this->domElement->getElementsByTagNameNS(TubePressVideo::ns_yt, 'statistics')->item(0);
        return number_format($stats->getAttribute('viewCount'));
    }
    
    private function getSpecificThumbURL($which) {
    	$thumbs = $this->mediaGroup->getElementsByTagNameNS(TubePressVideo::ns_media, 'thumbnail');
        return $thumbs->item($which)->getAttribute('url');
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
        return date("M d, Y", strtotime($datetime . " " . $timezone));
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
        if (!isset($this->processedAlready[$member])) {
            $this->processedAlready[$member] = call_user_func(array($this, '_get' . ucwords($member)));
        }
        return $this->processedAlready[$member];
    }
}
?>
