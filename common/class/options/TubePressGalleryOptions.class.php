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
class TubePressGalleryOptions extends TubePressOptionsCategory {
    
    const MODE = "mode";
    
    private $galleries;
    
    public function __construct() {
            
        $this->setTitle("Which videos?");
    
        $this->galleries = array(
            TubePressGalleryValue::top_rated => new TubePressTopRatedGallery(),
            TubePressGalleryValue::FAVORITES => new TubePressFavoritesGallery(),
            TubePressGalleryValue::FEATURED => new TubePressFeaturedGallery(),
            TubePressGalleryValue::mobile => new TubePressMobileGallery(),
            TubePressGalleryValue::PLAYLIST => new TubePressPlaylistGallery(),
            TubePressGalleryValue::popular => new TubePressPopularGallery(),
            TubePressGalleryValue::tag => new TubePressSearchGallery(),
            TubePressGalleryValue::user => new TubePressUserGallery(),
            TubePressGalleryValue::most_linked => new TubePressMostLinkedGallery(),
            TubePressGalleryValue::most_recent => new TubePressMostRecentGallery(),
            TubePressGalleryValue::most_discussed => new TubePressMostDiscussedGallery(),
            TubePressGalleryValue::most_responded => new TubePressMostRespondedGallery()
        );
        
        $this->setOptions(array(
            TubePressGalleryOptions::MODE => new TubePressOption(
                TubePressGalleryOptions::MODE,
            	" ", " ",
                new TubePressGalleryValue(TubePressGalleryOptions::MODE, $this->galleries)
            )));
    }
    
    public function printForOptionsForm(HTML_Template_IT &$tpl) {

        $tpl->setVariable("OPTION_CATEGORY_TITLE", $this->getTitle());
            
        /* go through each option in the category */
        foreach($this->galleries as $gallery) {             
            $tpl->setVariable("OPTION_TITLE", $gallery->getTitle());
            $tpl->setVariable("OPTION_DESC", $gallery->getDescription());
            $tpl->setVariable("OPTION_NAME", $gallery->getName());
            
            if ($this->get(TubePressGalleryOptions::MODE)->getValue()->getCurrentValue() == $gallery->getName()) {
                $tpl->setVariable("OPTION_SELECTED", "CHECKED");
            }
            
            $tpl->parse("galleryType");
            
            if ($gallery instanceof TubePressHasValue) {
                $gallery->getValue()->printForOptionsPage($tpl);
            }
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
    
    public function &getGallery($galleryName) {
    	return $this->galleries[$galleryName];
    }
}
?>