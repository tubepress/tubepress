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
    var $metaValues;

    /**
     * Constructor
     */
    function TubePressVideo($videoXML)
    {    
        if (!is_array($videoXML)) {
            return PEAR::raiseError(_tpMsg("VIDNOARR"));
        }
        
        if (count($videoXML) == 0) {
            return PEAR::raiseError("VIDEMTARR");
        }
        
        $ut_vid_attributes = array("author", "id", "title",
            "length_seconds", "rating_avg", "rating_count",
            "description", "view_count", "upload_time", "comment_count",
            "tags", "url", "thumbnail_url");
        
        foreach ($ut_vid_attributes as $ut_att) {
            if (!array_key_exists($ut_att, $videoXML)) {
                return PEAR::raiseError(_tpMsg("MISSATT", array($ut_att)));
            }
        }
        
        $this->metaValues =
            array(TP_VID_AUTHOR =>
                      $videoXML['author'],
                
                  TP_VID_ID =>          
                      $videoXML['id'],
                      
                  TP_VID_TITLE =>       
                      htmlspecialchars($videoXML['title'], ENT_QUOTES),
                      
                  TP_VID_LENGTH =>      
                      TubePressVideo::_humanTime($videoXML['length_seconds']),
                      
                  TP_VID_RATING_AVG =>  
                      $videoXML['rating_avg'],
                      
                  TP_VID_RATING_CNT =>  
                      number_format($videoXML['rating_count']),
                      
                  TP_VID_DESC =>        
                      $videoXML['description'],
                      
                  TP_VID_VIEW =>        
                      number_format($videoXML['view_count']),
                      
                  TP_VID_UPLOAD_TIME => 
                      date("M j, Y", $videoXML['upload_time']),
                      
                  TP_VID_COMMENT_CNT => 
                      number_format($videoXML['comment_count']),
                      
                  TP_VID_TAGS =>        
                      $videoXML['tags'],
                      
                  TP_VID_URL =>         
                      $videoXML['url'],
                      
                  TP_VID_THUMBURL =>    
                      $videoXML['thumbnail_url']);
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
