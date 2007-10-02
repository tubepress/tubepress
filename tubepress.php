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

defined(TP_OPTION_NAME)
    || require("common/defines.php");
class_exists("TubePressStorageBox")
    || require("common/class/TubePressStorageBox.php");
class_exists("PEAR")
    || require("lib/PEAR/PEAR.php");
function_exists("tp_executeOptionsPage")
    || require("env/WordPress/TubePressOptions.php");
class_exists("TubePressGallery")
    || require("common/class/TubePressGallery.php");
class_exists("TubePressDebug")
    || require("common/class/util/TubePressDebug.php");
    
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

    $keyword = "";
    $stored = "";
    if (!tp_shouldWeExecute($content, $keyword, $stored)) {
        return $content;
    }
    
    /* ------------------------------------------------------------ */
    /* ------------ PARSE THE TAG --------------------------------- */
    /* ------------------------------------------------------------ */ 

    WordPressStorageBox::applyTag($keyword->getValue(), $content, $stored, $stored->options);

    /* ------------------------------------------------------------ */
    /* ------------ PRINT DEBUG OUTPUT IF WE NEED IT -------------- */
    /* ------------------------------------------------------------ */ 

    /* Are we debugging? */
    $debug = $stored->options->get(TP_OPT_DEBUG);

    if ($debug->getValue() == true
        && isset($_GET[TP_PARAM_DEBUG]) 
        && ($_GET[TP_PARAM_DEBUG] == true)) {
            $newcontent .= TubePressDebug::debug($stored);
    }
    
    /* ------------------------------------------------------------ */
    /* ------------ NOW THE FUN PART ------------------------------ */
    /* ------------------------------------------------------------ */ 
    
    /* printing a single video only? */
    $playerLocation = $stored->options->get(TP_OPT_PLAYIN);
    if ($playerLocation->getValue() == TP_PLAYIN_NW
    	&& isset($_GET[TP_PARAM_VID])) {
    	ob_start();
        include dirname(__FILE__) . "/common/templates/single_video.php";
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    } else {
    	$newcontent .= TubePressGallery::generate($stored);
    }

    /* We're done! Replace the tag with our new content */
    return str_replace($stored->tagString, $newcontent, $content);
}

/**
 * Spits out the CSS and JS files that we always need for TubePress
 */
function tp_insertCSSJS()
{
    $stored = tp_safeGetStorage();
    if ($stored == NULL) {
        return;
    }
    
    global $tubepress_base_url;
    $url = $tubepress_base_url . "/common";
    print<<<GBS
        <script type="text/javascript" src="$url/tubepress.js"></script>
        <link rel="stylesheet" href="$url/tubepress.css" 
            type="text/css" />
        <link rel="stylesheet" href="$url/pagination.css" 
            type="text/css" />
GBS;
    
    print TubePressPlayerPackage::getHeadContents($stored);
}

function tp_shouldWeExecute($content, &$keyword, &$stored) {
    
    $stored = tp_safeGetStorage();
    if ($stored == NULL) {
        return false;
    }
    
    $keyword = $stored->options->get(TP_OPT_KEYWORD);
    
    if (strpos($content, '[' . $keyword->getValue()) === false) {
        return false;
    }
    return true;
}

function tp_safeGetStorage() {
    
    global $tubepress_storagebox;

    if (isset($tubepress_storagebox)) {
        return $tubepress_storagebox;
    }

    $stored = get_option(TP_OPTION_NAME);
    if ($stored == NULL) {
        return NULL;
    }
    if (!is_a($stored, "TubePressStorageBox")) {
        return NULL;
    }
    
    $result = $stored->checkValidity();
    if (PEAR::isError($result)) {
        return NULL;
    }

    $tubepress_storagebox = $stored;
    return $stored;
}

/* don't forget to add our hooks! */
add_filter('the_content', 'tp_main');
add_action('admin_menu',  'tp_executeOptionsPage');
add_action('wp_head', 'tp_insertCSSJS');
?>
