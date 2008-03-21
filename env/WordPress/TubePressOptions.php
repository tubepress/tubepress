<?php
/**
 * TubePressOptions.php
 * 
 * Handles printing out the WordPress options page for TubePress
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once(dirname(__FILE__) . "/../../tubepress_classloader.php");
    
    /**
     * This is the main method for the TubePress global options page,
     * which is loaded when you're in the wp-admin section of your blog.
     * It basically just loads _tp_executeOptionsPage()
     */
    function tp_executeOptionsPage()
    {
        if (function_exists('add_options_page')) {
            add_options_page("TubePress Options", "TubePress", 9, 
                'TubePressOptions.php', '_tp_executeOptionsPage');
        }
    }
    
    /**
     * This is where the fun stuff happens
     */
    function _tp_executeOptionsPage()
    {
		/* initialize the database if we need to */
        WordPressStorage_v157::initDB();

        /* see what we've got in the db */
        $stored = get_option("tubepress");
        if (!($stored instanceof TubePressStorage_v157)) {
            throw new Exception("Problem retrieving options from database");
        }

        /* are we updating? */
        if (isset($_POST['tubepress_save'])) {
        	try {
	            TubePressOptionsForm::collect($stored, $_POST);
	            update_option("tubepress", $stored);
	            echo '<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>';
        	} catch (Exception $error) {
        		echo '<div id="message" class="error fade"><p><strong>' . $error->getMessage() . '</strong></p></div>';
        	}
        }
        
        $newOptions = get_option("tubepress");
        
        TubePressOptionsForm::display($newOptions);
    }
?>
