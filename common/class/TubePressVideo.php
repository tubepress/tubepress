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
    /* an array of meta values about this video */
    var $_vid;

    function getAuthor()
    {
    	return $this->_vid['author'];
    }
    
    function getId()
    {
    	return $this->_vid['id'];
    }
    
    function getTitle()
    {
    	return $this->_vid['title'];
    }
    
    function getRuntime()
    {
    	return $this->_vid['runtime'];
    }
    
    function getRatingAverage()
    {
    	return $this->_vid['rating_avg'];
    }
    
    function getRatingCount()
    {
    	return $this->_vid['rating_count'];
    }
    
    function getDescription()
    {
    	return $this->_vid['description'];
    }
    
    function getViewCount()
    {
    	return $this->_vid['view_count'];
    }
    
    function getUploadTime()
    {
    	return $this->_vid['upload_time'];
    }
    
    function getCommentCount()
    {
    	return $this->_vid['comment_count'];
    }
    
    function getTags()
    {
    	return $this->_vid['tags'];
    }
    
    function getURL()
    {
    	return $this->_vid['url'];
    }
    
    function getThumbURL()
    {
    	return $this->_vid['thumbnail_url'];
    }
    
    
    function TubePressVideo($videoXML)
    {   
        if (!is_array($videoXML)) {
            return;
        }
        
        $this->_vid['author'] = 
            $videoXML['author']['name'];
          
        $this->_vid['id'] = $videoXML['id'];
        $pos = strrpos($this->_vid['id'], "/");
        $this->_vid['id'] = substr($this->_vid['id'], $pos);
            
        $this->_vid['title'] =
            htmlspecialchars($videoXML['title']['_content'], ENT_QUOTES);
        
        $videoXML['runtime'] =
            TubePressVideo::_humanTime($videoXML['media:group']['yt:duration']['seconds']);
            
        //$videoXML['rating_count'] =
        //    number_format($videoXML['rating_count']);
            
        $this->_vid['description'] = 
            $videoXML['media:group']['media:description']['_content'];    
            
        $this->_vid['view_count'] =
            number_format($videoXML['yt:statistics']['viewCount']);
        
        $this->_vid['thumbnail_url'] = 
            $videoXML['media:group']['media:thumbnail'][0]['url'];
            
        //if (is_numeric($videoXML['upload_time'])) {
        //    $videoXML['upload_time'] =
        //        date("M j, Y", $videoXML['upload_time']);
        //}
        //$videoXML['comment_count'] =
        //    number_format($videoXML['comment_count']);
            
        //$this->_vid['author'] = $videoXML;
    }

    /**
     * Converts seconds to minutes and seconds
     * 
     * @param length_seconds The runtime of a video, in seconds
     */
    function _humanTime($length_seconds)
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
