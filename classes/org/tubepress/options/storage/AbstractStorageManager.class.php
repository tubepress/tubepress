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
 * Handles persistent storage of TubePress options
 *
 */
abstract class org_tubepress_options_storage_AbstractStorageManager implements org_tubepress_options_storage_StorageManager
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
    	$allOpts = org_tubepress_options_manager_AbstractOptionsManager::getAllOptionNames();
        
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
    		org_tubepress_options_category_Advanced::DATEFORMAT 		 => "M j, Y",
    		org_tubepress_options_category_Advanced::DEBUG_ON 			 => true,
    		org_tubepress_options_category_Advanced::FILTER 			 => false,
    		org_tubepress_options_category_Advanced::CACHE_ENABLED 	 => true,
    		org_tubepress_options_category_Advanced::NOFOLLOW_LINKS 	 => true,
    		org_tubepress_options_category_Advanced::KEYWORD 			 => "tubepress",
    		org_tubepress_options_category_Advanced::RANDOM_THUMBS 	 => true,
    		org_tubepress_options_category_Advanced::CLIENT_KEY 		 => "ytapi-EricHough-TubePress-ki6oq9tc-0",
    		org_tubepress_options_category_Advanced::RANDOM_THUMBS 	 => true,
    		org_tubepress_options_category_Advanced::CLIENT_KEY 		 => "ytapi-EricHough-TubePress-ki6oq9tc-0",
    		org_tubepress_options_category_Advanced::DEV_KEY 			 => "AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg",
    		org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => "normal",
    		org_tubepress_options_category_Display::DESC_LIMIT 		 => 80,
    		org_tubepress_options_category_Display::ORDER_BY 			 => "viewCount",
    		org_tubepress_options_category_Display::RELATIVE_DATES 	 => false,
    		org_tubepress_options_category_Display::RESULTS_PER_PAGE 	 => 20,
    		org_tubepress_options_category_Display::THUMB_HEIGHT 		 => 90,
    		org_tubepress_options_category_Display::THUMB_WIDTH 		 => 120,
    		org_tubepress_options_category_Embedded::AUTOPLAY 			 => false,
    		org_tubepress_options_category_Embedded::BORDER 			 => false,
    		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT 	 => 355,
    		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH 	 => 425,
    		org_tubepress_options_category_Embedded::GENIE 			 => false,
    		org_tubepress_options_category_Embedded::LOOP 				 => false,
    		org_tubepress_options_category_Embedded::PLAYER_COLOR 		 => "/",
    		org_tubepress_options_category_Embedded::SHOW_RELATED 		 => true,
    		org_tubepress_options_category_Embedded::QUALITY            => "normal",
    		org_tubepress_options_category_Gallery::MODE 				 => "recently_featured",
    		org_tubepress_options_category_Gallery::FAVORITES_VALUE 	 => "mrdeathgod",
    		org_tubepress_options_category_Gallery::MOST_VIEWED_VALUE 	 => "today",
    		org_tubepress_options_category_Gallery::PLAYLIST_VALUE 	 => "D2B04665B213AE35",
    		org_tubepress_options_category_Gallery::TAG_VALUE 			 => "stewart daily show",
    		org_tubepress_options_category_Gallery::TOP_RATED_VALUE 	 => "today",
    		org_tubepress_options_category_Gallery::USER_VALUE 		 => "3hough",
    		org_tubepress_options_category_Meta::AUTHOR 				 => false,
    		org_tubepress_options_category_Meta::CATEGORY 				 => false,
    		org_tubepress_options_category_Meta::DESCRIPTION 			 => false,
    		org_tubepress_options_category_Meta::ID 					 => false,
    		org_tubepress_options_category_Meta::LENGTH 				 => true,
    		org_tubepress_options_category_Meta::RATING 				 => false,
    		org_tubepress_options_category_Meta::RATINGS 				 => false,
    		org_tubepress_options_category_Meta::TAGS 					 => false,
    		org_tubepress_options_category_Meta::TITLE 				 => true,
    		org_tubepress_options_category_Meta::UPLOADED 				 => false,
    		org_tubepress_options_category_Meta::URL 					 => false,
    		org_tubepress_options_category_Meta::VIEWS 				 => true,
    		org_tubepress_options_category_Widget::TITLE 				 => "TubePress",
    		org_tubepress_options_category_Widget::TAGSTRING 			 => "[tubepress thumbHeight='105', thumbWidth='135']"
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
     * Set the org_tubepress_options_validation_InputValidationService
     *
     * @param org_tubepress_options_validation_InputValidationService $validationService The validation service
     */
    public function setValidationService(org_tubepress_options_validation_InputValidationService $validationService)
    {
    	$this->_validationService = $validationService;
    }
}
