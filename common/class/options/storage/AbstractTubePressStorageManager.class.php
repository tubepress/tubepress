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
 * Handles persistent storage of TubePress options
 *
 */
abstract class AbstractTubePressStorageManager implements TubePressStorageManager
{   
	private $_validationService;
	
    /**
     * Creates an option in storage
     *
     * @param unknown_type $optionName  The name of the option to create
     * @param unknown_type $optionValue The default value of the new option
     * 
     * @return void
     */
    protected abstract function create($optionName, $optionValue);    
    
    /**
     * Print out debugging info for this
     * storage manager
     *
     * @return void
     */
    public final function debug()
    {
    	$allOpts = AbstractTubePressOptionsManager::getAllOptionNames();
        
        $result = "Should have " . sizeof($allOpts) . " options total";
        
        $result .= "<ol>";
        foreach ($allOpts as $opt) {
            if ($this->exists($opt)) {
                $result .= "<li><font color=\"green\">" .
                    "$opt exists and its value is \"" . $this->get($opt) .
                    "\"</font></li>";
            } else {
                $result .= "<li><font color=\"red\">" .
                    "$opt does not exist!</font></li>";
            }
            
        }
        $result .= "</ol>";
        return $result;
    }    
    
    /**
     * Deletes an option from storage
     *
     * @param unknown_type $optionName The name of the option to delete
     * 
     * @return void
     */
    protected abstract function delete($optionName);    
     
    /**
     * Initialize the persistent storage
     * 
     * @return void
     */
    public final function init()
    {
    	$vals = array(
    		TubePressAdvancedOptions::DATEFORMAT 		 => "M j, Y",
    		TubePressAdvancedOptions::DEBUG_ON 			 => true,
    		TubePressAdvancedOptions::FILTER 			 => false,
    		TubePressAdvancedOptions::CACHE_ENABLED 	 => true,
    		TubePressAdvancedOptions::NOFOLLOW_LINKS 	 => true,
    		TubePressAdvancedOptions::KEYWORD 			 => "tubepress",
    		TubePressAdvancedOptions::RANDOM_THUMBS 	 => true,
    		TubePressAdvancedOptions::CLIENT_KEY 		 => "ytapi-EricHough-TubePress-ki6oq9tc-0",
    		TubePressAdvancedOptions::RANDOM_THUMBS 	 => true,
    		TubePressAdvancedOptions::CLIENT_KEY 		 => "ytapi-EricHough-TubePress-ki6oq9tc-0",
    		TubePressAdvancedOptions::DEV_KEY 			 => "AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg",
    		TubePressDisplayOptions::CURRENT_PLAYER_NAME => "normal",
    		TubePressDisplayOptions::DESC_LIMIT 		 => 80,
    		TubePressDisplayOptions::ORDER_BY 			 => "viewCount",
    		TubePressDisplayOptions::RELATIVE_DATES 	 => false,
    		TubePressDisplayOptions::RESULTS_PER_PAGE 	 => 20,
    		TubePressDisplayOptions::THUMB_HEIGHT 		 => 90,
    		TubePressDisplayOptions::THUMB_WIDTH 		 => 120,
    		TubePressEmbeddedOptions::AUTOPLAY 			 => false,
    		TubePressEmbeddedOptions::BORDER 			 => false,
    		TubePressEmbeddedOptions::EMBEDDED_HEIGHT 	 => 355,
    		TubePressEmbeddedOptions::EMBEDDED_WIDTH 	 => 425,
    		TubePressEmbeddedOptions::GENIE 			 => false,
    		TubePressEmbeddedOptions::LOOP 				 => false,
    		TubePressEmbeddedOptions::PLAYER_COLOR 		 => "/",
    		TubePressEmbeddedOptions::SHOW_RELATED 		 => true,
    		TubePressGalleryOptions::MODE 				 => "featured",
    		TubePressGalleryOptions::FAVORITES_VALUE 	 => "mrdeathgod",
    		TubePressGalleryOptions::MOST_VIEWED_VALUE 	 => "today",
    		TubePressGalleryOptions::PLAYLIST_VALUE 	 => "D2B04665B213AE35",
    		TubePressGalleryOptions::TAG_VALUE 			 => "stewart daily show",
    		TubePressGalleryOptions::TOP_RATED_VALUE 	 => "today",
    		TubePressGalleryOptions::USER_VALUE 		 => "3hough",
    		TubePressMetaOptions::AUTHOR 				 => false,
    		TubePressMetaOptions::CATEGORY 				 => false,
    		TubePressMetaOptions::DESCRIPTION 			 => false,
    		TubePressMetaOptions::ID 					 => false,
    		TubePressMetaOptions::LENGTH 				 => true,
    		TubePressMetaOptions::RATING 				 => false,
    		TubePressMetaOptions::RATINGS 				 => false,
    		TubePressMetaOptions::TAGS 					 => false,
    		TubePressMetaOptions::TITLE 				 => true,
    		TubePressMetaOptions::UPLOADED 				 => false,
    		TubePressMetaOptions::URL 					 => false,
    		TubePressMetaOptions::VIEWS 				 => true,
    		TubePressWidgetOptions::TITLE 				 => "TubePress",
    		TubePressWidgetOptions::TAGSTRING 			 => "[tubepress thumbHeight='105', thumbWidth='135']"
    	);
    	
    	foreach($vals as $val => $key) {
    		$this->_init($val, $key);
    	}
    }    

    private function _init($name, $value)
    {
    	if (!$this->exists($name)) {
    		$this->delete($name);
    		$this->create($name, $value);
    	}
    }
    
    /**
     * Sets an option value
     *
     * @param string       $optionName  The option name
     * @param unknown_type $optionValue The option value
     * 
     * @return void
     */
    public final function set($optionName, $optionValue)
    {
        $this->_validationService->validate($optionName, $optionValue);
        $this->setOption($optionName, $optionValue);
    }    
    
    /**
     * Sets an option to a new value, without validation
     *
     * @param string       $optionName  The name of the option to update
     * @param unknown_type $optionValue The new option value
     * 
     * @return void
     */
    protected abstract function setOption($optionName, $optionValue);
    
    /**
     * Set the TubePressInputValidationService
     *
     * @param TubePressInputValidationService $validationService The validation service
     */
    public function setValidationService(TubePressInputValidationService $validationService)
    {
    	$this->_validationService = $validationService;
    }
}
