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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../classes/tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_ioc_DefaultIocService'));

/**
 * This is where the fun stuff happens
 */
function __tp_executeOptionsPage()
{
	try {
	    $iocContainer = new org_tubepress_ioc_DefaultIocService();
	    $wpsm = $iocContainer->get(org_tubepress_ioc_IocService::STORAGE);
	    $wpsm->init();
	    
	    $optionsForm = $iocContainer->get(org_tubepress_ioc_IocService::FORM_HNDLER);
	        
	    /* are we updating? */
	    if (isset($_POST['tubepress_save'])) {
	        try {
	            $optionsForm->collect($_POST);
	            echo '<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>';
	        } catch (Exception $error) {
	            echo '<div id="message" class="error fade"><p><strong>' . 
	                $error->getMessage() . '</strong></p></div>';
	        }
	    }
	    $optionsForm->display();
	} catch (Exception $e) {
		print $e->getMessage();
	}
}
?>
