<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.org
Description: Displays gorgeous YouTube and Vimeo galleries in your posts, pages, and/or sidebar. Upgrade to <a href="http://tubepress.org/download/">TubePress Pro</a> for more features!
Author: Eric D. Hough
Version: 2.1.2
Author URI: http://ehough.com

Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)

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

if (version_compare(PHP_VERSION, '5.0.2', '>=') && !class_exists('org_tubepress_env_wordpress_Main')) {

    global $tubepress_base_url;
    
    /* have to consider that sometimes people may name the "tubepress" directory differently */
    $dirName = dirname(__FILE__);
    $baseName = basename(realpath($dirName));

    include "$dirName/classes/org/tubepress/env/wordpress/Main.class.php";
    include "$dirName/classes/org/tubepress/env/wordpress/Admin.class.php";
    include "$dirName/classes/org/tubepress/env/wordpress/Widget.class.php";

    /* set the tubepress_base_url global */
    $tubepress_base_url = get_option('siteurl') . "/wp-content/plugins/$baseName";        

    /* register the plugin's message bundles */
    load_plugin_textdomain('tubepress', false, "$baseName/i18n");

    add_filter('the_content',  array('org_tubepress_env_wordpress_Main',   'contentFilter'));
    add_action('wp_head',      array('org_tubepress_env_wordpress_Main',   'headAction'));
    add_action('init',         array('org_tubepress_env_wordpress_Main',   'initAction'));

    add_action('admin_menu',   array('org_tubepress_env_wordpress_Admin',  'menuAction'));
    add_action('admin_head',   array('org_tubepress_env_wordpress_Admin',  'headAction'));
    add_action('admin_init',   array('org_tubepress_env_wordpress_Admin',  'initAction'));

    add_action('widgets_init', array('org_tubepress_env_wordpress_Widget', 'initAction'));
}

?>
