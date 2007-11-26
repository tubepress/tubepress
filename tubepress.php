<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.org
Description: Display configurable YouTube galleries in your posts and/or pages
Author: Eric Hough
Version: 1.5.5
Author URI: http://ehough.com

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

function_exists("tp_executeOptionsPage")
    || require("env/WordPress/TubePressOptions.php");

if (!isset($tubepress_base_url)) {
    $tubepress_base_url = get_settings('siteurl') . "/wp-content/plugins/tubepress";
}

/**
 * Main filter hook. Looks for a tubepress tag
 * and replaces it with a gallery (or single video) if it's found
*/
function tp_main($content = '')
{
    /* Store everything we generate in the following string */
    $newcontent = "";
    
    /* ------------------------------------------------------------ */
    /* ------------ DETERMINE IF WE NEED TO EXECUTE --------------- */
    /* ------------------------------------------------------------ */

    try {
        if (!tp_shouldWeExecute($content)) {
            return $content;
        }
    } catch (Exception $e) {
        return $e->getMessage();
    }
    
    /* ------------------------------------------------------------ */
    /* ------------ PARSE THE TAG --------------------------------- */
    /* ------------------------------------------------------------ */ 

    WordPressStorageBox::applyTag($keyword->getValue(), $content, $stored, $stored->options);
    
    /* ------------------------------------------------------------ */
    /* ------------ NOW THE FUN PART ------------------------------ */
    /* ------------------------------------------------------------ */ 
    
    /* printing a single video only? */
    $player = $stored->getDisplayOptions()->get(TubePressDisplayOptions::player)->getCurrentValue();
    $gallery = $stored->getGalleryOptions()->get(TubePressGalleryOptions::mode)->getCurrentValue();
    
    if (is_a($player, "TPNewWindowPlayer")
    	&& isset($_GET[TP_PARAM_VID])) {
    	ob_start();
        include dirname(__FILE__) . "/common/templates/single_video.php";
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    } else {
    	$newcontent .= $gallery->generate($stored);
    }

    /* We're done! Replace the tag with our new content */
    return str_replace($stored->tagString, $newcontent, $content);
}

/**
 * Spits out the CSS and JS files that we always need for TubePress
 */
function tp_insertCSSJS()
{
    
    global $tubepress_base_url;
    $url = $tubepress_base_url . "/common";
    print<<<GBS
        <script type="text/javascript" src="$url/tubepress.js"></script>
        <link rel="stylesheet" href="$url/tubepress.css" 
            type="text/css" />
        <link rel="stylesheet" href="$url/pagination.css" 
            type="text/css" />
GBS;
    
    $stored = get_option("tubepress");
    
    if ($stored == NULL || !is_a($stored, "TubePressStorage")) {
        return;
    }
    
    $player = $stored->getDisplayOptions()->get(TubePressDisplayOptions::currentPlayerName)->getCurrentValue();
    
    print $player->getHeadContents();
}

function tp_shouldWeExecute($content) {
    
    $stored = get_option("tubepress");
    
    if ($stored == NULL) {
        return false;
    }
    
    if (!is_a($stored, "TubePressStorage")) {
        throw new Exception("Your stored options are invalid for this version
           of TubePress. Please go to WP-Admin > Options > TubePress to initialize them.");
    }
    
    $keyword = $stored->getAdvancedOptions()->get(TubePressAdvancedOptions::triggerWord)->getCurrentValue();
    
    if (strpos($content, '[' . $keyword) === false) {
        return false;
    }
    return true;
}

function __autoload($className) {

    $folder = tp_classFolder($className);

    if ($folder) {
        require_once($folder.$className.".class.php");
    }
}

function tp_classFolder($className, $sub = "/") {
    
    $currentDir = dirname(__FILE__);
    
    $dir = dir($currentDir.$sub);
    
    if (file_exists($currentDir.$sub.$className.".class.php")) {
        return $currentDir.$sub;
    }

    while (false !== ($folder = $dir->read())) {
        
        if (strpos($folder, ".") === 0) {
            continue;
        }
        
        if (is_dir($currentDir.$sub.$folder)) {
            $subFolder = tp_classFolder($className, $sub.$folder."/");
                
            if ($subFolder) {
                return $subFolder;
            }
        }     
    }
    $dir->close();
    return false;
}

/* don't forget to add our hooks! */
add_filter('the_content', 'tp_main');
add_action('admin_menu',  'tp_executeOptionsPage');
add_action('wp_head', 'tp_insertCSSJS');
?>
