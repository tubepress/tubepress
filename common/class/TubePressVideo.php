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

function_exists("_tpMsg")
    || require(dirname(__FILE__) . "/../messages.php");
defined("TP_VID_TITLE")
    || require(dirname(__FILE__) . "/../defines.php");

class TubePressVideo
{
    /* the raw xml about this video */
    var $_videoXML;
    var $_cachedThumbURL;

    function getAuthor()
    {
    	return $this->_videoXML['author']['name'];
    }
    
    function getCategory() {
    	$keywords = array();
        foreach ($this->_videoXML['category'] as $cat) {
            if (substr_count($cat['scheme'], "categories.cat") == 1) {
               return $cat['label'];
            }
        }
       return "";
    }
    
    function getId()
    {
        $pos = strrpos($this->_videoXML['id'], "/");
        return substr($this->_videoXML['id'], $pos + 1);
    }
    
    function getTitle()
    {
        return htmlspecialchars($this->_videoXML['title']['_content'], ENT_QUOTES);
    }
    
    function getRuntime()
    {
    	return TubePressVideo::_seconds2HumanTime($this->_videoXML['media:group']['yt:duration']['seconds']);
    }
    
    function getRatingAverage()
    {
    	return "";
    }
    
    function getRatingCount()
    {
    	return "";
    }
    
    function getDescription()
    {
        return $this->_videoXML['media:group']['media:description']['_content'];
    }
    
    function getViewCount()
    {
    	return number_format($this->_videoXML['yt:statistics']['viewCount']);
    }
    
    function getUploadTime()
    {
    	return TubePressVideo::_RFC3339_2_humanTime($this->_videoXML['published']);
    }
    
    function getCommentCount()
    {
    	return "";
    }

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
    
    function getThumbURL($which = -1)
    {   
        if (($which == -1)
            || ($which < 0)
            || ($which > count($this->_videoXML['media:group']['media:thumbnail']) -1)) {
            if ($this->_cachedThumbURL == "") {
                $random = rand(0, count($this->_videoXML['media:group']['media:thumbnail']) - 1);
                $this->_cachedThumbURL = $this->_videoXML['media:group']['media:thumbnail'][$random]['url'];
            }
            return $this->_cachedThumbURL;
        }
        
        
        return $this->_videoXML['media:group']['media:thumbnail'][$which]['url'];
    }
    
    
    function TubePressVideo($videoXML)
    {   
        if (!is_array($videoXML)) {
            return;
        }
        $this->_videoXML = $videoXML;
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
