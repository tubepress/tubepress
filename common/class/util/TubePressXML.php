<?php
/**
 * TubePressXML.php
 * 
 * Does all our XML and REST dirty work
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


abstract class TubePressXML
{

    /**
     * Connects to YouTube and returns raw XML
     */
    public static function fetch($request, $options = "")
    {   
        
        /* We turn off error reporting here because Snoopy is very noisy if we
         * can't connect
         */
        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

        $snoopy = new snoopy();
        $snoopy->read_timeout = 6;
        if (is_a($options, "TubePressOptionsPackage")) {
            $timeout = $options->get(TP_OPT_TIMEOUT);
            if (PEAR::isError($timeout)) {
                return $timeout;
            }
            $snoopy->read_timeout = $timeout->getValue();
        }

        if (!$snoopy->fetch($request)) {
            throw new Exception(_tpMsg("REFUSED") .
                $snoopy->error); 
        }
    
        if ($snoopy->timed_out) {
            throw new Exception(_tpMsg("TIMEOUT", $snoopy->read_timeout)); 
        }
    
        if (strpos($snoopy->response_code, "200 OK") === false) {
            throw new Exception(
                sprintf("YouTube did not respond with an HTTP OK: %s", 
                $snoopy->response_code));
        }
    
        error_reporting(E_ALL ^ E_NOTICE);

        return $snoopy->results;
    }

    /**
     * Looks at what the user is trying to do and generates the URL that will
     * be sent to YouTube base on that
     */
    public static function generateGalleryRequest($stored)
    {
        class_exists('TubePressStatic') || require("TubePressStatic.php");
        
        $request = "http://gdata.youtube.com";

        $currentMode = $stored->options->get(TP_OPT_MODE);

        switch ($currentMode->getValue()) {
       
            case TP_MODE_USER:
                $modeObj = $stored->modes->get(TP_MODE_USER);
                $request .= "/feeds/users/" . $modeObj->getValue() . "/uploads";
                break;
            
            case TP_MODE_FAV:
                $modeObj = $stored->modes->get(TP_MODE_FAV);
                 $request .= "/feeds/users/" . $modeObj->getValue() . "/favorites";
                break;
            
            case TP_MODE_TAG:
            case TP_MODE_REL:
            case TP_MODE_SEARCH:
            
            /* list_by_related is now deprecated, and returns identical 
             * results to list_by_tag. See http://groups.google.com/group/
             * youtube-api-newbies/browse_thread/thread/871006b92d2141c3/
             * 66eef9a7fced754e?lnk=gst&q=list_by_tag+identical&rnum=
             * 1#66eef9a7fced754e
             */
            
                $modeObj = $stored->modes->get(TP_MODE_SEARCH);
                $request .= "/feeds/videos?vq=" . urlencode($modeObj->getValue());
                break;
            
            case TP_MODE_PLST:
                $modeObj = $stored->modes->get(TP_MODE_PLST);
                $request .= "/feeds/playlists/" . $modeObj->getValue();
                break;
            
            case TP_MODE_POPULAR:
                $modeObj = $stored->modes->get(TP_MODE_POPULAR);
                $request .= "/feeds/standardfeeds/most_viewed?time=" . $modeObj->getValue();
                break;
        
            case TP_MODE_FEATURED:
                $request .= "/feeds/standardfeeds/recently_featured";
                break;
                
            case TP_MODE_MOBILE:
                $request .= "/feeds/standardfeeds/watch_on_mobile";
                break;
            
            case TP_MODE_TOPRATED:
                $modeObj = $stored->modes->get(TP_MODE_TOPRATED);
                $request .= "/feeds/standardfeeds/top_rated?time=" . $modeObj->getValue();
                break;
                
            default:
                throw new Exception(sprintf("Invalid mode specified (%s)",
                    $currentMode->getValue()));
        }

        $val = $stored->options->get(TP_OPT_VIDSPERPAGE);
        $val = $val->getValue();
        $pageNum = TubePressStatic::getPageNum();
        $start = ($pageNum * $val) - $val + 1;
        
        if ($start + $val > 1000) {
            $val = 1000 - $start;
        }
        
        $delimeter = '?';
        if (strpos($request, '?') !== false) {
            $delimeter = '&';
        }
        $request .= sprintf("%sstart-index=%s&max-results=%s",
            $delimeter, $start, $val);
    $delimeter = '?';
        if (strpos($request, '?') !== false) {
            $delimeter = '&';
        }
        $racyOpt = $stored->options->get(TP_OPT_FILTERADULT);
        if (!$racyOpt->getValue()
            && $currentMode->getValue() != TP_MODE_MOBILE
            && $currentMode->getValue() != TP_MODE_FEATURED
            && $currentMode->getValue() != TP_MODE_FAV
            && $currentMode->getValue() != TP_MODE_USER) {
            $request .= sprintf("%sracy=include", $delimeter);
        }
        
        
        if ($currentMode->getValue() != TP_MODE_PLST) {
        $orderOpt = $stored->options->get(TP_OPT_ORDERBY);
        $oderVal = $orderOpt->getValue();
        $delimeter = '?';
        if (strpos($request, '?') !== false) {
            $delimeter = '&';
        }
            $request .= sprintf("%sorderby=%s", $delimeter,
                $oderVal);
        }
        echo $request;
        return $request;
    }
    
    public static function generateVideoRequest($videoID)
    {
    	return "http://gdata.youtube.com/feeds/videos/" . $videoID;
    }
    
    /**
     * Takes YouTube's raw xml and tries to return an array of the videos
     */
    public static function toArray(&$youtube_xml)
    {
    
        class_exists('XML_Unserializer') || require(dirname(__FILE__) .
            '/../../../lib/PEAR/XML/XML_Serializer/Unserializer.php');
    
        $unserializer_options = array ('parseAttributes' => TRUE);

        $Unserializer = &new XML_Unserializer($unserializer_options);

        $status = $Unserializer->unserialize($youtube_xml);

        /* make sure we could read the xml */
        if (PEAR::isError($status)) {
            return $status;
        }

        $result = $Unserializer->getUnserializedData();

        /* double check to make sure we have an array */
        if (!is_array($result)) {
            throw new Exception("XML unserialization error");
        }

        return $result;
    }
}
?>