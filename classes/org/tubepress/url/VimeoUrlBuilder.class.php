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
 * Builds URLs to send out to Vimeo
 *
 */
class org_tubepress_url_VimeoUrlBuilder implements org_tubepress_url_UrlBuilder
{
    private $_tpom;
    
    /**
     * Builds a gdata request url for a list of videos
     *
     * @return string The gdata request URL for this gallery
     */
    public function buildGalleryUrl($currentPage)
    {
        $params = array();
        $mode   = $this->_tpom->get(org_tubepress_options_category_Gallery::MODE);
        
        switch ($mode) {
            
        case org_tubepress_gallery_TubePressGallery::VIMEO_UPLOADEDBY:
            $params['method']        = 'vimeo.videos.getUploaded';
            $params['user_id']       = $this->_tpom->get(org_tubepress_options_category_Gallery::VIMEO_UPLOADEDBY_VALUE);
            break;
        case org_tubepress_gallery_TubePressGallery::VIMEO_LIKES:
            $params['method']        = 'vimeo.videos.getLikes';
            $params['user_id']       = $this->_tpom->get(org_tubepress_options_category_Gallery::VIMEO_LIKES_VALUE);
            break;
        case org_tubepress_gallery_TubePressGallery::VIMEO_APPEARS_IN:
            $params['method']        = 'vimeo.videos.getAppearsIn';
            $params['user_id']       = $this->_tpom->get(org_tubepress_options_category_Gallery::VIMEO_APPEARS_IN_VALUE);
            break;
        case org_tubepress_gallery_TubePressGallery::VIMEO_SEARCH:
            $params['method']        = 'vimeo.videos.search';
            $params['query']         = $this->_tpom->get(org_tubepress_options_category_Gallery::VIMEO_SEARCH_VALUE);
            break;
        case org_tubepress_gallery_TubePressGallery::VIMEO_CREDITED:
            $params['method']        = 'vimeo.videos.getAll';
            $params['user_id']       = $this->_tpom->get(org_tubepress_options_category_Gallery::VIMEO_CREDITED_VALUE);
            break;
        case org_tubepress_gallery_TubePressGallery::VIMEO_CHANNEL:
            $params['method']        = 'vimeo.channels.getVideos';
            $params['channel_id']    = $this->_tpom->get(org_tubepress_options_category_Gallery::VIMEO_CHANNEL_VALUE);
            break;
        case org_tubepress_gallery_TubePressGallery::VIMEO_ALBUM:
            $params['method']        = 'vimeo.albums.getVideos';
            $params['album_id']      = $this->_tpom->get(org_tubepress_options_category_Gallery::VIMEO_ALBUM_VALUE);
            break;
        case org_tubepress_gallery_TubePressGallery::VIMEO_GROUP:
            $params['method']        = 'vimeo.groups.getVideos';
            $params['group_id']      = $this->_tpom->get(org_tubepress_options_category_Gallery::VIMEO_GROUP_VALUE);
        }
        
        $params['full_response'] = 'true';
        $params['page']          = $currentPage;
        $params['per_page']      = $this->_tpom->get(org_tubepress_options_category_Display::RESULTS_PER_PAGE);
        $sort = $this->_getSort($mode);
        if ($sort != '') {
        	$params['sort'] = $sort;
        }
        
        return $this->_buildUrl($params);
    }
    
    public function buildSingleVideoUrl($id)
    {
        $params = array();
        $params['method'] = 'vimeo.videos.getInfo';
        $params['video_id'] = $id;
        return $this->_buildUrl($params);
    }
    
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom) { $this->_tpom = $tpom; }
    
    private function _getSort($mode)
    {
    	/* these two modes can't be sorted */
    	if ($mode == org_tubepress_gallery_TubePressGallery::VIMEO_CHANNEL
    		|| $mode ==org_tubepress_gallery_TubePressGallery::VIMEO_ALBUM) {
    		return '';		
    	}
    	
    	$order = $this->_tpom->get(org_tubepress_options_category_Display::ORDER_BY);
    	
    	if ($mode == org_tubepress_gallery_TubePressGallery::VIMEO_SEARCH
    		&& $order == 'relevance') {
       		return 'relevant';
    	}
    	
    	if ($mode == org_tubepress_gallery_TubePressGallery::VIMEO_GROUP
    		&& $order == 'random') {
    		return $order;
    	}
    	
    	if ($order == 'viewCount') {
    		return 'most_played';
    	}
    	
    	if ($order == 'commentCount') {
    		return 'most_commented';
    	}
    	
    	if ($order == 'rating') {
    		return 'most_liked';
    	}
    	
    	if ($order == 'newest' || $order == 'oldest') {
    		return $order;	
    	}
    	return '';
    }
    
    private function _buildUrl($params)
    {
        $base = 'http://vimeo.com/api/rest/v2';
        
        $params['format']                 = 'php';
        $params['oauth_consumer_key']     = '86a1a3af34044829c435b2e0b03a8e6e';
        $params['oauth_nonce']            = md5(uniqid(microtime()));
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_timestamp']        = time();
        $params['oauth_version']          ='1.0';
        $params['oauth_signature']        = $this->_generateSignature($params, $base);
        return $base . '?' . http_build_query($params);
    }
    
    private function _generateSignature($params, $base)
    {
        uksort($params, 'strcmp');
        $params = $this->_url_encode_rfc3986($params);
        
        $baseString = array('GET', $base, urldecode(http_build_query($params)));
        $baseString = $this->_url_encode_rfc3986($baseString);
        $baseString = implode('&', $baseString);
        
        // Make the key
        $key_parts = array('55d2247024a79a29', '');
        $key_parts = $this->_url_encode_rfc3986($key_parts);
        $key = implode('&', $key_parts);
        
        // Generate signature
        return base64_encode(hash_hmac('sha1', $baseString, $key, true));
    }
    
    /**
     * URL encode a parameter or array of parameters.
     * 
     * @param array/string $input A parameter or set of parameters to encode.
     */
    private function _url_encode_rfc3986($input) {
        if (is_array($input)) {
            return array_map(array($this, '_url_encode_rfc3986'), $input);
        } elseif (is_scalar($input)) {
            return str_replace(array('+', '%7E'), array(' ', '~'), rawurlencode($input));
        } else {
            return '';
        }
    }
}
