<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.org
Description: Display configurable YouTube galleries in your posts and/or pages
Author: Eric D. Hough
Version: 1.6.0-svn
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

/* load up the options page */
function_exists("tp_executeOptionsPage")
	|| require("env/WordPress/TubePressOptions.php");

/* load up the class loader */
function_exists("tp_classFolder") || require("tubepress_classloader.php");
    
isset($tubepress_base_url)
    || $tubepress_base_url = get_settings('siteurl') . "/wp-content/plugins/tubepress";
    
load_plugin_textdomain("tubepress", 'wp-content/plugins/tubepress/common/messages');
    
/* only load the rest if they have PHP5 installed and we haven't already loaded */
if (substr(phpversion(), 0, 1) == "5" && !function_exists("tp_main")) {
	include("tubepress_main.php");
}

/* adds the options page */
add_action('admin_menu',  'tp_executeOptionsPage');
?>
