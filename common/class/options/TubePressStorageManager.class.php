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
abstract class TubePressStorageManager
{
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
        TubePressValidator::validate($optionName, $optionValue);
        $this->setOption($optionName, $optionValue);
    }
    
    /**
     * Initializes the storage
     *
     * @return void
     */
    public final function init()
    {
        /* first do the advanced options */
        if (!$this->exists(TubePressAdvancedOptions::DATEFORMAT)) {
            $this->delete(TubePressAdvancedOptions::DATEFORMAT);
            $this->create(TubePressAdvancedOptions::DATEFORMAT, "M j, Y");
        }
        
        if (!$this->exists(TubePressAdvancedOptions::DEBUG_ON)) {
            $this->delete(TubePressAdvancedOptions::DEBUG_ON);
            $this->create(TubePressAdvancedOptions::DEBUG_ON, true);
        }
        
        if (!$this->exists(TubePressAdvancedOptions::FILTER)) {
            $this->delete(TubePressAdvancedOptions::FILTER);
            $this->create(TubePressAdvancedOptions::FILTER, false);
        }
        
        if (!$this->exists(TubePressAdvancedOptions::CACHE_ENABLED)) {
        	$this->delete(TubePressAdvancedOptions::CACHE_ENABLED);
        	$this->create(TubePressAdvancedOptions::CACHE_ENABLED, true);
        }
        
        if (!$this->exists(TubePressAdvancedOptions::KEYWORD)) {
            $this->delete(TubePressAdvancedOptions::KEYWORD);
            $this->create(TubePressAdvancedOptions::KEYWORD, "tubepress");
        }
        
        if (!$this->exists(TubePressAdvancedOptions::RANDOM_THUMBS)) {
            $this->delete(TubePressAdvancedOptions::RANDOM_THUMBS);
            $this->create(TubePressAdvancedOptions::RANDOM_THUMBS, true);
        }
        
        if (!$this->exists(TubePressAdvancedOptions::CLIENT_KEY)) {
            $this->delete(TubePressAdvancedOptions::CLIENT_KEY);
            $this->create(TubePressAdvancedOptions::CLIENT_KEY, "ytapi-EricHough-TubePress-ki6oq9tc-0");
        }
        
        if (!$this->exists(TubePressAdvancedOptions::DEV_KEY)) {
            $this->delete(TubePressAdvancedOptions::DEV_KEY);
            $this->create(TubePressAdvancedOptions::DEV_KEY, "AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg");
        }
        
        /* now the display options */
        if (!$this->exists(TubePressDisplayOptions::CURRENT_PLAYER_NAME)) {
            $this->delete(TubePressDisplayOptions::CURRENT_PLAYER_NAME);
            $this->create(TubePressDisplayOptions::CURRENT_PLAYER_NAME, "normal");
        }
        
        if (!$this->exists(TubePressDisplayOptions::DESC_LIMIT)) {
        	$this->delete(TubePressDisplayOptions::DESC_LIMIT);
        	$this->create(TubePressDisplayOptions::DESC_LIMIT, 80);
        }
        
        if (!$this->exists(TubePressDisplayOptions::ORDER_BY)) {
            $this->delete(TubePressDisplayOptions::ORDER_BY);
            $this->create(TubePressDisplayOptions::ORDER_BY, "viewCount");
        }
        
        if (!$this->exists(TubePressDisplayOptions::RELATIVE_DATES)) {
            $this->delete(TubePressDisplayOptions::RELATIVE_DATES);
            $this->create(TubePressDisplayOptions::RELATIVE_DATES, false);
        }
        
        if (!$this->exists(TubePressDisplayOptions::RESULTS_PER_PAGE)) {
            $this->delete(TubePressDisplayOptions::RESULTS_PER_PAGE);
            $this->create(TubePressDisplayOptions::RESULTS_PER_PAGE, 20);
        }
        
        if (!$this->exists(TubePressDisplayOptions::THUMB_HEIGHT)) {
            $this->delete(TubePressDisplayOptions::THUMB_HEIGHT);
            $this->create(TubePressDisplayOptions::THUMB_HEIGHT, 90);
        }
        
        if (!$this->exists(TubePressDisplayOptions::THUMB_WIDTH)) {
            $this->delete(TubePressDisplayOptions::THUMB_WIDTH);
            $this->create(TubePressDisplayOptions::THUMB_WIDTH, 120);
        }
        
        /* now the embedded options */
        if (!$this->exists(TubePressEmbeddedOptions::AUTOPLAY)) {
            $this->delete(TubePressEmbeddedOptions::AUTOPLAY);
            $this->create(TubePressEmbeddedOptions::AUTOPLAY, false);
        }
        
        if (!$this->exists(TubePressEmbeddedOptions::BORDER)) {
            $this->delete(TubePressEmbeddedOptions::BORDER);
            $this->create(TubePressEmbeddedOptions::BORDER, false);
        }
        
        if (!$this->exists(TubePressEmbeddedOptions::EMBEDDED_HEIGHT)) {
            $this->delete(TubePressEmbeddedOptions::EMBEDDED_HEIGHT);
            $this->create(TubePressEmbeddedOptions::EMBEDDED_HEIGHT, 355);
        }
        
        if (!$this->exists(TubePressEmbeddedOptions::EMBEDDED_WIDTH)) {
            $this->delete(TubePressEmbeddedOptions::EMBEDDED_WIDTH);
            $this->create(TubePressEmbeddedOptions::EMBEDDED_WIDTH, 425);
        }
        
        if (!$this->exists(TubePressEmbeddedOptions::GENIE)) {
            $this->delete(TubePressEmbeddedOptions::GENIE);
            $this->create(TubePressEmbeddedOptions::GENIE, false);
        }
        
        if (!$this->exists(TubePressEmbeddedOptions::LOOP)) {
            $this->delete(TubePressEmbeddedOptions::LOOP);
            $this->create(TubePressEmbeddedOptions::LOOP, false);
        }
        
        if (!$this->exists(TubePressEmbeddedOptions::PLAYER_COLOR)) {
            $this->delete(TubePressEmbeddedOptions::PLAYER_COLOR);
            $this->create(TubePressEmbeddedOptions::PLAYER_COLOR, "/");
        }
        
        if (!$this->exists(TubePressEmbeddedOptions::SHOW_RELATED)) {
            $this->delete(TubePressEmbeddedOptions::SHOW_RELATED);
            $this->create(TubePressEmbeddedOptions::SHOW_RELATED, true);
        }
        
        /* gallery options */
        if (!$this->exists(TubePressGalleryOptions::MODE)) {
            $this->delete(TubePressGalleryOptions::MODE);
            $this->create(TubePressGalleryOptions::MODE, "featured");
        }
        
        if (!$this->exists(TubePressGalleryOptions::FAVORITES_VALUE)) {
            $this->delete(TubePressGalleryOptions::FAVORITES_VALUE);
            $this->create(TubePressGalleryOptions::FAVORITES_VALUE, "mrdeathgod");
        }
        
        if (!$this->exists(TubePressGalleryOptions::MOST_VIEWED_VALUE)) {
            $this->delete(TubePressGalleryOptions::MOST_VIEWED_VALUE);
            $this->create(TubePressGalleryOptions::MOST_VIEWED_VALUE, "today");
        }
        
        if (!$this->exists(TubePressGalleryOptions::PLAYLIST_VALUE)) {
            $this->delete(TubePressGalleryOptions::PLAYLIST_VALUE);
            $this->create(TubePressGalleryOptions::PLAYLIST_VALUE, 
                "D2B04665B213AE35");
        }
        
        if (!$this->exists(TubePressGalleryOptions::TAG_VALUE)) {
            $this->delete(TubePressGalleryOptions::TAG_VALUE);
            $this->create(TubePressGalleryOptions::TAG_VALUE, "stewart daily show");
        }
        
        if (!$this->exists(TubePressGalleryOptions::TOP_RATED_VALUE)) {
            $this->delete(TubePressGalleryOptions::TOP_RATED_VALUE);
            $this->create(TubePressGalleryOptions::TOP_RATED_VALUE, "today");
        }
        
        if (!$this->exists(TubePressGalleryOptions::USER_VALUE)) {
            $this->delete(TubePressGalleryOptions::USER_VALUE);
            $this->create(TubePressGalleryOptions::USER_VALUE, "3hough");
        }
        
        /* meta options */
        if (!$this->exists(TubePressMetaOptions::AUTHOR)) {
            $this->delete(TubePressMetaOptions::AUTHOR);
            $this->create(TubePressMetaOptions::AUTHOR, false);
        }
        
        if (!$this->exists(TubePressMetaOptions::CATEGORY)) {
            $this->delete(TubePressMetaOptions::CATEGORY);
            $this->create(TubePressMetaOptions::CATEGORY, false);
        }
        
        if (!$this->exists(TubePressMetaOptions::DESCRIPTION)) {
            $this->delete(TubePressMetaOptions::DESCRIPTION);
            $this->create(TubePressMetaOptions::DESCRIPTION, false);
        }
        
        if (!$this->exists(TubePressMetaOptions::ID)) {
            $this->delete(TubePressMetaOptions::ID);
            $this->create(TubePressMetaOptions::ID, false);
        }
        
        if (!$this->exists(TubePressMetaOptions::LENGTH)) {
            $this->delete(TubePressMetaOptions::LENGTH);
            $this->create(TubePressMetaOptions::LENGTH, true);
        }
        
        if (!$this->exists(TubePressMetaOptions::RATING)) {
            $this->delete(TubePressMetaOptions::RATING);
            $this->create(TubePressMetaOptions::RATING, false);
        }
        
        if (!$this->exists(TubePressMetaOptions::RATINGS)) {
            $this->delete(TubePressMetaOptions::RATINGS);
            $this->create(TubePressMetaOptions::RATINGS, false);
        }
        
        if (!$this->exists(TubePressMetaOptions::TAGS)) {
            $this->delete(TubePressMetaOptions::TAGS);
            $this->create(TubePressMetaOptions::TAGS, false);
        }
        
        if (!$this->exists(TubePressMetaOptions::TITLE)) {
            $this->delete(TubePressMetaOptions::TITLE);
            $this->create(TubePressMetaOptions::TITLE, true);
        }
        
        if (!$this->exists(TubePressMetaOptions::UPLOADED)) {
            $this->delete(TubePressMetaOptions::UPLOADED);
            $this->create(TubePressMetaOptions::UPLOADED, false);
        }
        
        if (!$this->exists(TubePressMetaOptions::URL)) {
            $this->delete(TubePressMetaOptions::URL);
            $this->create(TubePressMetaOptions::URL, false);
        }
        
        if (!$this->exists(TubePressMetaOptions::VIEWS)) {
            $this->delete(TubePressMetaOptions::VIEWS);
            $this->create(TubePressMetaOptions::VIEWS, true);
        }
        
        if (!$this->exists(TubePressWidgetOptions::TITLE)) {
        	$this->delete(TubePressWidgetOptions::TITLE);
        	$this->create(TubePressWidgetOptions::TITLE, "TubePress");
        }
        
    	if (!$this->exists(TubePressWidgetOptions::TAGSTRING)) {
        	$this->delete(TubePressWidgetOptions::TAGSTRING);
        	$this->create(TubePressWidgetOptions::TAGSTRING, "[tubepress thumbHeight='105', thumbWidth='135']");
        }
    }
    
    /**
     * Sets an option to a new value
     *
     * @param string       $optionName  The name of the option to update
     * @param unknown_type $optionValue The new option value
     * 
     * @return void
     */
    protected abstract function setOption($optionName, $optionValue);
    
    /**
     * Retrieve the current value of an option
     *
     * @param string $optionName The name of the option
     * 
     * @return unknown_type
     */
    public abstract function get($optionName);
    
    /**
     * Deletes an option from storage
     *
     * @param unknown_type $optionName The name of the option to delete
     * 
     * @return void
     */
    protected abstract function delete($optionName);
    
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
     * Determines if an option exists
     *
     * @param string $optionName The name of the option in question
     * 
     * @return boolean True if the option exists, false otherwise
     */
    public abstract function exists($optionName);
    
    /**
     * Enter description here...
     *
     * @return void
     */
    public function debug()
    {
    	$allOpts = TubePressOptionsManager::getAllOptionNames();
        
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
}
