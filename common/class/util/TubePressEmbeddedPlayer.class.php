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
 * Represents an HTML-embeddable YouTube player
 *
 */
class TubePressEmbeddedPlayer
{
    private $_asString;
    
    /**
     * Constructor
     *
     * @param TubePressVideo          $vid  The video to play
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * 
     * @return void
     */
    public function __construct(TubePressVideo $vid, TubePressOptionsManager $tpom)
    {
        $id       = $vid->getId();
        $height   = $tpom->get(TubePressEmbeddedOptions::EMBEDDED_HEIGHT);
        $width    = $tpom->get(TubePressEmbeddedOptions::EMBEDDED_WIDTH);
        $rel      = $tpom->get(TubePressEmbeddedOptions::SHOW_RELATED)? "1" : "0";
        $colors   = $tpom->get(TubePressEmbeddedOptions::PLAYER_COLOR);
        $autoPlay = $tpom->get(TubePressEmbeddedOptions::AUTOPLAY)? "1" : "0";
        $loop     = $tpom->get(TubePressEmbeddedOptions::LOOP)? "1" : "0";
        $egm      = $tpom->get(TubePressEmbeddedOptions::GENIE)? "1" : "0";
        $border   = $tpom->get(TubePressEmbeddedOptions::BORDER)? "1" : "0";

        $link = "http://www.youtube.com/v/$id";
        
        if ($colors != "/") {
            $colors = explode("/", $colors);
            $link  .= "&amp;color1=" . $colors[0] . "&amp;color2=" . $colors[1];
        }
        
        $link .= "&amp;rel=$rel";
        $link .= "&amp;autoplay=$autoPlay";
        $link .= "&amp;loop=$loop";
        $link .= "&amp;egm=$egm";
        $link .= "&amp;border=$border";
    
        $string          = '<object ' .  
       	    'type="application/x-shockwave-flash" style="width:' .
            $width . 'px;height:' . $height . 'px"';
        $string         .= ' data="' . $link . '">';
        $string         .= '<param name="wmode" value="transparent" />';
        $string         .= '<param name="movie" value="' . $link . '" />';   
        $string         .= '</object>';
        $this->_asString = $string;
    }
    
    /**
     * Spits back the text for this embedded player
     *
     * @return string The text for this embedded player
     */
    public function toString()
    {
        return $this->_asString;
    }
}

?>
