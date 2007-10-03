<?php
/**
 * TubePressDataPackage.php
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress
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
 */

/**
 * This is another abstract class that holds an array of items
 * and supports some basic functions on those items
 */
abstract class TubePressDataPackage
{
    /**
     * This is our array of items. Should never be accessed directly from
     * the outside
     */
    protected $_dataArray = array();
    private abstract $_validTypes;
    
    /**
     * Checks to see if parameter appears to be a correct set of options
     */
    public final function checkValidity()
    {
        /* make sure the db looks ok */
        if ($this->_dataArray == NULL) {
           throw new Exception("Database options are completely missing.");
        }
        
        if (!is_array($this->_dataArray)) {
            throw new Exception(
                sprintf("Database options appear to be of type '%s' instead of an array.",
                gettype($this->_dataArray)));
        }

        $validTypes = $this->getValidTypes();
        $modelItems = $this->getNames();
        
        foreach ($modelItems as $defaultItem) {
            
            /* Make sure we have all the keys */
            if (!array_key_exists($defaultItem, $this->_dataArray)) {
                throw new Exception(
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
         	    throw new Exception("You have options that are not current TubePressOptions");
         	}
        }
        
        /* finally, make sure that we have the right number of items */
        if (count($this->_dataArray) != count($modelItems)) {
            throw new Exception("You have extra items in this package! Expecting " . 
                count($modelItems)
                . " but you seem to have " . count($this->_dataArray));
        }
        return NULL;
    }

    
    public abstract function getNames();
    
    public abstract function getValidTypes();
   
    /**
     * Tries to get a single option from this package. Returns
     * error if the option is not part of the package.
     */
    public final function &get($name)
    {
        if ((!array_key_exists($name, $this->_dataArray))
            || (!is_a($this->_dataArray[$name], "TubePressDataItem"))) {
            throw new Exception(
            	sprintf("%s is not a valid option", $name));
        }
        return $this->_dataArray[$name];
    }
    
    public function setValue($name, $value) {
    	if (is_array_key($name, $this->_dataArray)
    	    && is_a($value, )) {
    		$this->_dataArray[$name]->setValue($modeValue);
    	} else {
    		throw new Exception($modeName . " is not a valid mode");
    	}
    }
}
?>
