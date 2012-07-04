<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_values_GallerySourceValue',
    'org_tubepress_api_const_options_values_OrderByValue',
    'org_tubepress_api_const_options_names_GallerySource',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_names_Feed',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_impl_feed_urlbuilding_AbstractUrlBuilderCommand',
    'org_tubepress_impl_feed_UrlBuilderChainContext',
));

/**
 * Builds URLs to send out to Vimeo
 *
 */
class org_tubepress_impl_feed_urlbuilding_VimeoUrlBuilderCommand extends org_tubepress_impl_feed_urlbuilding_AbstractUrlBuilderCommand
{
    const PARAM_ALBUM_ID      = 'album_id';
    const PARAM_CHANNEL_ID    = 'channel_id';
    const PARAM_FORMAT        = 'format';
    const PARAM_FULL_RESPONSE = 'full_response';
    const PARAM_GROUP_ID      = 'group_id';
    const PARAM_METHOD        = 'method';
    const PARAM_PAGE          = 'page';
    const PARAM_PER_PAGE      = 'per_page';
    const PARAM_QUERY         = 'query';
    const PARAM_SORT          = 'sort';
    const PARAM_USER_ID       = 'user_id';
    const PARAM_VIDEO_ID      = 'video_id';

    const METHOD_ALBUM_GETVIDEOS    = 'vimeo.albums.getVideos';
    const METHOD_CHANNEL_GETVIDEOS  = 'vimeo.channels.getVideos';
    const METHOD_GROUP_GETVIDEOS    = 'vimeo.groups.getVideos';
    const METHOD_VIDEOS_APPEARSIN   = 'vimeo.videos.getAppearsIn';
    const METHOD_VIDEOS_GETALL      = 'vimeo.videos.getAll';
    const METHOD_VIDEOS_GETINFO     = 'vimeo.videos.getInfo';
    const METHOD_VIDEOS_GETLIKES    = 'vimeo.videos.getLikes';
    const METHOD_VIDEOS_GETUPLOADED = 'vimeo.videos.getUploaded';
    const METHOD_VIDEOS_SEARCH      = 'vimeo.videos.search';

    const OAUTH_CONSUMER_KEY     = 'oauth_consumer_key';
    const OAUTH_NONCE            = 'oauth_nonce';
    const OAUTH_SIGNATURE_METHOD = 'oauth_signature_method';
    const OAUTH_TIMESTAMP        = 'oauth_timestamp';
    const OAUTH_VERSION          = 'oauth_version';
    const OAUTH_SIGNATURE        = 'oauth_signature';

    const SORT_MOST_COMMENTS = 'most_commented';
    const SORT_MOST_LIKED    = 'most_liked';
    const SORT_MOST_PLAYED   = 'most_played';
    const SORT_RELEVANT      = 'relevant';

    const INI_ARG_SEPARATOR = 'arg_separator.input';

    const LOG_PREFIX = 'Vimeo URL Builder';

    const URL_BASE = 'http://vimeo.com/api/rest/v2';

    /**
     * Build a gallery URL for the given page.
     *
     * @param int $currentPage The page number.
     *
     * @return string The gallery URL.
     */
    protected function buildGalleryUrl($currentPage)
    {
        $params       = array();
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext  = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $mode         = $execContext->get(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        $this->_verifyKeyAndSecretExists($execContext);

        self::_setIniArgSeparator();

        switch ($mode) {

        case org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY:

            $params[self::PARAM_METHOD]  = self::METHOD_VIDEOS_GETUPLOADED;
            $params[self::PARAM_USER_ID] = $execContext->get(org_tubepress_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE);
            break;

        case org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_LIKES:

            $params[self::PARAM_METHOD]  = self::METHOD_VIDEOS_GETLIKES;
            $params[self::PARAM_USER_ID] = $execContext->get(org_tubepress_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE);
            break;

        case org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN:

            $params[self::PARAM_METHOD]  = self::METHOD_VIDEOS_APPEARSIN;
            $params[self::PARAM_USER_ID] = $execContext->get(org_tubepress_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE);
            break;

        case org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH:

            $params[self::PARAM_METHOD] = self::METHOD_VIDEOS_SEARCH;
            $params[self::PARAM_QUERY]  = $execContext->get(org_tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE);

            $filter = $execContext->get(org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
            if ($filter != '') {
                $params[self::PARAM_USER_ID] = $filter;
            }

            break;

        case org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_CREDITED:

            $params[self::PARAM_METHOD]  = self::METHOD_VIDEOS_GETALL;
            $params[self::PARAM_USER_ID] = $execContext->get(org_tubepress_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE);
            break;

        case org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL:

            $params[self::PARAM_METHOD]     = self::METHOD_CHANNEL_GETVIDEOS;
            $params[self::PARAM_CHANNEL_ID] = $execContext->get(org_tubepress_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE);
            break;

        case org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_ALBUM:

            $params[self::PARAM_METHOD]   = self::METHOD_ALBUM_GETVIDEOS;
            $params[self::PARAM_ALBUM_ID] = $execContext->get(org_tubepress_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);
            break;

        case org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_GROUP:

            $params[self::PARAM_METHOD]   = self::METHOD_GROUP_GETVIDEOS;
            $params[self::PARAM_GROUP_ID] = $execContext->get(org_tubepress_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE);
        }

        $params[self::PARAM_FULL_RESPONSE] = 'true';
        $params[self::PARAM_PAGE]          = $currentPage;
        $params[self::PARAM_PER_PAGE]      = $execContext->get(org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
        $sort                              = $this->_getSort($mode, $execContext);

        if ($sort != '') {
            $params[self::PARAM_SORT] = $sort;
        }

        $finalUrl = $this->_buildUrl($params, $execContext);

        self::_restoreIniArgSeparator();

        return $finalUrl;
    }

    /**
    * Build the URL for a single video.
    *
    * @param string $id The video ID.
    *
    * @return string The URL for the video.
    */
    protected function buildSingleVideoUrl($id)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext  = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $pc           = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $providerName = $pc->calculateProviderOfVideoId($id);

        /** This is not a Vimeo video. */
        if ($providerName !== org_tubepress_api_provider_Provider::VIMEO) {
            throw new Exception("Unable to build Vimeo URL for video with ID $id");
        }

        $this->_verifyKeyAndSecretExists($execContext);

        self::_setIniArgSeparator();

        $params                       = array();
        $params[self::PARAM_METHOD]   = self::METHOD_VIDEOS_GETINFO;
        $params[self::PARAM_VIDEO_ID] = $id;

        $finalUrl = $this->_buildUrl($params, $execContext);

        self::_restoreIniArgSeparator();

        return $finalUrl;
    }

    /**
    * Return the name of the provider for which this command can handle.
    *
    * @return string The name of the video provider that this command can handle.
    */
    protected function getHandledProviderName()
    {
        return org_tubepress_api_provider_Provider::VIMEO;
    }

    private function _getSort($mode, org_tubepress_api_exec_ExecutionContext $execContext)
    {
        /* these two modes can't be sorted */
        if ($mode == org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL
            || $mode == org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_ALBUM) {
            return '';
        }

        $order = $execContext->get(org_tubepress_api_const_options_names_Feed::ORDER_BY);

        if ($mode == org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH
            && $order == org_tubepress_api_const_options_values_OrderByValue::RELEVANCE) {
               return self::SORT_RELEVANT;
        }

        if ($mode == org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_GROUP
            && $order == org_tubepress_api_const_options_values_OrderByValue::RANDOM) {
            return $order;
        }

        if ($order == org_tubepress_api_const_options_values_OrderByValue::VIEW_COUNT) {
            return self::SORT_MOST_PLAYED;
        }

        if ($order == org_tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT) {
            return self::SORT_MOST_COMMENTS;
        }

        if ($order == org_tubepress_api_const_options_values_OrderByValue::RATING) {
            return self::SORT_MOST_LIKED;
        }

        if ($order == org_tubepress_api_const_options_values_OrderByValue::NEWEST
            || $order == org_tubepress_api_const_options_values_OrderByValue::OLDEST) {
            return $order;
        }

        return '';
    }

    private function _buildUrl($params, org_tubepress_api_exec_ExecutionContext $execContext)
    {
        $params[self::PARAM_FORMAT]           = 'php';
        $params[self::OAUTH_CONSUMER_KEY]     = $execContext->get(org_tubepress_api_const_options_names_Feed::VIMEO_KEY);
        $params[self::OAUTH_NONCE]            = md5(uniqid(mt_rand(), true));
        $params[self::OAUTH_SIGNATURE_METHOD] = 'HMAC-SHA1';
        $params[self::OAUTH_TIMESTAMP]        = time();
        $params[self::OAUTH_VERSION]          ='1.0';
        $params[self::OAUTH_SIGNATURE]        = $this->_generateSignature($params, self::URL_BASE, $execContext);
        return self::URL_BASE . '?' . http_build_query($params);
    }

    private function _generateSignature($params, $base, org_tubepress_api_exec_ExecutionContext $execContext)
    {
        uksort($params, 'strcmp');
        $params = $this->_url_encode_rfc3986($params);

        $baseString = array('GET', $base, urldecode(http_build_query($params)));
        $baseString = $this->_url_encode_rfc3986($baseString);
        $baseString = implode('&', $baseString);

        // Make the key
        $keyParts = array($execContext->get(org_tubepress_api_const_options_names_Feed::VIMEO_SECRET), '');
        $keyParts = $this->_url_encode_rfc3986($keyParts);
        $key      = implode('&', $keyParts);

        // Generate signature
        return base64_encode(hash_hmac('sha1', $baseString, $key, true));
    }

    private function _verifyKeyAndSecretExists(org_tubepress_api_exec_ExecutionContext $execContext)
    {
        if ($execContext->get(org_tubepress_api_const_options_names_Feed::VIMEO_KEY) === '') {
            throw new Exception('Missing Vimeo API Consumer Key.');
        }
        if ($execContext->get(org_tubepress_api_const_options_names_Feed::VIMEO_SECRET) === '') {
            throw new Exception('Missing Vimeo API Consumer Secret.');
        }

    }

    /**
     * URL encode a parameter or array of parameters.
     *
     * @param array/string $input A parameter or set of parameters to encode.
     *
     * @return array/string The URL encoded parameter or array of parameters.
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

    private static function _setIniArgSeparator()
    {
    	/* Vimeo is sensitive to URL argument separators, so we have to set it to just '&' */
    	if (ini_get(self::INI_ARG_SEPARATOR) !== '&') {

    		org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Setting arg_separator.input');
    		@ini_set(self::INI_ARG_SEPARATOR, '&');
    	}
    }

    private static function _restoreIniArgSeparator()
    {
    	@ini_restore(self::INI_ARG_SEPARATOR);
    }
}
