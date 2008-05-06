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
 * A gallery with videos from some playlist
 *
 */
class TubePressPlaylistGallery extends TubePressGallery implements TubePressHasValue
{
    
    private $_playlistId;
    
    /**
     * Default constructor
     *
     */
    public function __construct() {
        $this->setName(TubePressGalleryValue::PLAYLIST);
        $this->setTitle("This playlist");
        $this->setDescription("Limited to 200 videos per playlist." .
            " Will usually look something like this:" .
            " D2B04665B213AE35. Copy the playlist id from the end of the " .
            "URL in your browser's address bar (while looking at a YouTube " .
            "playlist). It comes right after the 'p='. For instance: " .
            "http://youtube.com/my_playlists?p=D2B04665B213AE35");
        $this->_playlistId = new TubePressTextValue(TubePressGalleryValue::PLAYLIST . "Value", "D2B04665B213AE35");
    }
    
    /**
     * Defines where to fetch this gallery's feed
     * 
     * @return string The location of this gallery's feed from YouTube 
     */
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/playlists/" . \
        	$this->getValue()->getCurrentValue();
    }
    
    /**
     * Gets the current playlist ID
     *
     * @return TubePressTextValue The value of the current playlist ID
     */
    public function &getValue() {
        return $this->_playlistId;
    }
}

