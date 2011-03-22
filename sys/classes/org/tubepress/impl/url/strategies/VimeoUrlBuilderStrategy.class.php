<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_impl_url_strategies_AbstractUrlBuilderStrategy',
    'org_tubepress_api_const_options_values_ModeValue',
    'org_tubepress_api_options_OptionsManager',
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_const_options_names_Feed'));

/**
 * Builds URLs to send out to Vimeo
 *
 */
class org_tubepress_impl_url_strategies_VimeoUrlBuilderStrategy extends org_tubepress_impl_url_strategies_AbstractUrlBuilderStrategy
{
    /**
     * Builds a gdata request url for a list of videos
     *
     * @return string The gdata request URL for this gallery
     */
    protected function _buildGalleryUrl($currentPage)
    {
        $params = array();
        $ioc    = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom   = $ioc->get('org_tubepress_api_options_OptionsManager');
        $mode   = $tpom->get(org_tubepress_api_const_options_names_Output::MODE);

        $this->_verifyKeyAndSecretExists($tpom);

        switch ($mode) {

        case org_tubepress_api_const_options_values_ModeValue::VIMEO_UPLOADEDBY:
            $params['method']  = 'vimeo.videos.getUploaded';
            $params['user_id'] = $tpom->get(org_tubepress_api_const_options_names_Output::VIMEO_UPLOADEDBY_VALUE);
            break;
        case org_tubepress_api_const_options_values_ModeValue::VIMEO_LIKES:
            $params['method']  = 'vimeo.videos.getLikes';
            $params['user_id'] = $tpom->get(org_tubepress_api_const_options_names_Output::VIMEO_LIKES_VALUE);
            break;
        case org_tubepress_api_const_options_values_ModeValue::VIMEO_APPEARS_IN:
            $params['method']  = 'vimeo.videos.getAppearsIn';
            $params['user_id'] = $tpom->get(org_tubepress_api_const_options_names_Output::VIMEO_APPEARS_IN_VALUE);
            break;
        case org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH:
            $params['method'] = 'vimeo.videos.search';
            $params['query']  = $tpom->get(org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE);

            $filter = $tpom->get(org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
            if ($filter != '') {
                $params['user_id'] = $filter;
            }

            break;
        case org_tubepress_api_const_options_values_ModeValue::VIMEO_CREDITED:
            $params['method']  = 'vimeo.videos.getAll';
            $params['user_id'] = $tpom->get(org_tubepress_api_const_options_names_Output::VIMEO_CREDITED_VALUE);
            break;
        case org_tubepress_api_const_options_values_ModeValue::VIMEO_CHANNEL:
            $params['method']     = 'vimeo.channels.getVideos';
            $params['channel_id'] = $tpom->get(org_tubepress_api_const_options_names_Output::VIMEO_CHANNEL_VALUE);
            break;
        case org_tubepress_api_const_options_values_ModeValue::VIMEO_ALBUM:
            $params['method']   = 'vimeo.albums.getVideos';
            $params['album_id'] = $tpom->get(org_tubepress_api_const_options_names_Output::VIMEO_ALBUM_VALUE);
            break;
        case org_tubepress_api_const_options_values_ModeValue::VIMEO_GROUP:
            $params['method']   = 'vimeo.groups.getVideos';
            $params['group_id'] = $tpom->get(org_tubepress_api_const_options_names_Output::VIMEO_GROUP_VALUE);
        }

        $params['full_response'] = 'true';
        $params['page']          = $currentPage;
        $params['per_page']      = $tpom->get(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE);
        $sort                    = $this->_getSort($mode, $tpom);

        if ($sort != '') {
            $params['sort'] = $sort;
        }

        return $this->_buildUrl($params, $tpom);
    }

    protected function _buildSingleVideoUrl($id)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom         = $ioc->get('org_tubepress_api_options_OptionsManager');
        $pc           = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        
        //TODO: what if this bails?
        $providerName = $pc->calculateProviderOfVideoId($id);

        if ($providerName !== org_tubepress_api_provider_Provider::VIMEO) {
            throw new Exception("Unable to build Vimeo URL for video with ID $id");
        }

        $params             = array();
        $params['method']   = 'vimeo.videos.getInfo';
        $params['video_id'] = $id;

        return $this->_buildUrl($params, $tpom);
    }

    protected function _getHandledProviderName()
    {
        return org_tubepress_api_provider_Provider::VIMEO;
    }

    private function _getSort($mode, org_tubepress_api_options_OptionsManager $tpom)
    {
        /* these two modes can't be sorted */
        if ($mode == org_tubepress_api_const_options_values_ModeValue::VIMEO_CHANNEL
            || $mode == org_tubepress_api_const_options_values_ModeValue::VIMEO_ALBUM) {
            return '';
        }

        $order = $tpom->get(org_tubepress_api_const_options_names_Display::ORDER_BY);

        if ($mode == org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH
            && $order == 'relevance') {
               return 'relevant';
        }

        if ($mode == org_tubepress_api_const_options_values_ModeValue::VIMEO_GROUP
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

    private function _buildUrl($params, org_tubepress_api_options_OptionsManager $tpom)
    {
        $base = 'http://vimeo.com/api/rest/v2';

        $params['format']                 = 'php';
        $params['oauth_consumer_key']     = $tpom->get(org_tubepress_api_const_options_names_Feed::VIMEO_KEY);
        $params['oauth_nonce']            = md5(uniqid(mt_rand(), true));
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_timestamp']        = time();
        $params['oauth_version']          ='1.0';
        $params['oauth_signature']        = $this->_generateSignature($params, $base, $tpom);
        return $base . '?' . http_build_query($params);
    }

    private function _generateSignature($params, $base, org_tubepress_api_options_OptionsManager $tpom)
    {
        uksort($params, 'strcmp');
        $params = $this->_url_encode_rfc3986($params);

        $baseString = array('GET', $base, urldecode(http_build_query($params)));
        $baseString = $this->_url_encode_rfc3986($baseString);
        $baseString = implode('&', $baseString);

        // Make the key
        $keyParts = array($tpom->get(org_tubepress_api_const_options_names_Feed::VIMEO_SECRET), '');
        $keyParts = $this->_url_encode_rfc3986($keyParts);
        $key      = implode('&', $keyParts);

        // Generate signature
        return base64_encode(hash_hmac('sha1', $baseString, $key, true));
    }

    private function _verifyKeyAndSecretExists(org_tubepress_api_options_OptionsManager $tpom)
    {
        if ($tpom->get(org_tubepress_api_const_options_names_Feed::VIMEO_KEY) === '') {
            throw new Exception('Missing Vimeo API Consumer Key.');
        }
        if ($tpom->get(org_tubepress_api_const_options_names_Feed::VIMEO_SECRET) === '') {
            throw new Exception('Missing Vimeo API Consumer Secret.');
        }
        
    }
    
    /**
     * URL encode a parameter or array of parameters.
     * 
     * @param array/string $input A parameter or set of parameters to encode.
     */
    private function _url_encode_rfc3986($input)
    {
        if (is_array($input)) {
            return array_map(array($this, '_url_encode_rfc3986'), $input);
        } elseif (is_scalar($input)) {
            return str_replace(array('+', '%7E'), array(' ', '~'), rawurlencode($input));
        } else {
            return '';
        }
    }
}
