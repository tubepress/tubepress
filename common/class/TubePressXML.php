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

class_exists("snoopy") || require(dirname(__FILE__) . "/../../lib/snoopy/Snoopy.class.php");

class TubePressXML
{

    /**
     * Connects to YouTube and returns raw XML
     */
    function fetchRawXML($options)
    {   
        $snoopy = new snoopy();
        $snoopy->read_timeout = $options->getValue(TP_OPT_TIMEOUT);

        $request = TubePressXML::generateRequest($options);
        
        if (PEAR::isError($request)) {
            return $request;
        }

        if (!$snoopy->fetch($request)) {
            return PEAR::raiseError(_tpMsg("REFUSED") .
                $snoopy->error); 
        }
    
        if ($snoopy->timed_out) {
            return PEAR::raiseError(_tpMsg("TIMEOUT", $snoopy->read_timeout)); 
        }
    
        if (strpos($snoopy->response_code, "200 OK") === false) {
            return PEAR::raiseError(_tpMsg("BADHTTP", 
                $snoopy->response_code));
        }
    
        return $snoopy->results;
    }

    /**
     * Takes YouTube's raw xml and tries to return an array of the videos
     */
    function parseRawXML(&$youtube_xml)
    {
    
        class_exists('XML_Unserializer') || require(dirname(__FILE__) . '/../../lib/PEAR/XML/XML_Serializer/Unserializer.php');
    
        $unserializer_options = array ('parseAttributes' => TRUE);

        $Unserializer = &new XML_Unserializer($unserializer_options);

        $status = $Unserializer->unserialize($youtube_xml);

        if (PEAR::isError($status)) {
            return $status;
        }

        $result = $Unserializer->getUnserializedData();
    
        /* make sure we could read the xml */
        if (!is_array($result) || PEAR::isError($result)) {
            return PEAR::raiseError(_tpMsg("XMLUNSERR"));
        }
    
        /* make sure we have a status from YouTube */
        if (!array_key_exists('status', $result)) {
            return PEAR::raiseError(_tpMsg("NOSTATUS"));
        }
    
        /* see if YouTube liked us */
        if ($result['status'] != "ok") {
            $msg = "Unknown error";
            if (is_array($result['error']) && array_key_exists('description', $result['error']) 
                && array_key_exists('code', $result['error'])) {
                    $msg = $result['error']['description'] . " Code " . $result['error']['code'];
            }
            return PEAR::raiseError(_tpMsg("YTERROR", $msg));
        }

        if (!array_key_exists('total', $result['video_list'])) {
            return PEAR::raiseError(_tpMsg("NOCOUNT"));
        }
    
        /* if we have a video_list, just return it */
        if (is_array($result['video_list'])) {
            return $result['video_list'];
        }
    
        return PEAR::raiseError(_tpMsg("OKNOVIDS"));
    }

    function generateRequest($options)
    {
        class_exists('TubePressStatic') || require("TubePressStatic.php");
        
        $request = TP_YOUTUBE_RESTURL . "method=youtube.";

        $result = $options->getValue(TP_OPT_SEARCHBY);
        if (PEAR::isError($result)) {
            return $result;
        }

        switch ($result) {
       
            case TP_MODE_USER:
                $request .= "videos.list_by_user" .
                    "&user=" . $options->getValue(TP_OPT_USERVAL);
                break;
            
            case TP_MODE_FAV:
                $request .= "users.list_favorite_videos" .
                    "&user=" . $options->getValue(TP_OPT_FAVVAL);
                break;
            
            case TP_MODE_TAG:
                $request .= "videos.list_by_tag" .
                    "&tag=" . urlencode($options->getValue(TP_OPT_TAGVAL));
                break;
            
            /* list_by_related is now deprecated, and returns identical results to
             * list_by_tag. See http://groups.google.com/group/youtube-api-newbies/
             * browse_thread/thread/871006b92d2141c3/66eef9a7fced754e?
             * lnk=gst&q=list_by_tag+identical&rnum=1#66eef9a7fced754e
             */
            case TP_MODE_REL:
                $request .= "videos.list_by_tag" .
                    "&tag=" . urlencode($options->getValue(TP_OPT_TAGVAL));
                break;
            
            case TP_MODE_PLST:
                $request .= "videos.list_by_playlist" .
                    "&id=" . $options->getValue(TP_OPT_PLSTVAL);
                break;
            
            case TP_MODE_POPULAR:
                $request .= "videos.list_popular" .
                    "&time_range=" . $options->getValue(TP_OPT_POPVAL);
                break;
            
            case TP_MODE_CATEGORY:
                $request .= "videos.list_by_category" .
                    "&page=1" .
                    "&per_page=" . $options->getValue(TP_OPT_VIDSPERPAGE) .
                    "&category_id=" . $options->getValue(TP_OPT_CATVAL);
                $paging = true;
                break;
        
            case TP_MODE_FEATURED:
                $request .= "videos.list_featured";
                break;
            default:
                return PEAR::raiseError(_tpMsg("BADMODE",
                    $options->getValue(TP_OPT_SEARCHBY)));
        }

        if (TubePressStatic::areWePaging($options)) {
            $pageNum = ((isset($_GET[TP_PAGE_PARAM]))?
                $_GET[TP_PAGE_PARAM] : 1);
            $request .= sprintf("&page=%s&per_page=%s",
                $pageNum, $options->getValue(TP_OPT_VIDSPERPAGE));
        }

        $request .= "&dev_id=" . $options->getValue(TP_OPT_DEVID);
        return $request;
    }
    function TubePressXML()
    {
        die("This is a static class");
    }
}
?>