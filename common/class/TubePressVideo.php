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
    var $metaValues;

    /**
     * Constructor
     */
    function TubePressVideo($videoXML)
    {   
        if (!is_array($videoXML)) {
            return;
        }
        
        $this->metaValues[TP_VID_AUTHOR] = $videoXML['author'];
        $this->metaValues[TP_VID_ID] = $videoXML['id'];
        $this->metaValues[TP_VID_TITLE] =
            htmlspecialchars($videoXML['title'], ENT_QUOTES);
        $this->metaValues[TP_VID_LENGTH] =
            TubePressVideo::_humanTime($videoXML['length_seconds']);
        $this->metaValues[TP_VID_RATING_AVG] = $videoXML['rating_avg'];
        $this->metaValues[TP_VID_RATING_CNT] =
            number_format($videoXML['rating_count']);
        $this->metaValues[TP_VID_DESC] = $videoXML['description'];
        $this->metaValues[TP_VID_VIEW] =
            number_format($videoXML['view_count']);
        if (is_numeric($videoXML['upload_time'])) {
            $this->metaValues[TP_VID_UPLOAD_TIME] =
                date("M j, Y", $videoXML['upload_time']);
        }
        $this->metaValues[TP_VID_COMMENT_CNT] =
            number_format($videoXML['comment_count']);
        $this->metaValues[TP_VID_TAGS] = $videoXML['tags'];
        $this->metaValues[TP_VID_URL] = $videoXML['url'];
        $this->metaValues[TP_VID_THUMBURL] = $videoXML['thumbnail_url'];
    }
    
    /**
     * Checks to see if this video was properly filled in
     */
    function isValid()
    {
        if (!is_array($this->metaValues)) {
            return false;
        }
        
        $metas = TubePressVideo::getMetaNames();
        foreach ($metas as $meta) {
            if (!array_key_exists($meta, $this->metaValues)) {
                return false;
            }
            if ($this->metaValues[$meta] == "") {
                return false;
            }
        }
        return true;
    }
    
    /**
     * The full list of meta values that we want to retrieve from each
     * video
     */
     function getMetaNames()
     {
         return array(TP_VID_TITLE, TP_VID_LENGTH, TP_VID_VIEW, TP_VID_AUTHOR,
           TP_VID_ID, TP_VID_RATING_AVG, TP_VID_RATING_CNT, TP_VID_UPLOAD_TIME,
           TP_VID_COMMENT_CNT, TP_VID_TAGS, TP_VID_URL, TP_VID_THUMBURL,
           TP_VID_DESC);
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
