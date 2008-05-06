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
class TubePressEmbeddedOptions extends TubePressOptionsCategory {
    
    const EMBEDDED_HEIGHT = "embeddedHeight";
	const EMBEDDED_WIDTH = "embeddedWidth";
	const playerColor = "playerColor";
	const autoplay = "autoplay";
	const showRelated = "showRelated";
	const loop = "loop";
	const genie = "genie";
	const border = "border";

	public function __construct() {
	    
        $this->setTitle("Embedded player");
        
        $this->setOptions(array(
            
            TubePressEmbeddedOptions::EMBEDDED_HEIGHT => new TubePressOption(
                TubePressEmbeddedOptions::EMBEDDED_HEIGHT,
                "Max height (px)",
                "Default is 355",
                new TubePressIntValue(
                    TubePressEmbeddedOptions::EMBEDDED_HEIGHT,
                    355
                )
            ),
            
            TubePressEmbeddedOptions::EMBEDDED_WIDTH => new TubePressOption(
                TubePressEmbeddedOptions::EMBEDDED_WIDTH,
                "Max width (px)",
                "Default is 425",
                new TubePressIntValue(
                    TubePressEmbeddedOptions::EMBEDDED_WIDTH,
                    425
                )
            ),
           
           TubePressEmbeddedOptions::playerColor => new TubePressOption(
               TubePressEmbeddedOptions::playerColor,
               "Color",
               "",
               new TubePressColorValue(TubePressEmbeddedOptions::playerColor)
           ),
           
           TubePressEmbeddedOptions::autoplay => new TubePressOption(
               TubePressEmbeddedOptions::autoplay,
               "Auto-play videos", "",
               new TubePressBoolValue(TubePressEmbeddedOptions::autoplay, false)
           ),
           
           TubePressEmbeddedOptions::showRelated => new TubePressOption(
               TubePressEmbeddedOptions::showRelated,
               "Show related videos",
               "Toggles the display of related videos after a video finishes",
               new TubePressBoolValue(TubePressEmbeddedOptions::showRelated, true)
           ),
           
           TubePressEmbeddedOptions::loop => new TubePressOption(
               TubePressEmbeddedOptions::loop,
               "Loop",
               "Continue playing the video until the user stops it",
               new TubePressBoolValue(TubePressEmbeddedOptions::loop, false)
           ),
           
           TubePressEmbeddedOptions::genie => new TubePressOption(
               TubePressEmbeddedOptions::genie,
               "Enhanced genie menu",
               "Show the genie menu (if present) when the mouse enters the video area (as opposed to only when the user pushes the \"menu\" button",
               new TubePressBoolValue(TubePressEmbeddedOptions::genie, false)
           ),
           
           TubePressEmbeddedOptions::border => new TubePressOption(
               TubePressEmbeddedOptions::border,
               "Show border",
               "",
               new TubePressBoolValue(TubePressEmbeddedOptions::border, false)
           )
        ));
	}
}
?>