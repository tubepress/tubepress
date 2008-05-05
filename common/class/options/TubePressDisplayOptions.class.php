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
class TubePressDisplayOptions extends TubePressOptionsCategory {
    
    const CURRENT_PLAYER_NAME = "playerLocation";
	const orderBy = "orderBy";
	const RESULTS_PER_PAGE = "resultsPerPage";
    const thumbHeight = "thumbHeight";
	const thumbWidth = "thumbWidth";

	public function __construct() {
	    
        $this->setTitle("Gallery options");
        
        $thumbHeightValue = new TubePressIntValue(TubePressDisplayOptions::thumbHeight, 90);
        $thumbHeightValue->setMax(90);
        
        $thumbWidthValue = new TubePressIntValue(TubePressDisplayOptions::thumbWidth, 120);
        $thumbWidthValue->setMax(120);
        
        $resultsPerPageValue = new TubePressIntValue(TubePressDisplayOptions::RESULTS_PER_PAGE, 20);
        $resultsPerPageValue->setMax(50);
        
        $this->setOptions(array(
            TubePressDisplayOptions::CURRENT_PLAYER_NAME => new TubePressOption(
                TubePressDisplayOptions::CURRENT_PLAYER_NAME,
                "Play each video...", " ",
                new TubePressPlayerValue(
                    TubePressDisplayOptions::CURRENT_PLAYER_NAME,
                    new TPNormalPlayer())
            ),
            
            TubePressDisplayOptions::thumbHeight => new TubePressOption(
                TubePressDisplayOptions::thumbHeight,
                "Height (px) of thumbs",
                "Default (and maximum) is 90",
                $thumbHeightValue
            ),
                
            TubePressDisplayOptions::thumbWidth => new TubePressOption(
                TubePressDisplayOptions::thumbWidth,
                "Width (px) of thumbs",
                "Default (and maximum) is 120",
                $thumbWidthValue
           ),
           
           TubePressDisplayOptions::RESULTS_PER_PAGE => new TubePressOption(
                TubePressDisplayOptions::RESULTS_PER_PAGE,
                "Videos per page",
                "Default is 20, maximum is 50",
                $resultsPerPageValue
            ),
            
           TubePressDisplayOptions::orderBy => new TubePressOption(
               TubePressDisplayOptions::orderBy,
               "Order videos by",
               "",
               new TubePressOrderValue(TubePressDisplayOptions::orderBy)
           )
        ));
	}
}
?>