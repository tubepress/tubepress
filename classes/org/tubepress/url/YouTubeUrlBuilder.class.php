<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_url_UrlBuilder',
    'org_tubepress_options_category_Gallery',
    'org_tubepress_gallery_TubePressGallery',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_options_category_Advanced',
    'org_tubepress_options_category_Display',
    'org_tubepress_options_category_Embedded',
    'org_tubepress_options_category_Meta',
    'net_php_pear_Net_URL2',
    'org_tubepress_options_category_Feed'));

/**
 * Builds URLs to send out to YouTube for gdata
 *
 */
class org_tubepress_url_YouTubeUrlBuilder implements org_tubepress_url_UrlBuilder
{
    private $_tpom;
    
    /**
     * Builds a gdata request url for a list of videos
     *
     * @return string The gdata request URL for this gallery
     */
    public function buildGalleryUrl($currentPage)
    {
        $url = '';
        
        switch ($this->_tpom->get(org_tubepress_options_category_Gallery::MODE)) {
            
        case org_tubepress_gallery_TubePressGallery::USER:
            $url = 'users/' . $this->_tpom->get(org_tubepress_options_category_Gallery::USER_VALUE) . '/uploads';
            break;
            
        case org_tubepress_gallery_TubePressGallery::TOP_RATED:
            $url = 'standardfeeds/top_rated?time=' . $this->_tpom->get(org_tubepress_options_category_Gallery::TOP_RATED_VALUE);
            break;
            
        case org_tubepress_gallery_TubePressGallery::POPULAR:
            $url = 'standardfeeds/most_viewed?time=' . $this->_tpom->get(org_tubepress_options_category_Gallery::MOST_VIEWED_VALUE);
            break;
            
        case org_tubepress_gallery_TubePressGallery::PLAYLIST:
            $url = 'playlists/' . $this->_tpom->get(org_tubepress_options_category_Gallery::PLAYLIST_VALUE);
            break;
                
        case org_tubepress_gallery_TubePressGallery::MOST_RESPONDED:
            $url = 'standardfeeds/most_responded';
            break;
              
        case org_tubepress_gallery_TubePressGallery::MOST_RECENT:
            $url = 'standardfeeds/most_recent';
            break;
                
        case org_tubepress_gallery_TubePressGallery::MOST_LINKED:
            $url = 'standardfeeds/most_linked';
            break;
                
        case org_tubepress_gallery_TubePressGallery::MOST_DISCUSSESD:
            $url = 'standardfeeds/most_discussed';
            break;
                
        case org_tubepress_gallery_TubePressGallery::MOBILE:
            $url = 'standardfeeds/watch_on_mobile';
            break;
               
        case org_tubepress_gallery_TubePressGallery::FAVORITES:
            $url = 'users/' . $this->_tpom->get(org_tubepress_options_category_Gallery::FAVORITES_VALUE) . '/favorites';
            break;
                
        case org_tubepress_gallery_TubePressGallery::TAG:
            $tags = $this->_tpom->get(org_tubepress_options_category_Gallery::TAG_VALUE);
            $tags = explode(' ', $tags);
            $url  = 'videos?q=' . implode('+', $tags);
            break;
                                
        default:
            $url = 'standardfeeds/recently_featured';
            break;
        }

        $request = new net_php_pear_Net_URL2("http://gdata.youtube.com/feeds/api/$url");
        $this->_commonUrlPostProcessing($request);
        $this->_galleryUrlPostProcessing($request, $currentPage);
        return $request->getURL();
    }
    
    public function buildSingleVideoUrl($id)
    {
        $requestURL = new net_php_pear_Net_URL2("http://gdata.youtube.com/feeds/api/videos/$id");
        $this->_commonUrlPostProcessing($requestURL);
        
        return $requestURL->getURL();
    }
    
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom)
    { 
        $this->_tpom = $tpom; 
    }

    private function _commonUrlPostProcessing(net_php_pear_Net_URL2 $url)
    {
        $url->setQueryVariable('v', 2);
        $url->setQueryVariable('key', $this->_tpom->get(org_tubepress_options_category_Feed::DEV_KEY));
    }

    /**
     * Appends some global query parameters on the request
     * before we fire it off to YouTube
     *
     * @param string                  &$request The request to be manipulated
     * 
     * @return void
     */
    private function _galleryUrlPostProcessing(net_php_pear_Net_URL2 $url, $currentPage)
    {
        $perPage   = $this->_tpom->get(org_tubepress_options_category_Display::RESULTS_PER_PAGE);
        
        /* start index of the videos */
        $start = ($currentPage * $perPage) - $perPage + 1;
        
        $url->setQueryVariable('start-index', $start);
        $url->setQueryVariable('max-results', $perPage);
        
        $this->_setOrderBy($url);
        
        $url->setQueryVariable('safeSearch', $this->_tpom->get(org_tubepress_options_category_Feed::FILTER));

        if ($this->_tpom->get(org_tubepress_options_category_Feed::EMBEDDABLE_ONLY)) {
            $url->setQueryVariable('format', '5');
        }
    }
    
    private function _setOrderBy(net_php_pear_Net_URL2 $url) 
    {
        $order = $this->_tpom->get(org_tubepress_options_category_Display::ORDER_BY);
        $mode  = $this->_tpom->get(org_tubepress_options_category_Gallery::MODE);
        
        if ($order == 'random') {
            return;
        }
        
        /* any feed can take these */
        if ($order == 'viewCount' || $order =='published') {
            $url->setQueryVariable('orderby', $order);
            return;
        }
        
        /* playlist specific stuff */
        if ($mode == org_tubepress_gallery_TubePressGallery::PLAYLIST) {
            if (in_array($order, array('position', 'commentCount', 'duration', 'title'))) {
                $url->setQueryVariable('orderby', $order);
            }
            return;
        }
        
        if (in_array($order, array('relevance', 'rating'))) {
            $url->setQueryVariable('orderby', $order);
        }
    }
}
