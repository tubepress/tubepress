<?php
/**
Plugin Name: TubePress
Plugin URI: http://ehough.com/youtube/tubepress
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

function_exists('tp_insertCSSJS')
    || require('env/WordPress/WordPressHooks.php');
function_exists('_tpMsg')
    || require('common/messages.php');
    
defined(TP_OPTION_NAME)
    || require('common/defines.php');
    
class_exists('WordPressOptionsPackage')
    || require('env/WordPress/WordPressOptionsPackage.php');
class_exists('TubePressStatic')
    || require('common/class/TubePressStatic.php');
class_exists('TubePressGallery')
    || require('common/class/TubePressGallery.php');

/**
 * Main filter hook. Looks for a tubepress tag
 * and replaces it with a gallery (or single video) if it's found
*/
function tp_main ($content = '')
{
    /* Store everything we generate in the following string */
     $newcontent = "";
    
    /* ------------------------------------------------------------ */
    /* ------------ DETERMINE IF WE NEED TO EXECUTE --------------- */
    /* ------------------------------------------------------------ */

    $quickOpts = get_option(TP_OPTION_NAME);
    if ($quickOpts == NULL) {
        return $content;
    }

    $keyword = $quickOpts[TP_OPT_KEYWORD]->getValue();
    if (strpos($content, '[' . $keyword) === false) {
        return $content;
    }
 
    /* ------------------------------------------------------------ */
    /* ------------ PARSE THE TAG --------------------------------- */
    /* ------------------------------------------------------------ */ 
 
    $options = WordPressOptionsPackage::parse($keyword, $content);
    if (PEAR::isError($options)) {
        return TubePressStatic::bail(_tpMsg("PARSERR"), $options);
    }

    /* ------------------------------------------------------------ */
    /* ------------ PRINT DEBUG OUTPUT IF WE NEED IT -------------- */
    /* ------------------------------------------------------------ */ 

    /* Are we debugging? */
    $debug = $options->getValue(TP_DEBUG_ON);
    if ($debug == true
        && isset($_GET[TP_DEBUG_PARAM]) 
        && ($_GET[TP_DEBUG_PARAM] == true)) {
            $newcontent .= tp_debug($options);
    }
    
    /* ------------------------------------------------------------ */
    /* ------------ NOW THE FUN PART ------------------------------ */
    /* ------------------------------------------------------------ */ 

    switch (TubePressStatic::determineNextAction($options)) {
        case "SINGLEVIDEO":
            $newcontent .= TubePressGallery::printHTML_singleVideo($options);
            break;
        default:
            $result = TubePressGallery::generate($options);
            $newcontent .= PEAR::isError($result)?
                TubePressStatic::bail(_tpMsg("GALERR"), $result) :
                $result;
            break;
    }

    /* We're done! Replace the tag with our new content */
    return str_replace($options->tagString, $newcontent, $content);
}

/* don't forget to add our hooks! */
add_tubepress_hooks();

/**
 * Adds the WordPress hooks. Simple!
 */
function add_tubepress_hooks()
{	
    add_filter('the_content', 'tp_main');
    add_action('admin_menu',  'tp_executeOptionsPage');    
    add_action('wp_head',     'tp_insertCSSJS');
    
    /* add ThickBox or LightWindow, if we need them */
    $quickOpts = get_option(TP_OPTION_NAME);

    if ($quickOpts != NULL) {

        switch ($quickOpts[TP_OPT_PLAYIN]->getValue()) {
            case TP_PLAYIN_THICKBOX:
                add_action('wp_head', 'tp_insertThickBox');
                break;
            case TP_PLAYIN_LWINDOW:
                add_action('wp_head', 'tp_insertLightWindow');
                break;
            default:
        }
    }   
}


?>
