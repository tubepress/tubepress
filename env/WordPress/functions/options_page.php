<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Adds TubePress to the admin menu
 * 
 * @return void
 */
function tubepress_admin_menu_hook()
{
    if (function_exists('add_options_page')) {
        add_options_page("TubePress Options", "TubePress", 9, 
            __FILE__, '_tubepress_admin_menu_hook');
    }
}

/**
 * Enter description here...
 * 
 * @return void
 */
function _tubepress_admin_menu_hook()
{
	if (version_compare(PHP_VERSION, '5.0.0', '>=')) {
    	function_exists("__tp_executeOptionsPage")
    	    || require "options_page_php5.php";  
        __tp_executeOptionsPage();
    } else {
        print <<<EOT
<div id="message" class="error fade">
    <p>
        <strong>
            This version of TubePress requires PHP5 or higher. 
            Please <a href="http://php.net">upgrade your PHP installation</a> 
            or visit <a href="http://tubepress.org">tubepress.org</a> to obtain 
            a different version of the plugin.
        </strong>
    </p>
</div>
EOT
    ;
    }
}

/**
 * Enter description here...
 * 
 * @return void
 */
function tubepress_admin_head_hook()
{
    global $tubepress_base_url;
    $jsColorFile = "$tubepress_base_url/ui/options_page/js/jscolor/jscolor.js";
    echo "<script type=\"text/javascript\" src=\"$jsColorFile\"></script>";
}

function tubepress_admin_init_hook()
{
    $dirName = basename(realpath(dirname(__FILE__) . '/../../..'));
	wp_enqueue_style('jquery-ui-flick', WP_PLUGIN_URL . "/$dirName/ui/options_page/css/flick/jquery-ui-1.7.2.custom.css");
	wp_enqueue_script('jquery-ui-tabs');
}
