<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Builds URLs to send out to Vimeo
 *
 */
class tubepress_plugins_vimeo_impl_provider_VimeoUrlBuilder implements tubepress_spi_provider_UrlBuilder
{
    private static $_PARAM_ALBUM_ID      = 'album_id';
    private static $_PARAM_CHANNEL_ID    = 'channel_id';
    private static $_PARAM_FORMAT        = 'format';
    private static $_PARAM_FULL_RESPONSE = 'full_response';
    private static $_PARAM_GROUP_ID      = 'group_id';
    private static $_PARAM_METHOD        = 'method';
    private static $_PARAM_PAGE          = 'page';
    private static $_PARAM_PER_PAGE      = 'per_page';
    private static $_PARAM_QUERY         = 'query';
    private static $_PARAM_SORT          = 'sort';
    private static $_PARAM_USER_ID       = 'user_id';
    private static $_PARAM_VIDEO_ID      = 'video_id';

    private static $_METHOD_ALBUM_GETVIDEOS    = 'vimeo.albums.getVideos';
    private static $_METHOD_CHANNEL_GETVIDEOS  = 'vimeo.channels.getVideos';
    private static $_METHOD_GROUP_GETVIDEOS    = 'vimeo.groups.getVideos';
    private static $_METHOD_VIDEOS_APPEARSIN   = 'vimeo.videos.getAppearsIn';
    private static $_METHOD_VIDEOS_GETALL      = 'vimeo.videos.getAll';
    private static $_METHOD_VIDEOS_GETINFO     = 'vimeo.videos.getInfo';
    private static $_METHOD_VIDEOS_GETLIKES    = 'vimeo.videos.getLikes';
    private static $_METHOD_VIDEOS_GETUPLOADED = 'vimeo.videos.getUploaded';
    private static $_METHOD_VIDEOS_SEARCH      = 'vimeo.videos.search';

    private static $_OAUTH_CONSUMER_KEY     = 'oauth_consumer_key';
    private static $_OAUTH_NONCE            = 'oauth_nonce';
    private static $_OAUTH_SIGNATURE_METHOD = 'oauth_signature_method';
    private static $_OAUTH_TIMESTAMP        = 'oauth_timestamp';
    private static $_OAUTH_VERSION          = 'oauth_version';
    private static $_OAUTH_SIGNATURE        = 'oauth_signature';

    private static $_SORT_MOST_COMMENTS = 'most_commented';
    private static $_SORT_MOST_LIKED    = 'most_liked';
    private static $_SORT_MOST_PLAYED   = 'most_played';
    private static $_SORT_RELEVANT      = 'relevant';
    private static $_SORT_NEWEST        = 'newest';
    private static $_SORT_OLDEST        = 'oldest';
    private static $_SORT_RANDOM        = 'random';

    private static $_INI_ARG_SEPARATOR = 'arg_separator.input';

    private static $_URL_BASE = 'http://vimeo.com/api/rest/v2';

    /**
     * Build a gallery URL for the given page.
     *
     * @param int $currentPage The page number.
     *
     * @return string The gallery URL.
     */
    public final function buildGalleryUrl($currentPage)
    {
        $params       = array();
        $execContext  = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $mode         = $execContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        $this->_verifyKeyAndSecretExists($execContext);

        self::_setIniArgSeparator();

        switch ($mode) {

        case tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY:

            $params[self::$_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETUPLOADED;
            $params[self::$_PARAM_USER_ID] = $execContext->get(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE);

            break;

        case tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES:

            $params[self::$_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETLIKES;
            $params[self::$_PARAM_USER_ID] = $execContext->get(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE);

            break;

        case tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN:

            $params[self::$_PARAM_METHOD]  = self::$_METHOD_VIDEOS_APPEARSIN;
            $params[self::$_PARAM_USER_ID] = $execContext->get(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE);

            break;

        case tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH:

            $params[self::$_PARAM_METHOD] = self::$_METHOD_VIDEOS_SEARCH;
            $params[self::$_PARAM_QUERY]  = $execContext->get(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE);

            $filter = $execContext->get(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);

            if ($filter != '') {

                $params[self::$_PARAM_USER_ID] = $filter;
            }

            break;

        case tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED:

            $params[self::$_PARAM_METHOD]  = self::$_METHOD_VIDEOS_GETALL;
            $params[self::$_PARAM_USER_ID] = $execContext->get(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE);

            break;

        case tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL:

            $params[self::$_PARAM_METHOD]     = self::$_METHOD_CHANNEL_GETVIDEOS;
            $params[self::$_PARAM_CHANNEL_ID] = $execContext->get(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE);

            break;

        case tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM:

            $params[self::$_PARAM_METHOD]   = self::$_METHOD_ALBUM_GETVIDEOS;
            $params[self::$_PARAM_ALBUM_ID] = $execContext->get(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);

            break;

        case tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP:

            $params[self::$_PARAM_METHOD]   = self::$_METHOD_GROUP_GETVIDEOS;
            $params[self::$_PARAM_GROUP_ID] = $execContext->get(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE);
        }

        $params[self::$_PARAM_FULL_RESPONSE] = 'true';
        $params[self::$_PARAM_PAGE]          = $currentPage;
        $params[self::$_PARAM_PER_PAGE]      = $execContext->get(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
        $sort                                = $this->_getSort($mode, $execContext);

        if ($sort != '') {

            $params[self::$_PARAM_SORT] = $sort;
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
     * @throws InvalidArgumentException If we can't build a URL for the given ID.
     *
     * @return string The URL for the video.
     */
    public final function buildSingleVideoUrl($id)
    {
        $execContext  = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();

        $this->_verifyKeyAndSecretExists($execContext);

        self::_setIniArgSeparator();

        $params                         = array();
        $params[self::$_PARAM_METHOD]   = self::$_METHOD_VIDEOS_GETINFO;
        $params[self::$_PARAM_VIDEO_ID] = $id;

        $finalUrl = $this->_buildUrl($params, $execContext);

        self::_restoreIniArgSeparator();

        return $finalUrl;
    }

    private function _getSort($mode, tubepress_spi_context_ExecutionContext $execContext)
    {
        /**
         * 'vimeoUploadedBy'    : newest, oldest, most_played, most_commented, or most_liked
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getUploaded
         *
         * 'vimeoLikes'         : newest, oldest, most_played, most_commented, or most_liked
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getLikes
         *
         * 'vimeoAppearsIn'     : newest, oldest, most_played, most_commented, or most_liked
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getAppearsIn
         *
         * 'vimeoSearch'        : newest, oldest, most_played, most_commented, or most_liked, or relevant
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getByTag
         *
         * 'vimeoCreditedTo'    : newest, oldest, most_played, most_commented, or most_liked
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.videos.getAll
         *
         * 'vimeoChannel'       : N/A
         * 'vimeoAlbum'         : N/A
         *
         * 'vimeoGroup'         : newest, oldest, most_played, most_commented, most_liked, or random
         *                      https://developer.vimeo.com/apis/advanced/methods/vimeo.groups.getVideos
         */

        /* these two modes can't be sorted */
        if ($mode == tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL
            || $mode == tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM) {

            return '';
        }

        $order = $execContext->get(tubepress_api_const_options_names_Feed::ORDER_BY);

        /* handle "relevance" sort */
        if ($mode == tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH
            && $order == tubepress_api_const_options_values_OrderByValue::RELEVANCE) {

               return self::$_SORT_RELEVANT;
        }

        /* handle "random" sort */
        if ($mode == tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP
            && $order == tubepress_api_const_options_values_OrderByValue::RANDOM) {

            return $order;
        }

        switch ($order) {

            case tubepress_api_const_options_values_OrderByValue::NEWEST:

                return self::$_SORT_NEWEST;

            case tubepress_api_const_options_values_OrderByValue::OLDEST:

                return self::$_SORT_OLDEST;

            case tubepress_api_const_options_values_OrderByValue::VIEW_COUNT:

                return self::$_SORT_MOST_PLAYED;

            case tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT:

                return self::$_SORT_MOST_COMMENTS;

            case tubepress_api_const_options_values_OrderByValue::RATING:

                return self::$_SORT_MOST_LIKED;

            default:

                return '';
        }
    }

    private function _buildUrl($params, tubepress_spi_context_ExecutionContext $execContext)
    {
        $params[self::$_PARAM_FORMAT]           = 'php';
        $params[self::$_OAUTH_CONSUMER_KEY]     = $execContext->get(tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY);
        $params[self::$_OAUTH_NONCE]            = md5(uniqid(mt_rand(), true));
        $params[self::$_OAUTH_SIGNATURE_METHOD] = 'HMAC-SHA1';
        $params[self::$_OAUTH_TIMESTAMP]        = time();
        $params[self::$_OAUTH_VERSION]          ='1.0';
        $params[self::$_OAUTH_SIGNATURE]        = $this->_generateSignature($params, self::$_URL_BASE, $execContext);

        return self::$_URL_BASE . '?' . http_build_query($params);
    }

    private function _generateSignature($params, $base, tubepress_spi_context_ExecutionContext $execContext)
    {
        uksort($params, 'strcmp');
        $params = $this->_url_encode_rfc3986($params);

        $baseString = array('GET', $base, urldecode(http_build_query($params)));
        $baseString = $this->_url_encode_rfc3986($baseString);
        $baseString = implode('&', $baseString);

        // Make the key
        $keyParts = array($execContext->get(tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET), '');
        $keyParts = $this->_url_encode_rfc3986($keyParts);
        $key      = implode('&', $keyParts);

        // Generate signature
        return base64_encode(hash_hmac('sha1', $baseString, $key, true));
    }

    private function _verifyKeyAndSecretExists(tubepress_spi_context_ExecutionContext $execContext)
    {
        if ($execContext->get(tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY) === '') {

            throw new RuntimeException('Missing Vimeo API Consumer Key.');
        }
        if ($execContext->get(tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET) === '') {

            throw new RuntimeException('Missing Vimeo API Consumer Secret.');
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
    	if (ini_get(self::$_INI_ARG_SEPARATOR) !== '&') {

    		@ini_set(self::$_INI_ARG_SEPARATOR, '&');
    	}
    }

    private static function _restoreIniArgSeparator()
    {
    	@ini_restore(self::$_INI_ARG_SEPARATOR);
    }
}
