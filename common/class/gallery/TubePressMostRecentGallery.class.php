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
 * A gallery with the most-recently-added videos to YouTube
 *
 */
class TubePressMostRecentGallery extends TubePressGallery
{

	/**
	 * Default constructor
	 *
	 */
    public function __construct()
    {
        $this->setName(TubePressGalleryValue::most_recent);
        $this->setTitle("Most recently added");
    }
     
    /**
     * Defines where to fetch this gallery's feed
     * 
     * @return string The location of this gallery's feed from YouTube 
     */
    protected final function getRequestURL()
    {
        return "http://gdata.youtube.com/feeds/api/standardfeeds/most_recent";
    }
}

