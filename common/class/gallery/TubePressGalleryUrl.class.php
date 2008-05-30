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
 * Returns the YouTube query URL based on which mode we're in
 *
 */
class TubePressGalleryUrl 
{
    
    /**
     * The main logic in this class
     *
     * @param TubePressOptionsManager $tpom
     * @return string The YouTube request URL for this mode
     */
    public function get(TubePressOptionsManager $tpom)
    {
        
        $url = "";
        
        switch ($tpom->get(TubePressGalleryOptions::MODE)) {
            
            case TubePressGallery::USER:
                $url = "users/" . $tpom->get(TubePressGalleryOptions::USER_VALUE) . "/uploads";
                break;
            
            case TubePressGallery::TOP_RATED:
                $url = "standardfeeds/top_rated?time=" . $tpom->get(TubePressGalleryOptions::TOP_RATED_VALUE);
                break;
            
            case TubePressGallery::POPULAR:
                $url = "standardfeeds/most_viewed?time=" . $tpom->get(TubePressGalleryOptions::MOST_VIEWED_VALUE);
                break;
            
            case TubePressGallery::PLAYLIST:
                $url = "playlists/" . $tpom->get(TubePressGalleryOptions::PLAYLIST_VALUE);
                break;
                
            case TubePressGallery::MOST_RESPONDED:
                $url = "standardfeeds/most_responded";
                break;
                
            case TubePressGallery::MOST_RECENT:
                $url = "standardfeeds/most_recent";
                break;
                
            case TubePressGallery::MOST_LINKED:
                $url = "standardfeeds/most_linked";
                break;
                
            case TubePressGallery::MOST_DISCUSSESD:
                $url = "standardfeeds/most_discussed";
                break;
                
            case TubePressGallery::MOBILE:
                $url = "standardfeeds/watch_on_mobile";
                break;
                
            case TubePressGallery::FAVORITES:
                $url = "users/" . $tpom->get(TubePressGalleryOptions::FAVORITES_VALUE) . "/favorites";
                break;
                
            case TubePressGallery::TAG:
                $tags = $tpom->get(TubePressGalleryOptions::TAG_VALUE);
                $tags = explode(" ", $tags);
                $url = "videos?vq="    . implode("+", $tags);
                break;
                                
            default:
                $url = "standardfeeds/recently_featured";
                break;
        }
        
        return "http://gdata.youtube.com/feeds/api/$url";
    }
}
