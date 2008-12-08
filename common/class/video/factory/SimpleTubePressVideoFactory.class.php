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
 * Simple implementation of TubePressVideoFactory
 */
class SimpleTubePressVideoFactory implements TubePressVideoFactory
{
    private $_mediaGroup;

    const NS_MEDIA = 'http://search.yahoo.com/mrss/';
    const NS_YT    = 'http://gdata.youtube.com/schemas/2007';
    const NS_GD    = 'http://schemas.google.com/g/2005';
    
    /**
     * Main method
     *
     * @param DOMElement   $rss     The raw XML of what we got from YouTube
     * 
     * @return TubePressVideo
     */
    public function generate(DOMElement $rss)
    {   
        $this->_mediaGroup = 
            $rss->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_MEDIA, 
                'group')->item(0);
            
        $vid = new TubePressVideo();
        $vid->setAuthor(		$this->getAuthor($rss));
		$vid->setCategory(		$this->getCategory($rss));
		$vid->setThumbUrls(		$this->getThumbUrls($rss));
		$vid->setDescription(	$this->getDescription($rss));
		$vid->setId(			$this->getId($rss));
		$vid->setRating(		$this->getRatingAverage($rss));
		$vid->setRatings(		$this->getRatingCount($rss));
		$vid->setLength(		$this->getRuntime($rss));
		$vid->setTags(			$this->getTags($rss));
		$vid->setTitle(			$this->getTitle($rss));
		$vid->setUploadTime(	$this->getUploadTime($rss));
		$vid->setYouTubeUrl(	$this->getURL($rss));
		$vid->setViews(			$this->getViewCount($rss));

		return $vid;
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function getAuthor(DOMElement $rss)
    {
        $authorNode = $rss->getElementsByTagName('author')->item(0);
        return $authorNode->getElementsByTagName('name')->item(0)->nodeValue;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function getCategory(DOMElement $rss)
    {
        return trim($rss->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_MEDIA,
            'category')->item(0)->nodeValue);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function getDescription(DOMElement $rss)
    {
        return trim($this->_mediaGroup->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_MEDIA,
            'description')->item(0)->nodeValue);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function getId(DOMElement $rss)
    { 
        $thumb = 
             $rss->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_MEDIA,
                 "thumbnail")->item(0);
        $id = $thumb->getAttribute("url");
        $id = substr($id, 0, strrpos($id, "/"));
        $id = substr($id, strrpos($id, "/") + 1);
        return $id;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function getRatingAverage(DOMElement $rss)
    { 
        $count = $rss->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_GD,
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
    private function getRatingCount(DOMElement $rss)
    { 
        $count = $rss->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_GD,
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
    private function getRuntime(DOMElement $rss)
    { 
        $duration = 
            $this->_mediaGroup->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_YT,
                'duration')->item(0);
        return 
            SimpleTubePressVideoFactory::_seconds2HumanTime($duration->getAttribute('seconds'));
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function getTags(DOMElement $rss)
    { 
        $rawKeywords = 
            $this->_mediaGroup->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_MEDIA,
                'keywords')->item(0);
        return split(", ", trim($rawKeywords->nodeValue));
    }
    
    private function getThumbUrls(DOMElement $rss)
    {
    	$results = array();
    	$thumbs = 
            $this->_mediaGroup->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_MEDIA, 
                'thumbnail');
    	for ($x = 0; $x < $thumbs->length; $x++) {
    		array_push($results, $thumbs->item($x)->getAttribute('url'));
    	}
    	return $results;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function getTitle(DOMElement $rss)
    { 
                $title = 
            $rss->getElementsByTagName('title')->item(0)->nodeValue;
        return htmlspecialchars($title, ENT_QUOTES);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function getUploadTime(DOMElement $rss)
    { 
                $publishedNode = $rss->getElementsByTagName('published');
        if ($publishedNode->length == 0) {
            return "N/A";
        }
        $views = $publishedNode->item(0);
        return SimpleTubePressVideoFactory::_rfc3339toHumanTime($views->nodeValue);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function getURL(DOMElement $rss)
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
     * Enter description here...
     *
     * @return unknown
     */
    private function getViewCount(DOMElement $rss) 
    { 
        $stats = $rss->getElementsByTagNameNS(SimpleTubePressVideoFactory::NS_YT,
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
