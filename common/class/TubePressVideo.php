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
    /* the raw xml about this video */
    private $_videoXML;
    private $_cachedThumbURL;

    function TubePressVideo($videoXML, $options = "")
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
        $this->_videoXML = $videoXML;
    }

	/**
     * The video's author
     */
    function getAuthor()
    {
    	return $this->_videoXML['author']['name'];
    }
    
    /**
     * Which category this video is in
     */
    function getCategory() {
    	$keywords = array();
        foreach ($this->_videoXML['category'] as $cat) {
            if (substr_count($cat['scheme'], "categories.cat") == 1) {
               return $cat['label'];
            }
        }
       return "";
    }
    
    /**
     * This video's YouTube ID
     */
    function getId()
    {
        $url = $this->_videoXML['media:group']['media:player']['url'];
        $pos = strrpos($url, "=");
        return substr($url, $pos + 1);
    }
    
    /**
     * The video's title
     */
    function getTitle()
    {
        return htmlspecialchars($this->_videoXML['title']['_content'], ENT_QUOTES);
    }
    
    /**
     * The video's runtime in minutes and seconds
     */
    function getRuntime()
    {
    	return TubePressVideo::_seconds2HumanTime($this->_videoXML['media:group']['yt:duration']['seconds']);
    }
    
    /**
     * The average rating for this video. I HATE this method!!
     */
    function getRatingAverage()
    {
    	$crappyHTML = $this->_videoXML['content']['_content'];
    	$fullStars = substr_count($crappyHTML, "http://gdata.youtube.com/static/images/icn_star_full_11x11.gif");
    	$halfStars = substr_count($crappyHTML, "http://gdata.youtube.com/static/images/icn_star_half_11x11.gif");
        return $fullStars + (0.5 * $halfStars);
    }
    
    /**
     * How many people have rated the video. I hate this function
     * even more than the previous.
     */
    function getRatingCount()
    {
    	$crappyHTML = $this->_videoXML['content']['_content'];
    	
    	$first = strpos($crappyHTML, '<div style="font-size: 11px;">');
    	$last = strpos($crappyHTML, '>', $first);
    	
    	$ratingString = substr($crappyHTML, $last + 1, strpos($crappyHTML, '<', $last + 1) - $last - 1);
        return number_format($ratingString);
    }
    
    /**
     * Returns the video's textual description
     */
    function getDescription()
    {
        return $this->_videoXML['media:group']['media:description']['_content'];
    }
    
    /**
     * Returns the view count
     */
    function getViewCount()
    {
    	return number_format($this->_videoXML['yt:statistics']['viewCount']);
    }
    
    /**
     * When was this video uploaded?
     */
    function getUploadTime()
    {
    	return TubePressVideo::_RFC3339_2_humanTime($this->_videoXML['published']);
    }
    
    /**
     * How many comments?
     */
    //function getCommentCount()
    //{
    //	return "";
    //}

    /**
     * Gets a space-separated list of tags for this video
     */
    function getTags()
    {
        $keywords = array();
        foreach ($this->_videoXML['category'] as $cat) {
            if (substr_count($cat['scheme'], "keywords.cat") == 1) {
                array_push($keywords, $cat['term']);
            }
        }
       return implode(" ", $keywords);
    }
    
    /**
     * The URL to this video on YouTube.com
     */
    function getURL()
    {
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
    
    /**
     * The URL to this video's thumbnail. Typically there are
     * multiple to choose from.
     */
    function getThumbURL($which = -1)
    {   
        if (($which == -1)
            || ($which < 0)
            || ($which >= count($this->_videoXML['media:group']['media:thumbnail']))) {
            if ($this->_cachedThumbURL == "") {
                $random = rand(0, count($this->_videoXML['media:group']['media:thumbnail']) - 1);
                $this->_cachedThumbURL = $this->_videoXML['media:group']['media:thumbnail'][$random]['url'];
            }
            return $this->_cachedThumbURL;
        }

        return $this->_videoXML['media:group']['media:thumbnail'][$which]['url'];
    }
    
    


    /**
     * Converts gdata timestamps to human readable
     * 
     * @param length_seconds The runtime of a video, in seconds
     */
    function _RFC3339_2_humanTime($rfc3339)
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
    function _seconds2HumanTime($length_seconds)
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
}
?>
