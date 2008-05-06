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
 * A gallery of a user's "favorite" videos from YouTube
 */
class TubePressFavoritesGallery extends TubePressGallery 
    implements TubePressHasValue
{
    
    private $_user;
    
    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->setName(TubePressGalleryValue::FAVORITES);
        $this->setTitle("This YouTube user's \"favorites\"");
        $this->setDescription("YouTube limits this mode to the " . \
            "latest 500 favorites");
        $this->_user = 
            new TubePressTextValue(TubePressGalleryValue::FAVORITES . \
            "Value", "mrdeathgod");
    }
    
    /**
     * Defines where to fetch this gallery's feed
     * 
     * @return string The location of this gallery's feed from YouTube 
     */
    protected final function getRequestURL()
    {
        return "http://gdata.youtube.com/feeds/api/users/"
            . $this->getValue()->getCurrentValue() . "/favorites";
    }
    
    /**
     * Returns the current user for which we're retrieving favorites
     * 
     * @return TubePressTextValue The YouTube user whose favorites we're
     *                             fetching 
     */
    public function &getValue()
    {
        return $this->_user;
    }
}
