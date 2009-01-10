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
 * Implementation of TubePressStorageManager that uses the
 * regular WordPress options API
 *
 */
class WordPressStorageManager extends AbstractTubePressStorageManager
{
	const OPTION_PREFIX = "tubepress-";
	
    /**
     * Sets an option to a new value, without validation
     *
     * @param string       $optionName  The name of the option to update
     * @param unknown_type $optionValue The new option value
     * 
     * @return void
     */
    protected function setOption($optionName, $optionValue)
    {
        update_option(WordPressStorageManager::OPTION_PREFIX . $optionName,
            $optionValue);
    }
    
    /**
     * Retrieve the current value of an option
     *
     * @param string $optionName The name of the option
     * 
     * @return unknown_type The option's value
     */
    public function get($optionName)
    {
        return get_option(WordPressStorageManager::OPTION_PREFIX . $optionName);
    }
    
    /**
     * Deletes an option from storage
     *
     * @param unknown_type $optionName The name of the option to delete
     * 
     * @return void
     */
    protected function delete($optionName)
    {
        delete_option(WordPressStorageManager::OPTION_PREFIX . $optionName);
    }
    
    /**
     * Creates an option in storage
     *
     * @param unknown_type $optionName  The name of the option to create
     * @param unknown_type $optionValue The default value of the new option
     * 
     * @return void
     */
    protected function create($optionName, $optionValue)
    {
        add_option(WordPressStorageManager::OPTION_PREFIX . $optionName,
            $optionValue);
    }
    
    /**
     * Determines if an option exists
     *
     * @param string $optionName The name of the option in question
     * 
     * @return boolean True if the option exists, false otherwise
     */
    public function exists($optionName)
    {
        return get_option(WordPressStorageManager::OPTION_PREFIX . $optionName)
           !== false;
    }
    
    public function nuclear()
    {
    	$allOptions = get_alloptions();
    	foreach ($allOptions as $key => $value) {
    		if (preg_match("/^tubepress.*/", $key)) {
    			delete_option($key);
    		}
    	}
    	$this->init();
    }
}
?>
