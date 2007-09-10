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

class TubePressXML
{
    /**
     * Constructor
     */
    function TubePressXML()
    {
        die("This is a static class");
    }

    /**
     * Connects to YouTube and returns raw XML
     */
    function fetchRawXML($options, $request)
    {   
        class_exists("snoopy") || require(dirname(__FILE__) .
            "/../../../lib/snoopy/Snoopy.class.php");
        
        /* We turn off error reporting here because Snoopy is very noisy if we
         * can't connect
         */
        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
        
        $snoopy = new snoopy();
        $timeout = $options->get(TP_OPT_TIMEOUT);
        if (PEAR::isError($timeout)) {
            return $timeout;
        }
        $snoopy->read_timeout = $timeout->getValue();

        if (!$snoopy->fetch($request)) {
            return PEAR::raiseError(_tpMsg("REFUSED") .
                $snoopy->error); 
        }
    
        if ($snoopy->timed_out) {
            return PEAR::raiseError(_tpMsg("TIMEOUT", $snoopy->read_timeout)); 
        }
    
        if (strpos($snoopy->response_code, "200 OK") === false) {
            return PEAR::raiseError(
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
    function generateRequest($stored)
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
            
            /* list_by_related is now deprecated, and returns identical 
             * results to list_by_tag. See http://groups.google.com/group/
             * youtube-api-newbies/browse_thread/thread/871006b92d2141c3/
             * 66eef9a7fced754e?lnk=gst&q=list_by_tag+identical&rnum=
             * 1#66eef9a7fced754e
             */
            
                $modeObj = $stored->modes->get(TP_MODE_REL);
                print_r($modeObj);
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
            default:
                return PEAR::raiseError(_tpMsg("BADMODE",
                    $currentMode->getValue()));
        }

        //if (TubePressStatic::areWePaging($stored->options)) {
         //   $val = $stored->options->get(TP_OPT_VIDSPERPAGE);
         //   $pageNum = TubePressStatic::getPageNum();
         //   $request .= sprintf("&page=%s&per_page=%s",
         //       $pageNum, $val->getValue());
       // }

        return $request;
    }
    
    /**
     * Takes YouTube's raw xml and tries to return an array of the videos
     */
    function parseRawXML(&$youtube_xml)
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
            return PEAR::raiseError("XML unserialization error");
        }

        if (!is_array($result['entry'])) {
            return PEAR::raiseError("No matching videos!");
        }
        
        return $result['entry'];
    }
}
?>