<?php
/**
 * TubePressDataPackage.php
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

class_exists("PEAR")
    || require(dirname(__FILE__) . "/../../../lib/PEAR/PEAR.php");

/**
 * This is another "abstract" class that holds an array of items
 */
class TubePressDataPackage
{
    /**
     * This is our array of items. Should never be accessed directly from
     * the outside
     */
    var $_dataArray;

    /**
     * Constructor
     */
    function TubePressDataPackage()
    {
		die("TubePressDataPackage is an abstract class");
    }
    
    /**
     * Checks to see if parameter appears to be a correct set of options
     */
    function checkValidity()
    {
        /* make sure the db looks ok */
        if ($this->_dataArray == NULL) {
            return PEAR::raiseError("Database options are completely missing.");
        }
        
        if (!is_array($this->_dataArray)) {
            return PEAR::raiseError(
                sprintf("Database options appear to be of type '%s' instead of an array.",
                gettype($this->_dataArray)));
        }

        $validTypes = $this->getValidTypes();
        $modelItems = $this->getNames();
        
        foreach ($modelItems as $defaultItem) {
            
            /* Make sure we have all the keys */
            if (!array_key_exists($defaultItem, $this->_dataArray)) {
                return PEAR::raiseError(
                	vsprintf("Database is missing the '%s' option. You have %s out of " .
                 		 "%s options stored. Perhaps you need to initialize your database?",
                    	array($defaultItem, 
                        	count($this->_dataArray), count($modelItems))));
            }

            /* Make sure each entry is a valid type */
			$found = false;
            foreach ($validTypes as $type) {
				if (is_a($this->_dataArray[$defaultItem], $type)) {
					$found = true;
				}
			}
            
         	if (!$found) {
         	    return PEAR::raiseError("You have options that are not current TubePressOptions");
         	}
        }
        
        /* finally, make sure that we have the right number of items */
        if (count($this->_dataArray) != count($modelItems)) {
            return PEAR::raiseError("You have extra items in this package! Expecting " . 
                count($modelItems)
                . " but you seem to have " . count($this->_dataArray));
        }
        return NULL;
    }

    
    function getNames()
    {
   		die("Can't call \"getNames\" from TubePressDataPackage");
    }
    
    function getValidTypes()
    {
   	    die("Can't call \"getValidTypes\" from TubePressDataPackage");
    }
   
    /**
     * Tries to get a single option from this package. Returns
     * error if the option is not part of the package.
     */
    function &get($name)
    {
        if ((!array_key_exists($name, $this->_dataArray))
            || (!is_a($this->_dataArray[$name], "TubePressDataItem"))) {
            return PEAR::raiseError(
            	sprintf("%s is not a valid option", $name));
        }
        return $this->_dataArray[$name];
    }
}
?>
