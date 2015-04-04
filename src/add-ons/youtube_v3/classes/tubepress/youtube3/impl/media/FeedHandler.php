<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_youtube3_impl_media_FeedHandler implements tubepress_app_api_media_HttpFeedHandlerInterface
{
    private static $_URL_PARAM_FORMAT      = 'format';
    private static $_URL_PARAM_KEY         = 'key';
    private static $_URL_PARAM_SAFESEARCH  = 'safeSearch';

    // New for v3
    private static $_URL_PARAM_MAX_RESULTS = 'maxResults';
    private static $_URL_PARAM_PAGE_TOKEN  = 'pageToken';
    private static $_URL_PARAM_VERSION     = 'v';
    private static $_URL_PARAM_VERSION_NUM = 3;
    private static $_URL_PARAM_ORDER       = 'order';

    private static $_YOUTUBE_API_URL       = 'https://www.googleapis.com/youtube/v3/';
    private static $_YOUTUBE_REQUEST_URL   = '';

    /**
     * @var array().
     */
    private $_parts = array('id','snippet'); //  

    /**
     * @var array() $_feedResults.
     */
    private $_feedResults;

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
     * @var bool
     */
    private $_videoNotFound = false;

    /**
     * @var tubepress_lib_api_http_HttpClientInterface
     */
    private $_httpClient;

    public function __construct(tubepress_platform_api_log_LoggerInterface     $logger,
                                tubepress_app_api_options_ContextInterface     $context,
                                tubepress_platform_api_url_UrlFactoryInterface $urlFactory,
                                tubepress_lib_api_http_HttpClientInterface     $httpClient)
    {
        $this->_logger     = $logger;
        $this->_context    = $context;
        $this->_urlFactory = $urlFactory;
        $this->_httpClient = $httpClient;
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
            'feed'           => $this->_feedResults,
            'zeroBasedIndex' => $index
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
        $url = self::$_YOUTUBE_API_URL;  

        switch ($this->_context->get(tubepress_app_api_options_Names::GALLERY_SOURCE)) 
        {
            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR:
                
                $this->_parts[] = 'contentDetails';
                $this->_parts[] = 'statistics';

                $params = 'videos?chart=mostPopular';

                if ($this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE) == 'today')
                {
                    $params .= '&publishedAfter='. gmDate("Y-m-d\T00:00:00\Z"); 
                }    

                break;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST:

                $this->_parts[] = 'contentDetails';

                $params = sprintf('playlistItems?playlistId=%s', $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE));

                break;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED:

                $params = sprintf('search?relatedToVideoId=%s&type=video', $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_RELATED_VALUE));

                break;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES:

                $username    = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE);               
                $first_url   = self::$_YOUTUBE_API_URL . 'channels?part=contentDetails&forUsername='.$username;

                $favoritesFeed = $this->_makeDirectCall($first_url);

                if (!isset($favoritesFeed['items'][0]['contentDetails']['relatedPlaylists']['favorites']))
                {
                    throw new LogicException('Channel has no YouTube favorites');
                }    

                $favoritesPlaylistId = $favoritesFeed['items'][0]['contentDetails']['relatedPlaylists']['favorites'];

                $this->_parts[] = 'contentDetails';
                $this->_parts[] = 'status'; 

                $params = sprintf('playlistItems?playlistId=%s', $favoritesPlaylistId);

                break;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_USER:

                $username  = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_USER_VALUE);
                $first_url = self::$_YOUTUBE_API_URL . 'channels?part=contentDetails&forUsername='.$username;
                
                $feed = $this->_makeDirectCall($first_url);

                if (!isset($feed['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {

                    throw new LogicException('Channel has no YouTube uploads');
                }

                $favoritesPlaylistId = $feed['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

                $this->_parts[] = 'contentDetails';
                $this->_parts[] = 'status'; 
                
                $params = sprintf('playlistItems?playlistId=%s', $favoritesPlaylistId);

                 break;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH:

                // https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q=skateboarding+dog&type=video&videoDefinition=high
                // https://www.googleapis.com/youtube/v3/videos?q=iphone+ios+apple&type=video&part=id, snippet

                $tags = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_YOUTUBE_TAG_VALUE);
                $tags = self::_replaceQuotes($tags);
                $tags = urlencode($tags);
                $params = "search?q=$tags" . '&type=video'; 

                $filter = $this->_context->get(tubepress_app_api_options_Names::SEARCH_ONLY_USER);
                if ($filter != '') {

                    $first_url   = self::$_YOUTUBE_API_URL . 'channels?part=contentDetails&forUsername='.$filter;
                    
                    $feed = $this->_makeDirectCall($first_url);

                    if (!isset($feed['items'][0]['id']))
                    {
                        throw new LogicException('No Channel Id found');
                    }  

                    $channelId = $feed['items'][0]['id'];

                    $params .= sprintf("&channelId=%s", $channelId);
                }

                break;

            default:

                throw new LogicException('Invalid source supplied to YouTube');
        }

        $params .= sprintf('&part=%s', implode(',', array_unique($this->_parts)));

        // stored for later use in the loops - it seems that nextPageToken is not enough info
        self::$_YOUTUBE_REQUEST_URL = $url.$params;

        $requestUrl = $this->_urlFactory->fromString($url.$params);
        
        $this->_urlPostProcessingCommon($requestUrl);

        $this->_urlPostProcessingGallery($requestUrl, $currentPage);

        return $requestUrl;
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
        $this->_parts[] = 'contentDetails';
        $this->_parts[] = 'statistics';

        $url = self::$_YOUTUBE_API_URL; 
        $params = sprintf('videos?id=%s&part=%s', $id, implode(',', array_unique($this->_parts))); 

        $requestURL = $this->_urlFactory->fromString($url.$params);
        $this->_urlPostProcessingCommon($requestURL);

        return $requestURL;
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
        if (!isset($this->_feedResults['pageInfo'])) {

            return 0;
        }

        $totalResults = $this->_feedResults['pageInfo']['totalResults'];

        self::_makeSureNumeric($totalResults);

        return $totalResults;
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
        $item = $this->_feedResults['items'][$index];

        if (isset($item['snippet']['title'])
            && $item['snippet']['title'] === 'Deleted video'
            && isset($item['snippet']['description'])
            && $item['snippet']['description'] === 'This video is unavailable.') {

            return 'Video has been deleted';
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
        if ($this->_videoNotFound) {

            return 0;
        }

        return count($this->_feedResults['items']);
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

        // poor man's - use Event or Collector
        if(isset($_REQUEST['tubepress_page']) && ($_REQUEST['tubepress_page'] > 1))
        {
            $currentPage = $_REQUEST['tubepress_page'];
            $decodedFeed = json_decode($feed, true); 
            if (isset($decodedFeed['nextPageToken']))
            {
                $feed = $this->_get_page_token($decodedFeed['nextPageToken'], $currentPage);            
            }    
        } 

        $this->_createFeedArray($feed);

        $this->_feedProcessing();

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
        unset($this->_feedResults);        
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
        // Thank you, YouTube API v3, for returning a different item array based on whether it is a 
        // single or multiple requested items. Just thank you.

        $id  = isset($this->_feedResults['items'][$index]['id']['videoId'])? 
            $this->_feedResults['items'][$index]['id']['videoId']:
            $this->_feedResults['items'][$index]['id'];

        return $id;

    }

    private static function _replaceQuotes($text)
    {
        return str_replace(array('&#8216', '&#8217', '&#8242;', '&#34', '&#8220;', '&#8221;', '&#8243;'), '"', $text);
    }

    private function _urlPostProcessingCommon(tubepress_platform_api_url_UrlInterface $url)
    {
        $query = $url->getQuery();
        $query->set(self::$_URL_PARAM_VERSION, self::$_URL_PARAM_VERSION_NUM);
        $query->set(self::$_URL_PARAM_KEY, $this->_context->get(tubepress_youtube3_api_Constants::OPTION_DEV_KEY));
    }

    private function _urlPostProcessingGallery(tubepress_platform_api_url_UrlInterface $url, $currentPage)
    {
        $perPage = $this->_context->get(tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE);
 
        $query = $url->getQuery();
        $query->set(self::$_URL_PARAM_MAX_RESULTS, $perPage);

        $this->_urlProcessingOrderBy($url);

        $query->set(self::$_URL_PARAM_SAFESEARCH, $this->_context->get(tubepress_youtube3_api_Constants::OPTION_FILTER));

        if ($this->_context->get(tubepress_youtube3_api_Constants::OPTION_EMBEDDABLE_ONLY)) {

            $query->set(self::$_URL_PARAM_FORMAT, '5');
        }
    }

    private function _get_page_token($nextPageToken, $currentPage)
    {
        // $nextPageToken is for $currentPage + 1
        $perPage     = $this->_context->get(tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE);
        
        // start by getting page 2
        foreach (range(2, $currentPage) as $key => $value) {
        
            $url   =    self::$_YOUTUBE_REQUEST_URL . '&maxResults='.$perPage.'&pageToken='.$nextPageToken;
            
            $feed = $this->_makeDirectCall($url);

            if (isset($feed['nextPageToken']))
            {
                $nextPageToken = $feed['nextPageToken'];
            }   
        }

        return $feed;
     
    }

    private function _urlProcessingOrderBy(tubepress_platform_api_url_UrlInterface $url)
    {
        $requestedSortOrder   = $this->_context->get(tubepress_app_api_options_Names::FEED_ORDER_BY);
        $currentGallerySource = $this->_context->get(tubepress_app_api_options_Names::GALLERY_SOURCE);
        $filter               = $this->_context->get(tubepress_app_api_options_Names::SEARCH_ONLY_USER);     

        $query                = $url->getQuery();

        if ($requestedSortOrder === tubepress_app_api_options_AcceptableValues::ORDER_BY_DEFAULT) {

            $query->set(self::$_URL_PARAM_ORDER, $this->_calculateDefaultSearchOrder($currentGallerySource));
            return;
        }

        if ($requestedSortOrder === tubepress_youtube3_api_Constants::ORDER_BY_NEWEST) {

            $query->set(self::$_URL_PARAM_ORDER, 'date');
            return;
        }

        if ($requestedSortOrder == tubepress_youtube3_api_Constants::ORDER_BY_VIEW_COUNT) {
            //not sure if this will cover all cases, but all I've seen at the moment
            if ($filter != '') {
                $query->set(self::$_URL_PARAM_ORDER, tubepress_youtube3_api_Constants::ORDER_BY_RELEVANCE);

                return;
            }  

            $query->set(self::$_URL_PARAM_ORDER, 'viewCount');

            return;
        }

        if (in_array($requestedSortOrder, array(
                tubepress_youtube3_api_Constants::ORDER_BY_RELEVANCE, 
                tubepress_youtube3_api_Constants::ORDER_BY_RATING,
                tubepress_youtube3_api_Constants::ORDER_BY_TITLE))) 
        {

            $query->set(self::$_URL_PARAM_ORDER, $requestedSortOrder);

            return;
        }

        //default
        // TODO: best if we can figure out what legal values are for sorting when filtering on author
        if ($filter != '') {
            $query->set(self::$_URL_PARAM_ORDER, tubepress_youtube3_api_Constants::ORDER_BY_RELEVANCE);

            return;
        } 


        $query->set(self::$_URL_PARAM_ORDER, 'viewCount');

        return;


    }

    private function _calculateDefaultSearchOrder($currentGallerySource)
    {
        switch ($currentGallerySource) {

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR:

                return tubepress_youtube3_api_Constants::ORDER_BY_VIEW_COUNT;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH:
            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED:

                return tubepress_youtube3_api_Constants::ORDER_BY_RELEVANCE;

            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_USER:
            case tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES:

                return 'date';
        }
    }

    private static function _makeSureNumeric($result)
    {
        if (is_numeric($result) === false) {

            throw new RuntimeException("YouTube returned a non-numeric result count: $result");
        }
    }

    function _createFeedArray($feed)
    {
        if (!is_array($feed)){ 
            $feed = json_decode($feed, true);
        }

        $this->_feedResults = $feed;
    }

    private function _feedProcessing()
    {
        if (!isset($this->_feedResults['items'])) {

            return;
        }

        // hopefully this can be worked around in the future. there is data missing from certain types
        foreach ($this->_feedResults['items'] as $index => $item) {

            // if statistics are missing, that is the lowest common denominator so we'll get a separate pull for the video
            if (!isset($this->_feedResults['statistics'])) {

                // go get the video 
                $videoId = $this->_getVideoId($item);

                $this->_parts[] = 'contentDetails';
                $this->_parts[] = 'statistics';

                $url = self::$_YOUTUBE_API_URL;
                $params = 'videos?id='.$videoId.  '&part='. implode(',',$this->_parts);
                $videoSingleFeed = $this->_makeDirectCall($url . $params);

                //or just array merge, but who knows what madness that might unleash...
                if (!empty($videoSingleFeed['items'])) {

                    $this->_feedResults['items'][$index]['snippet']['categoryId'] =  $videoSingleFeed['items'][0]['snippet']['categoryId'];
                    $this->_feedResults['items'][$index]['contentDetails'] = $videoSingleFeed['items'][0]['contentDetails'];
                    $this->_feedResults['items'][$index]['statistics']     = $videoSingleFeed['items'][0]['statistics'];                    
                }    
            }   

            // Category
            if (isset($this->_feedResults['items'][$index]['snippet']['categoryId'])) {

                // go get the channel name
                $params = 'videoCategories?part=snippet&id='. $this->_feedResults['items'][$index]['snippet']['categoryId'];
                
                $categorySingleFeed = $this->_makeDirectCall($url . $params);

                $this->_feedResults['items'][$index]['snippet']['categoryName'] = $categorySingleFeed['items'][0]['snippet']['title'];
            }
        }
    }

    // we need to really check through & get a more foolproof way -maybe search videoId anywhere...?
    function _getVideoId($item)
    {
        if (isset($item['snippet']['resourceId']['videoId']))
        {
            return $item['snippet']['resourceId']['videoId'];
        }    
        if (isset($item['id']['videoId']))
        {
            return $item['id']['videoId'];
        }           
        if (isset($item['id']))
        {
            return $item['id'];
        }   
        return false;
    }

    private function _makeDirectCall($url, $requestOpts = array())
    {
        $apikey      = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_DEV_KEY);
        $url        .= '&key='.$apikey;
        $httpRequest = $this->_httpClient->createRequest('GET', $url, $requestOpts);
         
        $httpRequest->setConfig(array_merge($httpRequest->getConfig(), array('tubepress-remote-api-call' => true)));

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug('Making sub-request for <code>' . $url . '</code>');
        }

        $httpResponse = $this->_httpClient->send($httpRequest);
        $rawFeed      = $httpResponse->getBody()->toString();

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Raw result for <a href="%s">URL</a> is in the HTML source for this page. <span style="display:none">%s</span>',
                $url, htmlspecialchars($rawFeed)));
        }

        return json_decode($rawFeed, true);
    }
}