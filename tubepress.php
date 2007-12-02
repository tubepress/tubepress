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

    try {
    
        if (!tp_shouldWeExecute($content)) {
            return $content;
        }

        //WordPressStorageBox::applyTag($keyword->getValue(), $content, $stored, $stored->options);
    
        /* ------------------------------------------------------------ */
        /* ------------ NOW THE FUN PART ------------------------------ */
        /* ------------------------------------------------------------ */ 
        
        $stored = get_option("tubepress");
        
        /* printing a single video only? */
        $player = $stored->getDisplayOptions()->get(TubePressDisplayOptions::currentPlayerName)->getValue()->getCurrentValue();
        $gallery = $stored->getGalleryOptions()->get(TubePressGalleryOptions::mode)->getValue()->getCurrentValue();

        $newcontent .= $gallery->generate($stored);

    	/* replace the tag with our new content */
        return str_replace("[tubepress]", $newcontent, $content);
    
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
        $player = $stored->getDisplayOptions()->get(TubePressDisplayOptions::currentPlayerName)->getValue()->getCurrentValue();
        print $player->getHeadContents();
    } catch (Exception $error) {
        /* this is in the head, so just print an HTML comment and proceed */
        print "<!-- " . $error->getMessage() . " -->";
    }
}

function tp_shouldWeExecute($content) {
    
    $stored = get_option("tubepress");
    
    if ($stored == NULL
        || !($stored instanceof TubePressStorage_v157)) {
        return false;
    }
    
    $trigger = $stored->getAdvancedOptions()->get(TubePressAdvancedOptions::triggerWord)->getValue()->getCurrentValue();
    
    if (strpos($content, '[' . $trigger) === false) {
        return false;
    }
    
    return true;
}

function __autoload($className) {

    $folder = tp_classFolder($className);
    
    if ($folder !== false) {
        require_once($folder . $className . ".class.php");
    } else {
        if (!class_exists($className, false)) {
            echo $className . " class not found <br />";
        }
    }
}
    
function tp_classFolder($className, $sub = DIRECTORY_SEPARATOR) {
        
    $currentDir = dirname(__FILE__);
        
    $dir = dir($currentDir . $sub);
        
    if (file_exists($currentDir.$sub.$className.".class.php")) {
        return $currentDir.$sub;
    }
    
    while (false !== ($folder = $dir->read())) {
            
        if (strpos($folder, ".") === 0) {
            continue;
        }
            
        if (is_dir($currentDir.$sub.$folder)) {
            $subFolder = tp_classFolder($className, $sub.$folder.DIRECTORY_SEPARATOR);
                    
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
