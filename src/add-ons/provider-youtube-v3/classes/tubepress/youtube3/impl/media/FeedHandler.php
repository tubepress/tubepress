<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_youtube3_impl_media_FeedHandler implements tubepress_app_api_media_HttpFeedHandlerInterface
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_lib_api_array_ArrayReaderInterface
     */
    private $_arrayReader;

    /**
     * @var array
     */
    private $_feedAsArray;

    /**
     * @var array
     */
    private $_metadataAsArray;

    /**
     * @var tubepress_youtube3_impl_ApiUtility
     */
    private $_apiUtility;

    /**
     * @var int
     */
    private $_skippedVideoCount;

    private $_invokedAtLeastOnce;

    public function __construct(tubepress_platform_api_log_LoggerInterface     $logger,
                                tubepress_app_api_options_ContextInterface     $context,
                                tubepress_platform_api_url_UrlFactoryInterface $urlFactory,
                                tubepress_lib_api_array_ArrayReaderInterface   $arrayReader,
                                tubepress_youtube3_impl_ApiUtility             $apiUtility)
    {
        $this->_logger      = $logger;
        $this->_context     = $context;
        $this->_urlFactory  = $urlFactory;
        $this->_arrayReader = $arrayReader;
        $this->_apiUtility  = $apiUtility;
    }

    /**
     * @return string The name of this feed handler. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'youtube_v3';
    }

    /**
     * Apply any data that might be needed from the feed to build attributes for this media item.
     *
     * @param tubepress_app_api_media_MediaItem $mediaItemId The media item.
     * @param int                               $index       The zero-based index.
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getNewItemEventArguments(tubepress_app_api_media_MediaItem $mediaItemId, $index)
    {
        return array(

            'feedAsArray'     => $this->_feedAsArray,
            'metadataAsArray' => $this->_metadataAsArray,
            'zeroBasedIndex'  => $index
        );
    }

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return tubepress_platform_api_url_UrlInterface The request URL for this gallery.
     *
     * @api
     * @since 4.0.0
     */
    public function buildUrlForPage($currentPage)
    {
        $url           = $this->_urlFactory->fromString(tubepress_youtube3_impl_ApiUtility::YOUTUBE_API_URL);
        $query         = $url->getQuery();
        $requestedMode = $this->_context->get(tubepress_app_api_options_Names::GALLERY_SOURCE);

        switch ($requestedMode) {

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR:
            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_LIST:

                $this->_urlBuildingPageVideos($url, $query, $requestedMode);
                break;

            //https://developers.google.com/youtube/v3/docs/playlistItems/list
            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST:
            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES:
            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_USER:

                $this->_urlBuildingPagePlaylistItems($url, $query, $requestedMode);
                break;

            //https://developers.google.com/youtube/v3/docs/search/list
            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED:
            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH:

                $this->_urlBuildingPageSearch($url, $query, $requestedMode);
                break;

            default:

                throw new LogicException('Invalid source supplied to YouTube');
        }

        $this->_urlBuildingPageCommonParams($url, $currentPage);
        $this->_urlBuildingAddCommonParameters($url);

        return $url;
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return tubepress_platform_api_url_UrlInterface The URL for the single video given.
     *
     * @api
     * @since 4.0.0
     */
    public function buildUrlForItem($id)
    {
        $url = $this->_urlFactory->fromString(tubepress_youtube3_impl_ApiUtility::YOUTUBE_API_URL);
        $url->addPath(tubepress_youtube3_impl_ApiUtility::PATH_VIDEOS);

        $query = $url->getQuery();
        $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_VIDEOS_ID, $id);

        $this->_urlBuildingAddCommonParameters($url);

        return $url;
    }

    /**
     * Count the total videos in this feed result.
     *
     * @return int The total result count of this query.
     *
     * @api
     * @since 4.0.0
     */
    public function getTotalResultCount()
    {
        $query = sprintf('%s.%s',
            tubepress_youtube3_impl_ApiUtility::RESPONSE_PAGEINFO,
            tubepress_youtube3_impl_ApiUtility::RESPONSE_PAGEINFO_TOTALRESULTS
        );

        $totalResults = $this->_arrayReader->getAsInteger($this->_feedAsArray, $query, 0);
        $totalResults = intval($totalResults);

        return $totalResults - $this->_skippedVideoCount;
    }

    /**
     * Determine why the given item cannot be used.
     *
     * @param integer $index The index into the feed.
     *
     * @return string The reason why we can't work with this video, or null if we can.
     *
     * @api
     * @since 4.0.0
     */
    public function getReasonUnableToUseItemAtIndex($index)
    {
        $items = $this->_arrayReader->getAsArray($this->_feedAsArray, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS, array());

        if (!isset($items[$index])) {

            return null;
        }

        $item  = $items[$index];
        $title = $this->_arrayReader->getAsString($item, sprintf('%s.%s',
            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET_TITLE));
        $desc  = $this->_arrayReader->getAsString($item, sprintf('%s.%s',
            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET_DESCRIPTION));

        if ($title === 'Deleted video' && $desc === 'This video is unavailable.') {

            return 'Video has been deleted';
        }

        if ($title === 'Private video' && $desc === 'This video is private.') {

            return 'Video is private';
        }

        return null;
    }

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @return integer A count of videos in this feed.
     *
     * @api
     * @since 4.0.0
     */
    public function getCurrentResultCount()
    {
        $items = $this->_arrayReader->getAsArray($this->_feedAsArray, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS, array());

        return count($items);
    }

    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed $feed The feed to construct.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function onAnalysisStart($feed)
    {
        $this->_skippedVideoCount = 0;
        $loggerEnabled            = $this->_logger->isEnabled();
        $this->_feedAsArray       = json_decode($feed, true);

        if ($this->_feedAsArray === null) {

            throw new RuntimeException('Unable to decode JSON from YouTube');
        }

        if ($loggerEnabled) {

            $this->_logger->debug(sprintf('Decoded feed from YouTube is visible in the HTML source of this page.<span style="display:none">%s</span>',

                htmlspecialchars(print_r($this->_feedAsArray, true))
            ));
        }

        $this->_apiUtility->checkForApiResponseError($this->_feedAsArray);

        $this->_metadataAsArray = $this->_collectMetadata();

        if ($loggerEnabled) {

            $this->_logger->debug(sprintf('Decoded metadata collected from YouTube is visible in the HTML source of this page.<span style="display:none">%s</span>',

                htmlspecialchars(print_r($this->_metadataAsArray, true))
            ));
        }
    }

    /**
     * Perform post-construction activites for the feed.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function onAnalysisComplete()
    {
        unset($this->_feedAsArray);
        unset($this->_metadataAsArray);
    }

    /**
     * Get the item ID of an element of the feed.
     *
     * @param integer $index The index into the feed.
     *
     * @return string The globally unique item ID.
     *
     * @api
     * @since 4.0.0
     */
    public function getIdForItemAtIndex($index)
    {
        $items = $this->_arrayReader->getAsArray($this->_metadataAsArray, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS, array());
        $id    = '';

        if (isset($items[$index])) {

            $item = $items[$index];
            $id   = $this->_arrayReader->getAsString($item, tubepress_youtube3_impl_ApiUtility::RESOURCE_ID);
        }

        if ($id === '') {

            return null;
        }

        return $id;
    }

    private function _urlBuildingAddCommonParameters(tubepress_platform_api_url_UrlInterface $url)
    {
        $part = sprintf('%s,%s',
            tubepress_youtube3_impl_ApiUtility::PART_ID,
            tubepress_youtube3_impl_ApiUtility::PART_SNIPPET
        );
        $key  = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_API_KEY);

        /**
         * Check to see if we're using the short-lived "shared" API key from TubePress 4.1.0 - 4.1.6.
         */
        if (!$key || $key === 'AIzaSyDENt00ayilKKoHolD9WGB_b9zvDjiHIso') {

            if (defined('ABSPATH') && defined('DB_NAME')) {

                throw new RuntimeException('Invalid Google API key. Please follow these instructions to fix: http://support.tubepress.com/customer/portal/articles/2026361-initial-setup');
            }

            throw new RuntimeException('Invalid Google API key. Please follow these instructions to fix: http://support.tubepress.com/customer/portal/articles/2029702-initial-setup');
        }

        $url->getQuery()->set(tubepress_youtube3_impl_ApiUtility::QUERY_APIKEY, $key)
                        ->set(tubepress_youtube3_impl_ApiUtility::QUERY_PART, $part);
    }

    /**
     * https://developers.google.com/youtube/v3/migration-guide#favorites
     *
     * @param $userChannelId
     *
     * return string|null
     */
    private function _urlBuildingDiscoverFavoritesChannelId($userChannelId)
    {
        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Looking up the channel ID for the videos favorited by %s', $userChannelId));
        }

        $channelListUrl = $this->_urlFactory->fromString(tubepress_youtube3_impl_ApiUtility::YOUTUBE_API_URL);
        $part           = tubepress_youtube3_impl_ApiUtility::PART_CHANNEL_CONTENT_DETAILS;
        $fields         = sprintf('%s,%s/%s',
            tubepress_youtube3_impl_ApiUtility::RESPONSE_ETAG,
            tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_CHANNEL_CONTENTDETAILS
        );

        $channelListUrl->addPath(tubepress_youtube3_impl_ApiUtility::PATH_CHANNELS);
        $channelListUrl->getQuery()->set(tubepress_youtube3_impl_ApiUtility::QUERY_CHANNELS_ID, $userChannelId)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_PART,        $part)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_MAX_RESULTS, 1)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_FIELDS,      $fields);

        $response           = $this->_apiUtility->getDecodedApiResponse($channelListUrl);
        $responseItems      = $this->_arrayReader->getAsArray($response, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS);
        $favoritesChannelId = '';

        if (count($responseItems) > 0) {

            $firstItem          = $responseItems[0];
            $favoritesChannelId = $this->_arrayReader->getAsString($firstItem, sprintf('%s.%s.%s',

                tubepress_youtube3_impl_ApiUtility::RESOURCE_CHANNEL_CONTENTDETAILS,
                tubepress_youtube3_impl_ApiUtility::RESOURCE_CHANNEL_CONTENTDETAILS_RELATED_PLAYLISTS,
                tubepress_youtube3_impl_ApiUtility::RESOURCE_CHANNEL_CONTENTDETAILS_RELATED_PLAYLISTS_FAVORITES
            ));
        }

        if ($favoritesChannelId === '') {

            throw new InvalidArgumentException(sprintf('Favorites for channel %s are not public.', $userChannelId));
        }

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Favorites channel ID for channel ID %s is %s', $userChannelId, $favoritesChannelId));
        }

        return $favoritesChannelId;
    }

    /**
     * https://developers.google.com/youtube/v3/migration-guide#favorites
     *
     * @param $userChannelId
     *
     * return string|null
     */
    private function _urlBuildingDiscoverUploadsChannelId($userChannelId)
    {
        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Looking up the channel ID for the videos uploaded by %s', $userChannelId));
        }

        $channelListUrl = $this->_urlFactory->fromString(tubepress_youtube3_impl_ApiUtility::YOUTUBE_API_URL);
        $part           = tubepress_youtube3_impl_ApiUtility::PART_CHANNEL_CONTENT_DETAILS;
        $fields         = sprintf('%s,%s/%s',
            tubepress_youtube3_impl_ApiUtility::RESPONSE_ETAG,
            tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_CHANNEL_CONTENTDETAILS
        );

        $channelListUrl->addPath(tubepress_youtube3_impl_ApiUtility::PATH_CHANNELS);
        $channelListUrl->getQuery()->set(tubepress_youtube3_impl_ApiUtility::QUERY_CHANNELS_ID, $userChannelId)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_PART,        $part)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_MAX_RESULTS, 1)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_FIELDS,      $fields);

        $response           = $this->_apiUtility->getDecodedApiResponse($channelListUrl);
        $responseItems      = $this->_arrayReader->getAsArray($response, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS);
        $favoritesChannelId = '';

        if (count($responseItems) > 0) {

            $firstItem      = $responseItems[0];
            $favoritesChannelId = $this->_arrayReader->getAsString($firstItem, sprintf('%s.%s.%s',

                tubepress_youtube3_impl_ApiUtility::RESOURCE_CHANNEL_CONTENTDETAILS,
                tubepress_youtube3_impl_ApiUtility::RESOURCE_CHANNEL_CONTENTDETAILS_RELATED_PLAYLISTS,
                tubepress_youtube3_impl_ApiUtility::RESOURCE_CHANNEL_CONTENTDETAILS_RELATED_PLAYLISTS_UPLOADS
            ));
        }

        if ($favoritesChannelId === '') {

            throw new InvalidArgumentException(sprintf('Uploads for channel %s are not public.', $userChannelId));
        }

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Uploads channel ID for channel ID %s is %s', $userChannelId, $favoritesChannelId));
        }

        return $favoritesChannelId;
    }

    /**
     * https://developers.google.com/youtube/v3/guides/working_with_channel_ids#v3
     *
     * @param $candidate
     *
     * @return string
     */
    private function _urlBuildingConvertUserOrChannelToChannelId($candidate)
    {
        $debugEnabled = $this->_logger->isEnabled();

        if ($debugEnabled) {

            $this->_logger->debug(sprintf('Determining if %s a YouTube user or channel ID. First, we\'ll assume it\'s a user', $candidate));
        }

        $channelListUrl = $this->_urlFactory->fromString(tubepress_youtube3_impl_ApiUtility::YOUTUBE_API_URL);
        $part           = tubepress_youtube3_impl_ApiUtility::PART_ID;
        $fields         = sprintf('%s,%s/%s',
            tubepress_youtube3_impl_ApiUtility::RESPONSE_ETAG,
            tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_ID
        );

        $channelListUrl->addPath(tubepress_youtube3_impl_ApiUtility::PATH_CHANNELS);
        $channelListUrl->getQuery()->set(tubepress_youtube3_impl_ApiUtility::QUERY_PART, $part)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_CHANNELS_FORUSERNAME, $candidate)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_MAX_RESULTS, 1)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_FIELDS, $fields);

        $channelId = $this->_getChannelIdOrNullFromUrl($channelListUrl);

        if ($channelId) {

            if ($debugEnabled) {

                $this->_logger->debug(sprintf('%s is a YouTube user with channel ID %s', $candidate, $channelId));
            }

            return $channelId;
        }

        if ($debugEnabled) {

            $this->_logger->debug(sprintf('%s does not appear to be a YouTube user. See if it is an exact channel ID', $candidate));
        }

        $channelListUrl->getQuery()->remove(tubepress_youtube3_impl_ApiUtility::QUERY_CHANNELS_FORUSERNAME)
                                   ->set(tubepress_youtube3_impl_ApiUtility::QUERY_CHANNELS_ID, $candidate);

        $channelId = $this->_getChannelIdOrNullFromUrl($channelListUrl);

        if ($channelId) {

            if ($debugEnabled) {

                $this->_logger->debug(sprintf('%s is an exact channel ID.', $channelId));
            }

            return $channelId;
        }

        if ($debugEnabled) {

            $this->_logger->debug(sprintf('%s does not appear to be a YouTube user or an exact channel ID. Last resort - trying to add "UC" in front of it.', $candidate));
        }

        $channelListUrl->getQuery()->set(tubepress_youtube3_impl_ApiUtility::QUERY_CHANNELS_ID, "UC$candidate");

        $channelId = $this->_getChannelIdOrNullFromUrl($channelListUrl);

        if ($channelId) {

            if ($debugEnabled) {

                $this->_logger->debug(sprintf('%s is a valid channel ID, we will use that instead.', $channelId));
            }

            return $channelId;
        }

        throw new InvalidArgumentException(sprintf('%s is not a valid YouTube user or channel', $candidate));
    }

    private function _getChannelIdOrNullFromUrl(tubepress_platform_api_url_UrlInterface $url)
    {
        $response      = $this->_apiUtility->getDecodedApiResponse($url);
        $responseItems = $this->_arrayReader->getAsArray($response, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS);

        if (count($responseItems) > 0) {

            $firstItem = $responseItems[0];
            $channelId = $this->_arrayReader->getAsString($firstItem, tubepress_youtube3_impl_ApiUtility::RESOURCE_ID);

            if ($channelId !== '') {

                return $channelId;
            }
        }

        return null;
    }

    private function _urlBuildingPageCommonParams(tubepress_platform_api_url_UrlInterface $url, $currentPage)
    {
        if (isset($this->_invokedAtLeastOnce)) {

            $perPage = $this->_context->get(tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE);

        } else {

            $perPage = min($this->_context->get(tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE), ceil(2.07));
        }

        $url->getQuery()->set(tubepress_youtube3_impl_ApiUtility::QUERY_MAX_RESULTS, $perPage);

        if ($currentPage === 1) {

            return;
        }

        $clone     = $url->getClone();
        $query     = $clone->getQuery();
        $nextToken = null;

        $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_PART,   tubepress_youtube3_impl_ApiUtility::PART_ID);
        $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_FIELDS, tubepress_youtube3_impl_ApiUtility::RESPONSE_NEXT_PAGE_TOKEN);

        for ($page = 2; $page <= $currentPage; $page++) {

            if ($nextToken !== null) {

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_PAGETOKEN, $nextToken);
            }

            $result = $this->_apiUtility->getDecodedApiResponse($clone);

            if (!isset($result[tubepress_youtube3_impl_ApiUtility::RESPONSE_NEXT_PAGE_TOKEN])) {

                throw new RuntimeException('Failed to retrieve pagination tokens');
            }

            $nextToken = $result[tubepress_youtube3_impl_ApiUtility::RESPONSE_NEXT_PAGE_TOKEN];
        }

        $url->getQuery()->set(tubepress_youtube3_impl_ApiUtility::QUERY_PAGETOKEN, $nextToken);
    }

    private function _urlBuildingPageSearchOrderBy(tubepress_platform_api_url_UrlInterface $url)
    {
        $requestedSortOrder = $this->_context->get(tubepress_app_api_options_Names::FEED_ORDER_BY);
        $query              = $url->getQuery();

        if ($requestedSortOrder === tubepress_app_api_options_AcceptableValues::ORDER_BY_DEFAULT) {

            $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_ORDER, tubepress_youtube3_api_Constants::ORDER_BY_RELEVANCE);
            return;
        }

        if ($requestedSortOrder === tubepress_youtube3_api_Constants::ORDER_BY_NEWEST) {

            $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_ORDER, 'date');
            return;
        }

        if ($requestedSortOrder == tubepress_youtube3_api_Constants::ORDER_BY_VIEW_COUNT) {

            $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_ORDER, tubepress_youtube3_api_Constants::ORDER_BY_VIEW_COUNT);

            return;
        }

        if (in_array($requestedSortOrder, array(
            tubepress_youtube3_api_Constants::ORDER_BY_RELEVANCE,
            tubepress_youtube3_api_Constants::ORDER_BY_RATING,
            tubepress_youtube3_api_Constants::ORDER_BY_TITLE))) {

            $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_ORDER, $requestedSortOrder);
        }
    }

    //https://developers.google.com/youtube/v3/docs/videos/list
    private function _urlBuildingPageVideos(tubepress_platform_api_url_UrlInterface $url,
                                            tubepress_platform_api_url_QueryInterface $query,
                                            $requestedMode)
    {
        $url->addPath(tubepress_youtube3_impl_ApiUtility::PATH_VIDEOS);

        switch ($requestedMode) {

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_LIST:

                $ids = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_LIST_VALUE);
                $ids = preg_split('/\s*,\s*/', $ids);
                $ids = implode(',', $ids);

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_VIDEOS_ID, $ids);

                break;

            default:

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_VIDEOS_CHART, 'mostPopular');
        }
    }

    //https://developers.google.com/youtube/v3/docs/playlistItems/list
    private function _urlBuildingPagePlaylistItems(tubepress_platform_api_url_UrlInterface $url,
                                                   tubepress_platform_api_url_QueryInterface $query,
                                                   $requestedMode)
    {
        $url->addPath(tubepress_youtube3_impl_ApiUtility::PATH_PLAYLIST_ITEMS);

        switch ($requestedMode) {

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST:

                $playlistId = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE);

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_PLITEMS_PL_ID, $playlistId);

                break;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES:

                $username           = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE);
                $userChannelId      = $this->_urlBuildingConvertUserOrChannelToChannelId($username);
                $favoritesChannelId = $this->_urlBuildingDiscoverFavoritesChannelId($userChannelId);

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_PLITEMS_PL_ID, $favoritesChannelId);

                break;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_USER:

                $username         = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_USER_VALUE);
                $userChannelId    = $this->_urlBuildingConvertUserOrChannelToChannelId($username);
                $uploadsChannelId = $this->_urlBuildingDiscoverUploadsChannelId($userChannelId);

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_PLITEMS_PL_ID, $uploadsChannelId);

                break;

            default:

                break;
        }
    }

    private function _urlBuildingPageSearch(tubepress_platform_api_url_UrlInterface $url,
                                            tubepress_platform_api_url_QueryInterface $query,
                                            $requestedMode)
    {
        $url->addPath(tubepress_youtube3_impl_ApiUtility::PATH_SEARCH);

        switch ($requestedMode) {

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED:

                $videoId = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_RELATED_VALUE);

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_RELATED, $videoId);

                break;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH:

                $tags   = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_TAG_VALUE);
                $tags   = str_replace(array('&#8216', '&#8217', '&#8242;', '&#34', '&#8220;', '&#8221;', '&#8243;'), '"', $tags);

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_Q, $tags);

                break;

            default:

                break;
        }

        $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_TYPE, 'video')
            ->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_SYNDICATED, 'true');

        if ($this->_context->get(tubepress_youtube3_api_Constants::OPTION_EMBEDDABLE_ONLY)) {

            $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_EMBEDDABLE, 'true');
        }

        $restrictToUser = $this->_context->get(tubepress_app_api_options_Names::SEARCH_ONLY_USER);

        if ($restrictToUser) {

            $userChannelId = $this->_urlBuildingConvertUserOrChannelToChannelId($restrictToUser);

            $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_CHANNEL_ID, $userChannelId);
        }

        switch ($this->_context->get(tubepress_youtube3_api_Constants::OPTION_FILTER)) {

            case tubepress_youtube3_api_Constants::SAFESEARCH_STRICT:

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_SAFESEARCH, 'strict');
                break;

            case tubepress_youtube3_api_Constants::SAFESEARCH_MODERATE:

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_SAFESEARCH, 'moderate');
                break;

            default:

                $query->set(tubepress_youtube3_impl_ApiUtility::QUERY_SEARCH_SAFESEARCH, 'none');
        }

        $this->_urlBuildingPageSearchOrderBy($url);
    }

    private function _collectMetadata()
    {
        $idQueriesToTest = array(

            sprintf('%s.%s.%s',
                tubepress_youtube3_impl_ApiUtility::RESOURCE_PLITEM_SNIPPET,
                tubepress_youtube3_impl_ApiUtility::RESOURCE_PLITEM_SNIPPET_RESOURCE_ID,
                tubepress_youtube3_impl_ApiUtility::RESOURCE_PLITEM_SNIPPET_RESOURCE_ID_VIDEO_ID),

            sprintf('%s.%s',
                tubepress_youtube3_impl_ApiUtility::RESOURCE_SEARCH_ID,
                tubepress_youtube3_impl_ApiUtility::RESOURCE_SEARCH_ID_VIDEO_ID),

            tubepress_youtube3_impl_ApiUtility::RESOURCE_ID,
        );

        $items         = $this->_arrayReader->getAsArray($this->_feedAsArray, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS, array());
        $ids           = array();
        $selectedQuery = null;

        for ($index = 0; $index < count($items); $index++) {

            if ($this->getReasonUnableToUseItemAtIndex($index) !== null) {

                if ($this->_logger->isEnabled()) {

                    $reason = $this->getReasonUnableToUseItemAtIndex($index);

                    $this->_logger->debug(sprintf('Skipping video at index %d: %s', $index, $reason));
                }

                $this->_skippedVideoCount++;

                unset($this->_feedAsArray[tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS][$index]);

                continue;
            }

            $item = $items[$index];

            if ($selectedQuery === null) {

                foreach ($idQueriesToTest as $query) {

                    if ($this->_arrayReader->getAsString($item, $query) !== '') {

                        $selectedQuery = $query;
                        break;
                    }
                }
            }

            if ($selectedQuery === null) {

                throw new RuntimeException('Unable to determine query to get video IDs');
            }

            $id = $this->_arrayReader->getAsString($item, $selectedQuery);

            if ($id == '') {

                throw new RuntimeException('Unable to determine ID for a video in the result.');
            }

            $ids[] = $id;
        }

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Making API call to collect metadata for %d video(s): [ %s ]',
                count($ids), implode(', ', $ids)));
        }

        $url = $this->_urlFactory->fromString(tubepress_youtube3_impl_ApiUtility::YOUTUBE_API_URL);
        $url->addPath(tubepress_youtube3_impl_ApiUtility::PATH_VIDEOS);

        $partsToRequest  = array(tubepress_youtube3_impl_ApiUtility::PART_ID, tubepress_youtube3_impl_ApiUtility::PART_SNIPPET);
        $fieldsToRequest = array(tubepress_youtube3_impl_ApiUtility::RESOURCE_ID, tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET);

        if ($this->_context->get(tubepress_youtube3_api_Constants::OPTION_META_COUNT_COMMENTS) ||
            $this->_context->get(tubepress_youtube3_api_Constants::OPTION_META_COUNT_DISLIKES) ||
            $this->_context->get(tubepress_youtube3_api_Constants::OPTION_META_COUNT_LIKES) ||
            $this->_context->get(tubepress_youtube3_api_Constants::OPTION_META_COUNT_FAVORITES) ||
            $this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_VIEWS)) {

            $partsToRequest[]  = tubepress_youtube3_impl_ApiUtility::PART_VIDEO_STATISTICS;
            $fieldsToRequest[] = tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS;
        }

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_LENGTH)) {

            $partsToRequest[]  = tubepress_youtube3_impl_ApiUtility::PART_VIDEO_CONTENT_DETAILS;
            $fieldsToRequest[] = tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_CONTENT_DETAILS;
        }

        $fields = sprintf('%s,%s(%s)',
            tubepress_youtube3_impl_ApiUtility::RESPONSE_ETAG,
            tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS,
            implode(',', $fieldsToRequest));

        /**
         * author           //snippet.channelId and snippet.channelTitle
         * category         //snippet.categoryId
         * comments count   //statistics.commentCount
         * date uploaded    //snippet.publishedAt
         * description      //snippet.description
         * disklikes count  //statistics.disklikeCount
         * favorites count  //statistics.favoriteCount
         * id               //id
         * length           //contentDetails.duration
         * likes count      //statistics.likeCount
         * tags             //snippet.tags
         * title            //snippet.title
         * url              //https://youtu.be/<id>
         * view count       //statistics.viewCount
         */
        $url->getQuery()->set(tubepress_youtube3_impl_ApiUtility::QUERY_VIDEOS_ID, implode(',', $ids))
            ->set(tubepress_youtube3_impl_ApiUtility::QUERY_PART, implode(',', $partsToRequest))
            ->set(tubepress_youtube3_impl_ApiUtility::QUERY_FIELDS, $fields);

        return $this->_apiUtility->getDecodedApiResponse($url);
    }

    public function __invoke()
    {
        $this->_invokedAtLeastOnce = true;
    }
}