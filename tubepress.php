<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.org
Description: Display configurable YouTube galleries in your posts and/or pages
Author: Eric D. Hough
Version: 1.6.0
Author URI: http://ehough.com


Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)

This file is part of TubePress (http://tubepress.org)

TubePress is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

TubePress is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
*/

function_exists("tp_executeOptionsPage")
    || require("env/WordPress/TubePressOptions.php");

function_exists("tp_classFolder")
	|| require("tubepress_classloader.php");
    
isset($tubepress_base_url)
    || $tubepress_base_url = get_settings('siteurl') . "/wp-content/plugins/tubepress";

/**
 * Main filter hook. Looks for a tubepress tag
 * and replaces it with a gallery (or single video) if it's found
*/

function tp_main($content = '')
{
    /* Store everything we generate in the following string */
    $newcontent = "";

    try {

        if (!tp_shouldWeExecute($content)) {
            return $content;
        }
        
        $stored = get_option("tubepress");
        $stored->parse($content);
        
        if (TubePressStatic::areWeDebugging($stored)) {
        	TubePressStatic::debugEnvironment($stored);
        }
        
        $modeName = $stored->getCurrentValue(TubePressGalleryOptions::mode);
        $gallery = $stored->getGalleryOptions()->getGallery($modeName);
        $newcontent .= $gallery->generate($stored);

    	/* replace the tag with our new content */
        return str_replace($stored->getTagString(), $newcontent, $content);
    
    } catch (Exception $e) {
        return $e->getMessage();
    }
}


/**
 * Spits out the CSS and JS files that we always need for TubePress
 */
function tp_insertCSSJS()
{
    
    global $tubepress_base_url;
    $url = $tubepress_base_url . "/common";
    print<<<GBS
        <script type="text/javascript" src="$url/js/tubepress.js"></script>
        <link rel="stylesheet" href="$url/css/tubepress.css" 
            type="text/css" />
        <link rel="stylesheet" href="$url/css/pagination.css" 
            type="text/css" />
GBS;
    
    $stored = get_option("tubepress");
   
    /* we're in the head here, so just return quietly */
    if ($stored == NULL || !($stored instanceof TubePressStorage_v157)) {
        return;
    }
    
    try {
        $playerName = $stored->getCurrentValue(TubePressDisplayOptions::currentPlayerName);
        $player = TubePressPlayer::getInstance($playerName);
        print $player->getHeadContents();
    } catch (Exception $e) {
        /* this is in the head, so just print an HTML comment and proceed */
        print "<!-- " . $e->getMessage() . " -->";
    }
}

function tp_shouldWeExecute($content) {
    
    $stored = get_option("tubepress");
    
    if ($stored == NULL
        || !($stored instanceof TubePressStorage_v157)) {
        return false;
    }
    
    $trigger = $stored->getCurrentValue(TubePressAdvancedOptions::triggerWord);
    
    if (strpos($content, '[' . $trigger) === false) {
        return false;
    }
    
    return true;
}

/* don't forget to add our hooks! */
add_filter('the_content', 'tp_main');
add_action('admin_menu',  'tp_executeOptionsPage');
add_action('wp_head', 'tp_insertCSSJS');
?>
