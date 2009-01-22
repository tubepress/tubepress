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

if (version_compare(PHP_VERSION, '5.0.0', '>=')
        && !function_exists("tubepress_content_filter")) {
        	
	load_plugin_textdomain('tubepress', 'wp-content/plugins/tubepress/i18n');
    include dirname(__FILE__) . '/../functions/main.php';

	add_filter('the_content', 'tubepress_content_filter');
	add_action('wp_head',     'tubepress_head_filter');
}

?>