<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

if (version_compare(PHP_VERSION, '5.0.0', '>=') && !function_exists("tubepress_content_filter")) {

    /* have to consider that sometimes people may name the "tubepress" directory differently */
    $dirName = basename(realpath(dirname(__FILE__) . '/../../..'));

    /* set the tubepress_base_url global */
    $tubepress_base_url = get_option('siteurl') . "/wp-content/plugins/$dirName";        

    /* register the plugin's message bundles */
    load_plugin_textdomain('tubepress', "wp-content/plugins/$dirName/i18n");

    /* load up the rest of the WordPress specific code */
    include dirname(__FILE__) . '/../functions/main.php';

    /* add a filter for all post/page content */
    add_filter('the_content', 'tubepress_content_filter');

    /* add a filter so we can add our CSS/JS to the head */
    add_action('wp_head',     'tubepress_head_filter');

    /* load up jQuery */
    add_action('init', 'tubepress_load_jquery');
}

?>
