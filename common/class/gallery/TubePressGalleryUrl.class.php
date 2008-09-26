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
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * 
     * @return string The YouTube request URL for this mode
     */
    public function get(TubePressOptionsManager $tpom)
    {
        $url = "";
        
        switch ($tpom->get(TubePressGalleryOptions::MODE)) {
            
        case TubePressGallery::USER:
            $url = "users/" . $tpom->get(TubePressGalleryOptions::USER_VALUE) .
                "/uploads";
            break;
            
        case TubePressGallery::TOP_RATED:
            $url = "standardfeeds/top_rated?time=" . 
                $tpom->get(TubePressGalleryOptions::TOP_RATED_VALUE);
            break;
            
        case TubePressGallery::POPULAR:
            $url = "standardfeeds/most_viewed?time=" . 
                $tpom->get(TubePressGalleryOptions::MOST_VIEWED_VALUE);
            break;
            
        case TubePressGallery::PLAYLIST:
            $url = "playlists/" . 
                $tpom->get(TubePressGalleryOptions::PLAYLIST_VALUE);
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
            $url = "users/" . $tpom->get(TubePressGalleryOptions::FAVORITES_VALUE) .
                "/favorites";
            break;
                
        case TubePressGallery::TAG:
            $tags = $tpom->get(TubePressGalleryOptions::TAG_VALUE);
            $tags = explode(" ", $tags);
            $url  = "videos?vq=" . implode("+", $tags);
            break;
                                
        default:
            $url = "standardfeeds/recently_featured";
            break;
        }

        $request = "http://gdata.youtube.com/feeds/api/$url";
        TubePressGalleryUrl::_urlPostProcessing($request,
            $tpom);
        return $request;
    }
    
/**
     * Appends some global query parameters on the request
     * before we fire it off to YouTube
     *
     * @param string                  &$request The request to be manipulated
     * @param TubePressOptionsManager $tpom     The TubePress options manager
     * 
     * @return void
     */
    private static function _urlPostProcessing(&$request, 
        TubePressOptionsManager $tpom)
    {
        
        $perPage = $tpom->get(TubePressDisplayOptions::RESULTS_PER_PAGE);
        $filter  = $tpom->get(TubePressAdvancedOptions::FILTER);
        $order   = $tpom->get(TubePressDisplayOptions::ORDER_BY);
        $mode    = $tpom->get(TubePressGalleryOptions::MODE);
        
        $currentPage = TubePressQueryString::getPageNum();
        
        /* start index of the videos */
        $start = ($currentPage * $perPage) - $perPage + 1;
        
        $requestURL = new Net_URL($request);
        $requestURL->addQueryString("start-index", $start);
        $requestURL->addQueryString("max-results", $perPage);
        
        $requestURL->addQueryString("racy", $filter ? "exclude" : "include");
      
        //TODO: this is ugly and stupid, in that order
        if ($mode != TubePressGallery::PLAYLIST) {
            $requestURL->addQueryString("orderby", $order);
        }
        
        /* YouTube API client ID and developer keys */
        $requestURL->addQueryString("client", $tpom->get(TubePressAdvancedOptions::CLIENT_KEY));
        $requestURL->addQueryString("key", $tpom->get(TubePressAdvancedOptions::DEV_KEY));
        
        $request = $requestURL->getURL();
    }
}
