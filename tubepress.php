<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.org
Description: Display configurable YouTube galleries in your posts and/or pages
Author: Eric Hough
Version: 1.5.0
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

defined("TP_OPTION_NAME")
    || require("common/defines.php");

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

    $keyword = "";
    $stored = "";
    if (!tp_shouldWeExecute($content, $keyword, $stored)) {
        return $content;
    }
    
    /* ------------------------------------------------------------ */
    /* ------------ PARSE THE TAG --------------------------------- */
    /* ------------------------------------------------------------ */ 

    WordPressStorageBox::applyTag($keyword, $content, $stored->options);
    if (PEAR::isError($stored->options)) {
        return TubePressStatic::bail($stored->options);
    }

    /* ------------------------------------------------------------ */
    /* ------------ PRINT DEBUG OUTPUT IF WE NEED IT -------------- */
    /* ------------------------------------------------------------ */ 

    /* Are we debugging? */
    $debug = $stored->options->getValue(TP_OPT_DEBUG);
    if (PEAR::isError($debug)) {
        return TubePressStatic::bail($debug);
    }
    if ($debug == true
        && isset($_GET[TP_PARAM_DEBUG]) 
        && ($_GET[TP_PARAM_DEBUG] == true)) {
            $newcontent .= TubePressDebug::debug($stored);
    }
    
    /* ------------------------------------------------------------ */
    /* ------------ NOW THE FUN PART ------------------------------ */
    /* ------------------------------------------------------------ */ 
    $newcontent .= TubePressGallery::generate($stored);

    /* We're done! Replace the tag with our new content */
    return str_replace($stored->options->tagString, $newcontent, $content);
}

/**
 * Spits out the CSS and JS files that we always need for TubePress
 */
function tp_insertCSSJS()
{
    $stored = get_option(TP_OPTION_NAME);
    if ($stored == NULL) {
    	return;
    }
    
    if (!is_a($stored, "TubePressStorageBox")) {
        return;
    }

    if (PEAR::isError($stored->checkValidity())) {
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
    
    $stored = get_option(TP_OPTION_NAME);
    if ($stored == NULL) {
    	return false;
    }
    
    if (!is_a($stored, "TubePressStorageBox")) {
        return false;
    }
    
    if (PEAR::isError($stored->checkValidity())) {
        return false;
    }
    
    $keyword = $stored->options->getValue(TP_OPT_KEYWORD);
    if (PEAR::isError($keyword)) {
        return false;
    }
    
    if (strpos($content, '[' . $keyword) === false) {
        return false;
    }
    return true;
}

/* don't forget to add our hooks! */
add_filter('the_content', 'tp_main');
add_action('admin_menu',  'tp_executeOptionsPage');
add_action('wp_head', 'tp_insertCSSJS');
?>
