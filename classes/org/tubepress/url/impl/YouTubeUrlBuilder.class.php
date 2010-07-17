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
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
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
class org_tubepress_url_impl_YouTubeUrlBuilder implements org_tubepress_url_UrlBuilder
{
    /**
     * Builds a gdata request url for a list of videos
     *
     * @return string The gdata request URL for this gallery
     */
    public function buildGalleryUrl(org_tubepress_ioc_IocService $ioc, $currentPage)
    {
        $url = '';
        
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        switch ($tpom->get(org_tubepress_options_category_Gallery::MODE)) {
            
        case org_tubepress_gallery_TubePressGallery::USER:
            $url = 'users/' . $tpom->get(org_tubepress_options_category_Gallery::USER_VALUE) . '/uploads';
            break;
            
        case org_tubepress_gallery_TubePressGallery::TOP_RATED:
            $url = 'standardfeeds/top_rated?time=' . $tpom->get(org_tubepress_options_category_Gallery::TOP_RATED_VALUE);
            break;
            
        case org_tubepress_gallery_TubePressGallery::POPULAR:
            $url = 'standardfeeds/most_viewed?time=' . $tpom->get(org_tubepress_options_category_Gallery::MOST_VIEWED_VALUE);
            break;
            
        case org_tubepress_gallery_TubePressGallery::PLAYLIST:
            $url = 'playlists/' . $tpom->get(org_tubepress_options_category_Gallery::PLAYLIST_VALUE);
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
                
        case org_tubepress_gallery_TubePressGallery::MOST_DISCUSSED:
            $url = 'standardfeeds/most_discussed';
            break;
                
        case org_tubepress_gallery_TubePressGallery::MOBILE:
            $url = 'standardfeeds/watch_on_mobile';
            break;
               
        case org_tubepress_gallery_TubePressGallery::FAVORITES:
            $url = 'users/' . $tpom->get(org_tubepress_options_category_Gallery::FAVORITES_VALUE) . '/favorites';
            break;
                
        case org_tubepress_gallery_TubePressGallery::TAG:
            $tags = $tpom->get(org_tubepress_options_category_Gallery::TAG_VALUE);
            $tags = explode(' ', $tags);
            $url  = 'videos?q=' . implode('+', $tags);
            break;
                                
        default:
            $url = 'standardfeeds/recently_featured';
            break;
        }

        $request = new net_php_pear_Net_URL2("http://gdata.youtube.com/feeds/api/$url");
        $this->_commonUrlPostProcessing($tpom, $request);
        $this->_galleryUrlPostProcessing($tpom, $request, $currentPage);
        $this->_fieldsPostProcessing($request);
        return $request->getURL();
    }
    
    public function buildSingleVideoUrl(org_tubepress_ioc_IocService $ioc, $id)
    {
        $requestURL = new net_php_pear_Net_URL2("http://gdata.youtube.com/feeds/api/videos/$id");
        $this->_commonUrlPostProcessing($ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER), $requestURL);
        
        return $requestURL->getURL();
    }

    private function _commonUrlPostProcessing(org_tubepress_options_manager_OptionsManager $tpom, net_php_pear_Net_URL2 $url)
    {
        $url->setQueryVariable('v', 2);
        $url->setQueryVariable('key', $tpom->get(org_tubepress_options_category_Feed::DEV_KEY));
    }

	private function _fieldsPostProcessing(net_php_pear_Net_URL2 $url)
    {
    	
    }

    /**
     * Appends some global query parameters on the request
     * before we fire it off to YouTube
     *
     * @param string                  &$request The request to be manipulated
     * 
     * @return void
     */
    private function _galleryUrlPostProcessing(org_tubepress_options_manager_OptionsManager $tpom, net_php_pear_Net_URL2 $url, $currentPage)
    {
        $perPage   = $tpom->get(org_tubepress_options_category_Display::RESULTS_PER_PAGE);
        
        /* start index of the videos */
        $start = ($currentPage * $perPage) - $perPage + 1;
        
        $url->setQueryVariable('start-index', $start);
        $url->setQueryVariable('max-results', $perPage);
        
        $this->_setOrderBy($tpom, $url);
        
        $url->setQueryVariable('safeSearch', $tpom->get(org_tubepress_options_category_Feed::FILTER));

        if ($tpom->get(org_tubepress_options_category_Feed::EMBEDDABLE_ONLY)) {
            $url->setQueryVariable('format', '5');
        }
    }
    
    private function _setOrderBy(org_tubepress_options_manager_OptionsManager $tpom, net_php_pear_Net_URL2 $url) 
    {
        $order = $tpom->get(org_tubepress_options_category_Display::ORDER_BY);
        $mode  = $tpom->get(org_tubepress_options_category_Gallery::MODE);
        
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
