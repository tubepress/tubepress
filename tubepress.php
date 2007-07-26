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

    $quickOpts = get_option(TP_OPTION_NAME);
    if (($quickOpts == NULL) {
    	return NULL;
    }
    
	if (!array_key_exists(TP_OPT_KEYWORD, $quickOpts["options"])) {
    	return NULL;
    }
    
    if (!is_a($quickOpts["options"][TP_OPT_KEYWORD], "TubePressStringOpt")) {
        return NULL;
    }
    $keyword = $quickOpts["options"][TP_OPT_KEYWORD]->_value;
	
    if (strpos($content, '[' . $keyword) === false) {
        return $content;
    }

    /* ------------------------------------------------------------ */
    /* ------------ PARSE THE TAG --------------------------------- */
    /* ------------------------------------------------------------ */ 

    $options = WordPressOptionsPackage::parse($keyword, $content);
    if (PEAR::isError($options)) {
        return TubePressStatic::bail($options);
    }

    /* ------------------------------------------------------------ */
    /* ------------ PRINT DEBUG OUTPUT IF WE NEED IT -------------- */
    /* ------------------------------------------------------------ */ 

    /* Are we debugging? */
    $debug = $options->getValue(TP_OPT_DEBUG);
    if (PEAR::isError($debug)) {
        return TubePressStatic::bail($debug);
    }
    if ($debug == true
        && isset($_GET[TP_PARAM_DEBUG]) 
        && ($_GET[TP_PARAM_DEBUG] == true)) {
            $newcontent .= TubePressDebug::debug($options);
    }
    
    /* ------------------------------------------------------------ */
    /* ------------ NOW THE FUN PART ------------------------------ */
    /* ------------------------------------------------------------ */ 
    $newcontent .= TubePressGallery::generate($options);

    /* We're done! Replace the tag with our new content */
    return str_replace($options->tagString, $newcontent, $content);
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
	
	print TubePressPlayerPackage::getHeadContents(get_option(TP_OPTION_NAME)["options"]);
}

/* don't forget to add our hooks! */
add_filter('the_content', 'tp_main');
add_action('admin_menu',  'tp_executeOptionsPage');
add_action('wp_head', 'tp_insertCSSJS');
?>
