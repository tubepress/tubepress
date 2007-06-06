<?php
/**
 * TubePressDebug.php
 * 
 * Prints out gobs of debugging information
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

class TubePressDebug
{
    /**
     * Static class
     */
    function TubePressDebug()
    {
        die("This is a static class.");
    }
    
    /**
     * This is the only public function here. Actually it's
     * the only real function.
     */
    function debug($options)
    {
        echo "TUBEPRESS DEBUG MODE<BR/><ol>";
        
        /* make sure our base URL is defined */
        global $tubepress_base_url;
        if (!isset($tubepress_base_url)) {
            echo "<li>tubepress_base_url is not defined</li>";
            TubePressDebug::_finish();
            return;
        } else {
            echo "<li>tubepress_base_url is defined to be <pre>" . 
                $tubepress_base_url . "</pre></li>";
        }
        
        /* see if we even have some options */
        if ($options == NULL) {
            echo "<li>Your options are completely missing!</li>";
            echo TubePressDebug::_finish();
            return;
        }
        
        /* make sure the options look good */
        $validOpts = TubePressOptionsPackage::checkValidity($options->_allOptions);
        if (PEAR::isError($validOpts)) {
            echo "<li>There is a problem with your options: " . $validOpts->message;
            echo "<br /><pre>";
            print_r($options);
            echo "</pre></li>";
            echo TubePressDebug::_finish();
            return;
        } else {
            echo "<li>Your stored options look good</li>";
        }
    
        /* see what we're going to do */
        $next = TubePressStatic::determineNextAction($options);
        if ($next == "SINGLEVIDEO") {
            echo "<li>We will print just one video on this page.</li>";
        } else {
            echo "<li>We will print a gallery on this page.</li>";
            TubePressDebug::_galleryDebug($options);
        }
        
        /* the full URL to this page */
        $url = TubePressStatic::fullURL();
        echo "<li>The URL to this page is <pre>" . $url . "</pre></li>";
        
        /* this is intended for subclasses of TubePressOptionsPackage
         * to spit out any debugging info they want
         */
        echo $options->debug();
    
        /* print out the entire package */
        echo "<li>And now, the motherload. This is your TubePressOptionsPackage...<br/><pre>";
        print_r($options);
        echo "</pre></li>";
        TubePressDebug::_finish();
    }
    
    /**
     * Debugs gallery generation
     */
    function _galleryDebug($options)
    {
        /* see if we're in a mode that does paging */
        $paging = TubePressStatic::areWePaging($options);
        if ($paging) {
            echo "<li>We will do pagination if we need to</li>";
        } else {
            echo "<li>We won't do pagination</li>";
        }
        
        /* see if we can make the request for YouTube */
        $request = TubePressXML::generateRequest($options);
        if (PEAR::isError($request)) {
            echo "<li>Could not generate a request: " . $request->message . "</li>";
            return;
        } else {
            echo "<li>The parameters we'll send to YouTube<pre>";
            $theUrl = new Net_URL($request);
            print_r($theUrl->querystring);
            echo "</pre></li>";
            echo "<li><a href='" . $request . "'>Click here to see the raw results from YouTube</a></li>";
        }
        
        /* see if we can talk to YouTube */
        $youtube_xml = TubePressXML::fetchRawXML($options);
        if (PEAR::isError($youtube_xml)) {
            echo "<li>Problem talking to YouTube: " . $youtube_xml->message . "</li>";
            return;
        } else {
            echo "<li>No problems talking to YouTube</li>";
        }
        
        /* see if we can understand the XML result */
        $videoArray = TubePressXML::parseRawXML($youtube_xml);
        if (PEAR::isError($videoArray)) {
            echo "<li>The results from YouTube seem to be malformed: " . $videoArray->message . "</li>";
            return;
        } else {
            echo "<li>No problem parsing the results from YouTube</li>";
            echo "<li>The results as an array<pre>";
            print_r($videoArray);
            echo "</pre></li>";
        }
        
        /* see how many videos we actually received */
        $videosReturnedCnt = is_array($videoArray['video'][0]) ?
            count($videoArray['video']) :
            1;
        echo "<li>We seem to have received " . $videosReturnedCnt . " videos</li>";
        
        /* find out how many videos we'll actually print */
        $vidLimit = ($paging ?
            $options->getValue(TP_OPT_VIDSPERPAGE) : 
            $videosReturnedCnt);
        if ($paging) {
            echo "<li>Since we are in a mode that supports paging, our video limit for this page will be " .
                $options->getValue(TP_OPT_VIDSPERPAGE) . ", which is user defined</li>";
        } else {
            echo "<li>Since we're in a non-paging mode, we'll print that many videos</li>";
        }
        if ($videosReturnedCnt < $vidLimit) {
            echo "<li>We got less videos than we can handle for this page</li>";
            $vidLimit = $videosReturnedCnt;
            echo "<li>Our new videos-to-print count is now " . $vidLimit . "</li>";
        }
        
        /* see if we can interpret each video result */
        echo "<li>Checking each video...</li><ol>";
        for ($x = 0; $x < $vidLimit; $x++) {
            
            /* Create a TubePressVideo object from the XML (if we can) */
            if ($videosReturnedCnt == 1) {
                $video = new TubePressVideo($videoArray['video']);
            } else {
                $video = new TubePressVideo($videoArray['video'][$x]);
            }
            
            echo "<li>" . $video->metaValues[TP_VID_TITLE] . "... ";
    
            if (PEAR::isError($video)) {
                echo "ERROR: " . $video->message;
            } else {
                echo "OK";
            }
            echo "</li>";
        }
        echo "</ol>";
    }
    
    /**
     * Spits out the trailer
     */
    function _finish()
    {
        echo "</ol>\r\nEND TUBEPRESS DEBUG MODE<br />";
    }
}
?>
