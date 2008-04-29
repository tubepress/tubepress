<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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

require_once(dirname(__FILE__) . "/../../tubepress_classloader.php");
    
    /**
     * This is where the fun stuff happens
     */
    function __tp_executeOptionsPage()
    {
		/* initialize the database if we need to */
        WordPressStorage_v160::initDB();

        /* see what we've got in the db */
        $stored = get_option("tubepress");
        if (!($stored instanceof TubePressStorage_v160)) {
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
