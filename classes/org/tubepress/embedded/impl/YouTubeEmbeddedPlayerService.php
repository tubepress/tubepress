<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
class org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerService
{
    /**
     * Spits back the text for this embedded player
     *
     * @return string The text for this embedded player
     */
    public function toString()
    {
        $link = new net_php_pear_Net_URL2(sprintf("http://www.youtube.com/v/%s", $this->_id));
        
        if (!($this->_color1 == "999999" && $this->_color2 == "FFFFFF")) {
            $link->setQueryVariable("color2", "0x" . $this->_color1);
            $link->setQueryVariable("color1", "0x" . $this->_color2);
        }
        $link->setQueryVariable("rel", $this->_showRelated   ? "1" : "0");
        $link->setQueryVariable("autoplay", $this->_autoPlay ? "1" : "0");
        $link->setQueryVariable("loop", $this->_loop         ? "1" : "0");
        $link->setQueryVariable("egm", $this->_genie         ? "1" : "0");
        $link->setQueryVariable("border", $this->_border     ? "1" : "0");
        $link->setQueryVariable("fs", $this->_fullscreen     ? "1" : "0");
        
        $link->setQueryVariable("showinfo", $this->_showInfo ? "1" : "0");
        
        switch ($this->_quality) {
        case "high":
            $link->setQueryVariable("ap", "%26");
            $link->setQueryVariable("fmt", "6");
            break;
        case "higher":
            $link->setQueryVariable("ap", "%26");
            $link->setQueryVariable("fmt", "18");
            break;
        case "highest":
            $link->setQueryVariable("ap", "%26");
            $link->setQueryVariable("fmt", "22");
            break;      
        }
        
        $link = $link->getURL(true);
        
        $embedSrc = sprintf(<<<EOT
<object type="application/x-shockwave-flash" 
    style="width: %spx; height: %spx" data="%s">
    <param name="wmode" value="transparent" />
    <param name="movie" value="%s" />
    <param name="allowfullscreen" value="%s" />
</object>
EOT
        , $this->_width, $this->_height, $link, $link, $this->_fullscreen ? "true" : "false");
    return str_replace("?", "&amp;", $embedSrc);
    }
}

?>
