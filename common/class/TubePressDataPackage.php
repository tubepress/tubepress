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

/**
 * This is meant to be an abstract class, though PHP 4 doesn't support
 * them :(. The idea here is that each implementation (WordPress, MoveableType)
 * extends this class and passes it around as the class that holds all 
 * of the users options. It's essentially just an array of TubePressOptions 
 * with some extra methods related to metadata on those options.
*/
class TubePressDataPackage
{
    /* this is our array of items */
    var $_dataArray;
    
    /* an array of class names that we're allowed to hold */
	var $_validTypes;

    /**
     * Default options
     */
    function TubePressOptionsPackage()
    {
		die "This is an abstract class";
    }
    
    /**
     * Checks to see if parameter appears to be a correct set of options
     * 
     * @param An array of the options that the user currently has
     * (typically pulled from the db)
     */
    function checkValidity()
    {
        /* make sure the db looks ok */
        if ($this->_dataArray == NULL) {
            return PEAR::raiseError(_tpMsg("NODB"));
        }
        if (!is_array($this->_dataArray)) {
            return PEAR::raiseError(_tpMsg("BADDB",
            array(gettype($this->_dataArray))));
        }
        
        $modelItems = array_keys($this->getDefaultPackage());
        
        foreach ($modelItems as $defaultItem) {
            /* Make sure we have all the keys */
            if (!array_key_exists($defaultItem, $this->_dataArray)) {
                return PEAR::raiseError(_tpMsg("DBMISS", 
                    array($defaultItem, 
                        count($this->_dataArray), count($modelItems))));
            }

            /* Make sure each entry is a valid type */
			$found = false;
            foreach ($this->_validTypes as $type) {
				if (is_a($this->_dataArray[$defaultItem], $type)) {
					$found = true;
				}
			}
         	if (!$found) {
         		return PEAR::raiseError(_tpMsg("OLDDB")
         	}
        }
        
        /* finally, make sure that we have the right number of items */
        if (count($this->_dataArray) != count($modelItems)) {
            return PEAR::raiseError("You have extra items in this package! Expecting " . 
                count($modelItems)
                . " but you seem to have " . count($this->_dataArray));
        }
    }
    
    /**
     * A wrapper for the item's getDescription()
     */
    function getDescription($name)
    {
        $result = $this->_get($name);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $result->getDescription();
   }
    
   function getNames()
   {
   		return array_keys($this->_dataArray);
   }
   
    /**
     * A wrapper for TubePressOption's getTitle()
     */
    function getTitle($name)
    {
        $result = $this->_get($name);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $result->getTitle();
    }
    
    /**
     * A wrapper for TubePressOption's getValue()
     */
    function getValue($name)
    {
        $result = $this->_get($name);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $result->getValue();
    }
    
    /**
     * Set a single option's value for this package. Returns error if
     * option does not exist, or invalid option value.
     */
    function setValue($name, $optionValue)
    {
        if (!array_key_exists($name, $this->_dataArray)) {
            return PEAR::raiseError(_tpMsg("NOSUCHOPT", array($name)));
        }
        
        $result = $this->_dataArray[$name]->setValue($optionValue);
        if (PEAR::isError($result)) {
            return $result;
        }
    }
    
    /**
     * Tries to get a single option from this package. Returns
     * error if the option is not part of the package.
     */
    function _get($name)
    {
        if ((!array_key_exists($name, $this->_dataArray))
            || (!is_a($this->_dataArray[$name], "TubePressOption"))) {
            return PEAR::raiseError(_tpMsg("NOSUCHOPT", array($name)));
        }
        return $this->_dataArray[$name];
    }
}
?>
