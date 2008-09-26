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

/**
 * Holds the current options for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a tag string
 */
class TubePressOptionsManager
{
    /**
     * Enter description here...
     *
     * @var array
     */
    private $_customOptions = array();
    
    /**
     * Enter description here...
     *
     * @var TubePressStorageManager
     */
    private $_tpsm;
    
    /**
     * The tag string currently in use
     *
     * @var string
     */
    private $_tagString;
    
    /**
     * Constructor
     *
     * @param TubePressStorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public function __construct(TubePressStorageManager $tpsm)
    {
        $this->_tpsm = $tpsm;
    }
    
    /**
     * Gets the value of an option
     *
     * @param string $optionName The name of the option
     * 
     * @return unknown The option value
     */
    public function get($optionName)
    {
        if (array_key_exists($optionName, $this->_customOptions)) {
            return $this->_customOptions[$optionName];
        }
        return $this->_tpsm->get($optionName);
    }
    
    /**
     * Enter description here...
     *
     * @param array $customOpts Custom options
     * 
     * @return void
     */
    public function setCustomOptions(array $customOpts)
    {
        $this->_customOptions = array_merge($this->_customOptions, $customOpts);
    }
    
    /**
     * Enter description here...
     *
     * @param string $newTagString The new tag string
     * 
     * @return void
     */
    public function setTagString($newTagString)
    {
        $this->_tagString = $newTagString;
    }
    
    /**
     * Enter description here...
     *
     * @return string The full tag string
     */
    public function getTagString()
    {
        return $this->_tagString;
    }
    
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
