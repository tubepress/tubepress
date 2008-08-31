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
 * Parent class of all TubePress galleries
 */
class TubePressGallery extends AbstractTubePressGallery
{
    const FAVORITES       = "favorites";
    const FEATURED        = "featured";
    const MOBILE          = "mobile";
    const MOST_DISCUSSESD = "most_discussed";
    const MOST_LINKED     = "most_linked";
    const MOST_RECENT     = "most_recent";
    const MOST_RESPONDED  = "most_responded";
    const PLAYLIST        = "playlist";
    const POPULAR         = "most_viewed";
    const TAG             = "tag";
    const TOP_RATED       = "top_rated";
    const USER            = "user";
    
    /**
     * Generates the content of this gallery
     * 
     * @param TubePressOptionsManager $tpom The TubePress options 
     *        manager containing all the user's options
     * 
     * @return The HTML content for this gallery
     */
    public final function generate(TubePressOptionsManager $tpom)
    {
    	try {
     	    return parent::generateThumbs("gallery.tpl.html", $tpom);
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
}