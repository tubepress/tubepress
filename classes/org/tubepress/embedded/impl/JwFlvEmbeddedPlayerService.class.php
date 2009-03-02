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
 * Represents an HTML-embeddable JW FLV Player
 *
 */
class org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerService
{
    /**
     * Spits back the text for this embedded player
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {
        global $tubepress_base_url;

        $link = new net_php_pear_Net_URL2(sprintf("http://www.youtube.com/watch?v=%s", $videoId));
        
        $link = $link->getURL(true);
        
        return sprintf(<<<EOT
<object 
    type="application/x-shockwave-flash" 
    data="%s/ui/embedded/longtail/mediaplayer/player.swf"
    width="425" 
    height="355" 
    id="VideoPlayback">
    <param name="movie" value="%s/ui/embedded/longtail/mediaplayer/player.swf" />
    <param name="allowscriptacess" value="sameDomain" />
    <param name="bgcolor" value="#000000" />
    <param name="quality" value="high" />
    <param name="flashvars" value="file=%s" />
</object>
EOT
        , $tubepress_base_url, $tubepress_base_url, $link);
    }
    
    public function getJavaScriptVideoIdMatcher()
    {
        return "/youtube\.com\/watch\?v=(.{11}).*/";
    }    
}

?>
