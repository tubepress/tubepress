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
class TubePressGalleryValue extends TubePressEnumValue {
    
  	/* All valid gallery types here */
	const favorites = 	"favorites";
	const tag = 		"tag";
    const user= 		"user";
    const playlist = 	"playlist";
    const featured = 	"featured";
    const popular = 	"popular";
    const top_rated = 	"top_rated";
    const mobile = 		"mobile";
    const most_linked =	"most_linked";
    const most_recent = "most_recent";
    const most_discussed = "most_discussed";
    const most_responded = "most_responded";
    
    private $galleries;
    
    /**
     * Default constructor
     *
     * @param string $theName
     */
    public function __construct($theName, array $theGalleries) {

        parent::__construct($theName, array(
            TubePressGalleryValue::favorites,
            TubePressGalleryValue::tag,
            TubePressGalleryValue::user,
            TubePressGalleryValue::playlist,
            TubePressGalleryValue::featured,
            TubePressGalleryValue::popular,
            TubePressGalleryValue::top_rated,
            TubePressGalleryValue::mobile,
            TubePressGalleryValue::most_linked
        ), TubePressGalleryValue::top_rated);
        
        $this->galleries = $theGalleries;
    }
    
    public function updateFromOptionsPage(array $postVars) {
        
    	/* see if it's there */
    	if (array_key_exists($this->getName(), $postVars)) {
        	$this->updateManually($postVars[$this->getName()]);
        }
        
        foreach ($this->galleries as &$gallery) {
        	if ($gallery instanceof TubePressHasValue) {
        		$value =& $gallery->getValue();
        		$value->updateFromOptionsPage($postVars);
        	}
        }
    }
}

?>