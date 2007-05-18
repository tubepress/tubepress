<?php
/*
tp_debug.php

Spits out gobs of debugging info

Copyright (C) 2007 Eric D. Hough (http://ehough.com)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 *  Spits out tons and tons of debug info
 */
function tp_debug($options)
{
    require_once("tp_options_logic.php");
    $tagString = $options->tagString;
    $dbCheck = (tp_validOptions(get_option(TP_OPTION_NAME))? 'good' : 'bad');    //WORDPRESS
    print <<<EOH
        YOU ARE NOW IN TUBEPRESS DEBUG MODE<BR/><ol>
        <li>Your database looks $dbCheck</li>
        <li>Here's the tag string you're using in this page: 
        <pre>$tagString</pre></li>
        <li>Here are the custom options that were parsed from that tag 
        string (if any) along with the site URL:<br/>
        <pre>
EOH;
    print_r($options->customOptions);
    print <<<EOH
        </pre></li>
        <li>Here are the options that were pulled from the db:<br/>
        <pre>
EOH;
    print_r($options->dbOptions);
    echo '</pre></li>';

    echo '<li>We ARE ';
    if (!tp_areWePaging($options)) {
        echo 'NOT ';
    }
    echo 'paging</li>';
        
    echo '<li>We ARE ';
    if (!tp_printingSingleVideo($options)) {
        echo 'NOT ';
    }
    echo 'printing just a single video</li>';        
        
    echo '<li>Here is the full URL to this page: <pre>' . tp_fullURL() . 
        '</pre></li>';
    
    /* Stop here if we're not making any requests to YouTube */
    if (tp_printingSingleVideo($options)) {
        return;
    }
    
    echo '<li>Here is the request that will be generated and sent to ' .
        'YouTube. ' .
        'Click it to see the raw results:<br/>';
    $request = tp_generateRequest($options);
    echo '<a href="' . $request . '">' . $request . '</a></li>';

    /* Grab the XML from YouTube's API */
    $youtube_xml = tp_get_youtube_xml($options);

    echo '<li>The result CAN';
    if (!is_a($youtube_xml, 'IsterXmlNode')) {
        echo 'NOT';
    }
    echo ' be interpreted as an IsterXmlNode</li>';
    
    /* count how many we got back */
    $videosReturnedCount = tp_count_videos($youtube_xml);
    echo '<li>Found ' . $videosReturnedCount . ' videos in the result</li>';
    
    $vidLimit = (tp_areWePaging($options)? 
        $options->get(TP_OPT_VIDSPERPAGE) : $videosReturnedCount);
        
    if ($videosReturnedCount < $vidLimit) {
        $vidLimit = $videosReturnedCount;
    }
    echo '<li>We will print ' . $vidLimit . ' videos on this page</li>';
    
    echo '<li>Now we will loop through each video and ' .
        'see if we can interpret it...<ol>';
    
    for ($x = 0; $x < $vidLimit; $x++) {
        echo '<li>';
        
        /* create a video from each */
        if ($vidLimit == 1) {
            $video = new TubePressVideo($youtube_xml->video);
        } else {
            $video = new TubePressVideo($youtube_xml->video[$x]);
        }
        if ($video->metaValues[TP_VID_ID] == '') {
            echo 'PROBLEM!';
        } else {
            echo 'OK - ' . $video->metaValues[TP_VID_TITLE];
        }
        echo '</li>';
    }
    echo '</li></ol>';
    
    echo '</ol>END OF TUBEPRESS DEBUG MODE';
}

?>