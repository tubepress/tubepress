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
 * Holds the current options for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a shortcode
 */
abstract class AbstractTubePressOptionsManager implements TubePressOptionsManager
{
    public static function getAllOptionNames() {
    	
    	$allCategories = array("TubePressAdvancedOptions", "TubePressDisplayOptions",
    	    "TubePressEmbeddedOptions", "TubePressGalleryOptions",
    	    "TubePressMetaOptions", "TubePressWidgetOptions");
    	
    	$allOpts = array();
    	foreach ($allCategories as $category) {
    		$class = new ReflectionClass($category);
    		foreach ($class->getConstants() as $constant) {
    			array_push($allOpts, $constant);
    		}
    	}
    	
    	return $allOpts;
    }
}
