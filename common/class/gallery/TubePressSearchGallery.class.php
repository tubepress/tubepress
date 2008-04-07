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
class TubePressSearchGallery extends TubePressGallery implements TubePressHasValue {
    
    private $searchString;
    
    public function __construct() {
        $this->setName(TubePressGalleryValue::tag);
        $this->setTitle("YouTube search for");
        $this->setDescription("YouTube limits this mode to 1,000 results");
        $this->searchString = new TubePressTextValue(TubePressGalleryValue::tag . "Value", "stewart daily show");
    }

    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/videos?vq=" . urlencode($this->getValue()->getCurrentValue());
    }
	
	public function &getValue() {
	    return $this->searchString;
	}
}
?>