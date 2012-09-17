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

/**
 * Builds URLs to send out to YouTube for gdata
 *
 */
class tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommand extends tubepress_impl_feed_urlbuilding_AbstractUrlBuilderCommand
{
    const PARAM_FORMAT      = 'format';
    const PARAM_KEY         = 'key';
    const PARAM_MAX_RESULTS = 'max-results';
    const PARAM_ORDER       = 'orderby';
    const PARAM_SAFESEARCH  = 'safeSearch';
    const PARAM_START_INDEX = 'start-index';
    const PARAM_VERSION     = 'v';

    /**
     * Builds a gdata request url for a list of videos
     *
     * @param int $currentPage The current page of the gallery.
     *
     * @return string The gdata request URL for this gallery
     */
    protected function buildGalleryUrl($currentPage)
    {
        $url         = '';
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        switch ($execContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE)) {

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_USER:

            $url = 'users/' . $execContext->get(tubepress_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE) . '/uploads';
            break;

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED:

            $url = 'standardfeeds/top_rated?time=' . $execContext->get(tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE);
            break;

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_VIEWED:

            $url = 'standardfeeds/most_popular?time=' . $execContext->get(tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_VIEWED_VALUE);
            break;

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST:

            $url = 'playlists/' . $execContext->get(tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
            break;

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED:

            $url = 'standardfeeds/most_responded';
            break;

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT:

            $url = 'standardfeeds/most_recent';
            break;

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES:

            $url = 'standardfeeds/top_favorites';
            break;

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED:

            $url = 'standardfeeds/most_discussed';
            break;

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES:

            $url = 'users/' . $execContext->get(tubepress_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE) . '/favorites';
            break;

        case tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH:

            $tags = $execContext->get(tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE);
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
        $this->_commonUrlPostProcessing($execContext, $request);
        $this->_galleryUrlPostProcessing($execContext, $request, $currentPage);
        return $request->toString();
    }

    /**
    * Return the name of the provider for which this command can handle.
    *
    * @return string The name of the video provider that this command can handle.
    */
    protected function getHandledProviderName()
    {
        return tubepress_spi_provider_Provider::YOUTUBE;
    }

    private static function _replaceQuotes($text)
    {
        return str_replace(array('&#8216', '&#8217', '&#8242;', '&#34', '&#8220;', '&#8221;', '&#8243;'), '"', $text);
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
    protected function buildSingleVideoUrl($id)
    {
        $context      = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $pc           = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProviderCalculator();
        $providerName = $pc->calculateProviderOfVideoId($id);

        if ($providerName !== tubepress_spi_provider_Provider::YOUTUBE) {

            throw new InvalidArgumentException("Unable to build YouTube URL for video with ID $id");
        }

        $requestURL = new ehough_curly_Url("http://gdata.youtube.com/feeds/api/videos/$id");
        $this->_commonUrlPostProcessing($context, $requestURL);

        return $requestURL->toString();
    }

    private function _commonUrlPostProcessing(tubepress_spi_context_ExecutionContext $execContext, ehough_curly_Url $url)
    {
        $url->setQueryVariable(self::PARAM_VERSION, 2);
        $url->setQueryVariable(self::PARAM_KEY, $execContext->get(tubepress_api_const_options_names_Feed::DEV_KEY));
    }

    private function _galleryUrlPostProcessing(tubepress_spi_context_ExecutionContext $execContext, ehough_curly_Url $url, $currentPage)
    {
        $perPage = $execContext->get(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);

        /* start index of the videos */
        $start = ($currentPage * $perPage) - $perPage + 1;

        $url->setQueryVariable(self::PARAM_START_INDEX, $start);
        $url->setQueryVariable(self::PARAM_MAX_RESULTS, $perPage);

        $this->_setOrderBy($execContext, $url);

        $url->setQueryVariable(self::PARAM_SAFESEARCH, $execContext->get(tubepress_api_const_options_names_Feed::FILTER));

        if ($execContext->get(tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY)) {

            $url->setQueryVariable(self::PARAM_FORMAT, '5');
        }
    }

    private function _setOrderBy(tubepress_spi_context_ExecutionContext $execContext, ehough_curly_Url $url)
    {
        $order = $execContext->get(tubepress_api_const_options_names_Feed::ORDER_BY);
        $mode  = $execContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        if ($order == tubepress_api_const_options_values_OrderByValue::RANDOM) {

            return;
        }

        /* any feed can take these */
        if ($order == tubepress_api_const_options_values_OrderByValue::VIEW_COUNT) {

            $url->setQueryVariable(self::PARAM_ORDER, $order);
            return;
        }

        if ($order == tubepress_api_const_options_values_OrderByValue::NEWEST) {

            $url->setQueryVariable(self::PARAM_ORDER, 'published');
            return;
        }

        /* playlist specific stuff */
        if ($mode == tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST) {

            if (in_array($order, array(

                tubepress_api_const_options_values_OrderByValue::POSITION,
                tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT,
                tubepress_api_const_options_values_OrderByValue::DURATION,
                tubepress_api_const_options_values_OrderByValue::TITLE))) {
                $url->setQueryVariable(self::PARAM_ORDER, $order);
            }
            return;
        }

        if (in_array($order, array(tubepress_api_const_options_values_OrderByValue::RELEVANCE, tubepress_api_const_options_values_OrderByValue::RATING))) {

            $url->setQueryVariable(self::PARAM_ORDER, $order);
        }
    }
}
