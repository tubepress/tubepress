<?php

class TubePressEmbeddedPlayer {
	
	private $asString;
	
	public function __construct(TubePressVideo $vid, TubePressStorage_v157 $stored) {
		$id = $vid->getId();
		$height = $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedHeight);
		$width = $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedWidth);
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