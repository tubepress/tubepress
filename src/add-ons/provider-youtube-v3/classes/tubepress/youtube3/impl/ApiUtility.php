<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_youtube3_impl_ApiUtility
{
    const YOUTUBE_API_URL = 'https://www.googleapis.com/youtube/v3/';

    /**
     * API paths.
     */
    const PATH_CHANNELS         = 'channels';
    const PATH_PLAYLIST_ITEMS   = 'playlistItems'; //favorites, uploads, playlist
    const PATH_SEARCH           = 'search';        //search, related
    const PATH_VIDEOS           = 'videos';        //most_popular
    const PATH_VIDEO_CATEGORIES = 'videoCategories';

    /**
     * Everything supports the "id" and "snippet" parts. At least so far.
     */
    const PART_ID      = 'id';
    const PART_SNIPPET = 'snippet';

    /**
     * search only supports the snippet part, so nothing special for it.
     */
    const PART_CHANNEL_CONTENT_DETAILS = 'contentDetails';
    const PART_VIDEO_CONTENT_DETAILS   = 'contentDetails';
    const PART_VIDEO_STATISTICS        = 'statistics';

    /**
     * A few common query keys.
     */
    const QUERY_APIKEY      = 'key';
    const QUERY_FIELDS      = 'fields';
    const QUERY_MAX_RESULTS = 'maxResults';
    const QUERY_PAGETOKEN   = 'pageToken';
    const QUERY_PART        = 'part';

    /**
     * Query keys specific to videos.
     */
    const QUERY_VIDEOS_ID    = 'id';
    const QUERY_VIDEOS_CHART = 'chart';

    /**
     * Query keys specific to playlistItems.
     */
    const QUERY_PLITEMS_PL_ID = 'playlistId';

    /**
     * Query keys specific to channels.
     */
    const QUERY_CHANNELS_ID          = 'id';
    const QUERY_CHANNELS_FORUSERNAME = 'forUsername';

    /**
     * Query keys specific to search.
     */
    const QUERY_SEARCH_CHANNEL_ID = 'channelId';
    const QUERY_SEARCH_EMBEDDABLE = 'videoEmbeddable';
    const QUERY_SEARCH_ORDER      = 'order';
    const QUERY_SEARCH_Q          = 'q';
    const QUERY_SEARCH_RELATED    = 'relatedToVideoId';
    const QUERY_SEARCH_SAFESEARCH = 'safeSearch';
    const QUERY_SEARCH_SYNDICATED = 'videoSyndicated';
    const QUERY_SEARCH_TYPE       = 'type';

    /**
     * Query keys specific to categories.
     */
    const QUERY_CATEGORIES_ID = 'id';

    /**
     * Common resource fields.
     */
    const RESOURCE_ID            = 'id';
    const RESOURCE_SNIPPET       = 'snippet';
    const RESOURCE_SNIPPET_TITLE = 'title';

    /**
     * channel resource fields.
     */
    const RESOURCE_CHANNEL_CONTENTDETAILS                             = 'contentDetails';
    const RESOURCE_CHANNEL_CONTENTDETAILS_RELATED_PLAYLISTS           = 'relatedPlaylists';
    const RESOURCE_CHANNEL_CONTENTDETAILS_RELATED_PLAYLISTS_FAVORITES = 'favorites';
    const RESOURCE_CHANNEL_CONTENTDETAILS_RELATED_PLAYLISTS_UPLOADS   = 'uploads';

    /**
     * playlistItem resource fields.
     */
    const RESOURCE_PLITEM_SNIPPET                      = 'snippet';
    const RESOURCE_PLITEM_SNIPPET_RESOURCE_ID          = 'resourceId';
    const RESOURCE_PLITEM_SNIPPET_RESOURCE_ID_VIDEO_ID = 'videoId';

    /**
     * search resource fields.
     */
    const RESOURCE_SEARCH_ID          = 'id';
    const RESOURCE_SEARCH_ID_VIDEO_ID = 'videoId';

    /**
     * video resource fields.
     */
    const RESOURCE_VIDEO_STATS                    = 'statistics';
    const RESOURCE_VIDEO_STATS_COMMENTS           = 'commentCount';
    const RESOURCE_VIDEO_STATS_DISLIKES           = 'dislikeCount';
    const RESOURCE_VIDEO_STATS_FAVORITES          = 'favoriteCount';
    const RESOURCE_VIDEO_STATS_LIKES              = 'likeCount';
    const RESOURCE_VIDEO_STATS_VIEWS              = 'viewCount';
    const RESOURCE_VIDEO_CONTENT_DETAILS          = 'contentDetails';
    const RESOURCE_VIDEO_CONTENT_DETAILS_DURATION = 'duration';
    const RESOURCE_VIDEO_SNIPPET                  = 'snippet';
    const RESOURCE_VIDEO_SNIPPET_CATEGORY_ID      = 'categoryId';
    const RESOURCE_VIDEO_SNIPPET_TAGS             = 'tags';
    const RESOURCE_VIDEO_SNIPPET_CHANNEL_ID       = 'channelId';
    const RESOURCE_VIDEO_SNIPPET_CHANNEL_TITLE    = 'channelTitle';
    const RESOURCE_VIDEO_SNIPPET_TITLE            = 'title';
    const RESOURCE_VIDEO_SNIPPET_DESCRIPTION      = 'description';
    const RESOURCE_VIDEO_SNIPPET_THUMBS           = 'thumbnails';
    const RESOURCE_VIDEO_SNIPPET_THUMBS_URL       = 'url';

    /**
     * Common response keys.
     */
    const RESPONSE_ETAG                  = 'etag';
    const RESPONSE_ITEMS                 = 'items';
    const RESPONSE_NEXT_PAGE_TOKEN       = 'nextPageToken';
    const RESPONSE_PAGEINFO              = 'pageInfo';
    const RESPONSE_PAGEINFO_TOTALRESULTS = 'totalResults';

    const RESPONSE_ERROR     = 'error';
    const RESPONSE_ERROR_MSG = 'message';

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_http_HttpClientInterface
     */
    private $_httpClient;

    /**
     * @var tubepress_api_array_ArrayReaderInterface
     */
    private $_arrayReader;

    /**
     * @var array
     */
    private $_memoryCache;

    public function __construct(tubepress_api_log_LoggerInterface        $logger,
                                tubepress_api_options_ContextInterface   $context,
                                tubepress_api_http_HttpClientInterface   $httpClient,
                                tubepress_api_array_ArrayReaderInterface $arrayReader)
    {
        $this->_logger      = $logger;
        $this->_context     = $context;
        $this->_httpClient  = $httpClient;
        $this->_arrayReader = $arrayReader;
        $this->_memoryCache = array();
    }

    /**
     * @param tubepress_api_url_UrlInterface $url
     * @param array                          $requestOpts
     *
     * @return array
     */
    public function getDecodedApiResponse(tubepress_api_url_UrlInterface $url, $requestOpts = array())
    {
        $url->getQuery()->set(self::QUERY_APIKEY, $this->_context->get(tubepress_youtube3_api_Constants::OPTION_API_KEY));

        $httpRequest = $this->_httpClient->createRequest('GET', $url, $requestOpts);
        $finalConfig = array_merge($httpRequest->getConfig(), array('tubepress-remote-api-call' => true));

        $httpRequest->setConfig($finalConfig);

        $urlAsString = $url->toString();

        if (!isset($this->_memoryCache[$urlAsString])) {

            $httpResponse = $this->_httpClient->send($httpRequest);
            $rawFeed      = $httpResponse->getBody()->toString();
            $decoded      = json_decode($rawFeed, true);

            if ($decoded === null) {

                throw new RuntimeException('Unable to decode JSON from YouTube');
            }

            $this->checkForApiResponseError($decoded);

            $this->_memoryCache[$urlAsString] = $decoded;

        } else {

            if ($this->_logger->isEnabled()) {

                $this->_logger->debug(sprintf('Response for <a href="%s">URL</a> found in the in-memory cache.', $urlAsString));
            }
        }

        return $this->_memoryCache[$urlAsString];
    }

    public function checkForApiResponseError(array $json)
    {
        $errorMessage = $this->_arrayReader->getAsString($json, sprintf('%s.%s', self::RESPONSE_ERROR, self::RESPONSE_ERROR_MSG));

        if ($errorMessage) {

            throw new RuntimeException(sprintf('YouTube responded with an error: %s',
                $errorMessage
            ));
        }
    }
}
