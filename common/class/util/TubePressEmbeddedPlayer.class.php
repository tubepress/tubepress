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
class TubePressEmbeddedPlayer {
	
	private $asString;
	
	public function __construct(TubePressVideo $vid, TubePressStorage_v160 $stored) {
		$id = $vid->getId();
		$height = $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedHeight);
		$width = $stored->getCurrentValue(TubePressEmbeddedOptions::EMBEDDED_WIDTH);
		$rel = $stored->getCurrentValue(TubePressEmbeddedOptions::showRelated)? "1" : "0";
		$colors = $stored->getCurrentValue(TubePressEmbeddedOptions::playerColor);
		$autoPlay = $stored->getCurrentValue(TubePressEmbeddedOptions::autoplay)? "1" : "0";
		$loop = $stored->getCurrentValue(TubePressEmbeddedOptions::loop)? "1" : "0";
		$egm = $stored->getCurrentValue(TubePressEmbeddedOptions::genie)? "1" : "0";
		$border = $stored->getCurrentValue(TubePressEmbeddedOptions::border)? "1" : "0";
		
		$link = "http://www.youtube.com/v/$id";
		
		if ($colors != "/") {
			$colors = explode("/", $colors);
			$link .= "&amp;color1=" . $colors[0] . "&amp;color2=" . $colors[1];
		}
		
		$link .= "&amp;rel=$rel";
		$link .= "&amp;autoplay=$autoPlay";
		$link .= "&amp;loop=$loop";
		$link .= "&amp;egm=$egm";
		$link .= "&amp;border=$border";
	
		$string = '<object type="application/x-shockwave-flash" style="width:' .
			$width . 'px;height:' . $height . 'px"';
		$string .= ' data="' . $link . '">';
		$string .= '<param name="wmode" value="transparent" />';
		$string .= '<param name="movie" value="' . $link . '" />';   
		$string .= '</object>';
		$this->asString = $string;
	}
	
	public function toString() {
		return $this->asString;
	}
}

?>