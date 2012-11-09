<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Builds URLs to send out to YouTube for gdata
 *
 */
class tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder implements tubepress_spi_provider_UrlBuilder
{
    private static $_URL_PARAM_FORMAT      = 'format';
    private static $_URL_PARAM_KEY         = 'key';
    private static $_URL_PARAM_MAX_RESULTS = 'max-results';
    private static $_URL_PARAM_ORDER       = 'orderby';
    private static $_URL_PARAM_SAFESEARCH  = 'safeSearch';
    private static $_URL_PARAM_START_INDEX = 'start-index';
    private static $_URL_PARAM_VERSION     = 'v';

    /**
     * Builds a gdata request url for a list of videos
     *
     * @param int $currentPage The current page of the gallery.
     *
     * @return string The gdata request URL for this gallery
     */
    public final function buildGalleryUrl($currentPage)
    {
        $execContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();

        switch ($execContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE)) {

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER:

                $url = 'users/' . $execContext->get(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE) . '/uploads';

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED:

                $url = 'standardfeeds/top_rated?time=' . $execContext->get(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE);

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR:

                $url = 'standardfeeds/most_popular?time=' . $execContext->get(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE);

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST:

                $url = 'playlists/' . $execContext->get(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED:

                $url = 'standardfeeds/most_responded';

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT:

                $url = 'standardfeeds/most_recent';

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES:

                $url = 'standardfeeds/top_favorites';

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED:

                $url = 'standardfeeds/most_discussed';

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED:

                $url = 'videos/' . $execContext->get(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_RELATED_VALUE) . '/related';

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES:

                $url = 'videos/' . $execContext->get(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_RESPONSES_VALUE) . '/responses';

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES:

                $url = 'users/' . $execContext->get(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE) . '/favorites';

                break;

            case tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH:

                $tags = $execContext->get(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE);
                $tags = self::_replaceQuotes($tags);
                $tags = urlencode($tags);
                $url  = "videos?q=$tags";

                $filter = $execContext->get(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);

                if ($filter != '') {

                    $url .= "&author=$filter";
                }

                break;

            default:

                $url = 'standardfeeds/recently_featured';

                break;
        }

        $request = new ehough_curly_Url("http://gdata.youtube.com/feeds/api/$url");

        $this->_urlPostProcessingCommon($execContext, $request);

        $this->_urlPostProcessingGallery($execContext, $request, $currentPage);

        return $request->toString();
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
        $context    = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $requestURL = new ehough_curly_Url("http://gdata.youtube.com/feeds/api/videos/$id");

        $this->_urlPostProcessingCommon($context, $requestURL);

        return $requestURL->toString();
    }

    private static function _replaceQuotes($text)
    {
        return str_replace(array('&#8216', '&#8217', '&#8242;', '&#34', '&#8220;', '&#8221;', '&#8243;'), '"', $text);
    }

    private function _urlPostProcessingCommon(tubepress_spi_context_ExecutionContext $execContext, ehough_curly_Url $url)
    {
        $url->setQueryVariable(self::$_URL_PARAM_VERSION, 2);
        $url->setQueryVariable(self::$_URL_PARAM_KEY, $execContext->get(tubepress_plugins_youtube_api_const_options_names_Feed::DEV_KEY));
    }

    private function _urlPostProcessingGallery(tubepress_spi_context_ExecutionContext $execContext, ehough_curly_Url $url, $currentPage)
    {
        $perPage = $execContext->get(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);

        /* start index of the videos */
        $start = ($currentPage * $perPage) - $perPage + 1;

        $url->setQueryVariable(self::$_URL_PARAM_START_INDEX, $start);
        $url->setQueryVariable(self::$_URL_PARAM_MAX_RESULTS, $perPage);

        $this->_urlProcessingOrderBy($execContext, $url);

        $url->setQueryVariable(self::$_URL_PARAM_SAFESEARCH, $execContext->get(tubepress_plugins_youtube_api_const_options_names_Feed::FILTER));

        if ($execContext->get(tubepress_plugins_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY)) {

            $url->setQueryVariable(self::$_URL_PARAM_FORMAT, '5');
        }
    }

    private function _urlProcessingOrderBy(tubepress_spi_context_ExecutionContext $execContext, ehough_curly_Url $url)
    {
        $order = $execContext->get(tubepress_api_const_options_names_Feed::ORDER_BY);
        $mode  = $execContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        if ($order == tubepress_api_const_options_values_OrderByValue::RANDOM) {

            return;
        }

        /* any feed can take these */
        if ($order == tubepress_api_const_options_values_OrderByValue::VIEW_COUNT) {

            $url->setQueryVariable(self::$_URL_PARAM_ORDER, $order);

            return;
        }

        if ($order == tubepress_api_const_options_values_OrderByValue::NEWEST) {

            $url->setQueryVariable(self::$_URL_PARAM_ORDER, 'published');

            return;
        }

        /* playlist specific stuff */
        if ($mode == tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST) {

            if (in_array($order, array(

                tubepress_api_const_options_values_OrderByValue::POSITION,
                tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT,
                tubepress_api_const_options_values_OrderByValue::DURATION,
                tubepress_api_const_options_values_OrderByValue::TITLE))) {
                $url->setQueryVariable(self::$_URL_PARAM_ORDER, $order);
            }
            
            return;
        }

        if (in_array($order, array(tubepress_api_const_options_values_OrderByValue::RELEVANCE, tubepress_api_const_options_values_OrderByValue::RATING))) {

            $url->setQueryVariable(self::$_URL_PARAM_ORDER, $order);
        }
    }


}