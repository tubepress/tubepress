<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_dailymotion_impl_media_FeedHandler implements tubepress_spi_media_HttpFeedHandlerInterface
{
    private static $_URL_BASE = 'https://api.dailymotion.com';

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_array_ArrayReaderInterface
     */
    private $_arrayReader;

    /**
     * @var tubepress_dailymotion_impl_ApiUtility
     */
    private $_apiUtility;

    /**
     * @var array
     */
    private $_feedAsArray;

    /**
     * @var int
     */
    private $_skippedVideoCount;

    private $_invokedAtLeastOnce;

    public function __construct(tubepress_api_log_LoggerInterface        $logger,
                                tubepress_api_options_ContextInterface   $context,
                                tubepress_api_url_UrlFactoryInterface    $urlFactory,
                                tubepress_api_array_ArrayReaderInterface $arrayReader,
                                tubepress_dailymotion_impl_ApiUtility    $apiUtility)
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
        return 'dailymotion';
    }

    /**
     * Gather data that might be needed from the feed to build attributes for this media item.
     *
     * @param tubepress_api_media_MediaItem $mediaItemId The media item.
     * @param int                               $index       The zero-based index.
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getNewItemEventArguments(tubepress_api_media_MediaItem $mediaItemId, $index)
    {
        return array(

            'feedAsArray'    => $this->_feedAsArray,
            'zeroBasedIndex' => $index
        );
    }

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return tubepress_api_url_UrlInterface The request URL for this gallery.
     *
     * @api
     * @since 4.0.0
     */
    public function buildUrlForPage($currentPage)
    {
        $mode  = $this->_context->get(tubepress_api_options_Names::GALLERY_SOURCE);
        $url   = $this->_urlFactory->fromString(self::$_URL_BASE);
        $query = $url->getQuery();

        $this->_startGalleryUrl($mode, $url);
        $this->_addPagination($currentPage, $query);
        $this->_addSort($mode, $query);
        $this->_addGlobalParams($query);
        $this->_addFieldsParam($query);
        $this->_addFilters($query);

        return $url;
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return tubepress_api_url_UrlInterface The URL for the single video given.
     *
     * @api
     * @since 4.0.0
     */
    public function buildUrlForItem($id)
    {
        $url = $this->_urlFactory->fromString(self::$_URL_BASE);

        $url->addPath('video')->addPath($id);
        $this->_addGlobalParams($url->getQuery());
        $this->_addFieldsParam($url->getQuery());

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
        $total = $this->_arrayReader->getAsInteger(
            $this->_feedAsArray,
            'total',
            0
        );

        return $total - $this->_skippedVideoCount;
    }

    /**
     * Determine why the given item cannot be used.
     *
     * @param integer $index The index into the feed.
     *
     * @return string The reason why we can't work with this media item, or null if we can.
     *
     * @api
     * @since 4.0.0
     */
    public function getReasonUnableToUseItemAtIndex($index)
    {
        $items = $this->_arrayReader->getAsArray($this->_feedAsArray, 'list', array());

        if (!isset($items[$index])) {

            return null;
        }

        $item        = $items[$index];
        $accessError = $this->_arrayReader->getAsString($item, 'access_error.title');

        if ($accessError) {

            return $accessError;
        }

        $embeddingAllowed = $this->_arrayReader->getAsBoolean($item, 'allow_embed', true);

        if (!$embeddingAllowed) {

            return 'This video cannot be embedded outside of Dailymotion';
        }

        $published = $this->_arrayReader->getAsBoolean($item, 'published');

        if (!$published) {

            return 'This video has not yet been published';
        }

        $private = $this->_arrayReader->getAsBoolean($item, 'private');

        if ($private) {

            $privateId = $this->_arrayReader->getAsString($item, 'private_id');

            if (!$privateId) {

                return 'This video is private and TubePress does not have access to it';
            }
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
        $items = $this->_arrayReader->getAsArray($this->_feedAsArray, 'list', array());

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

            throw new RuntimeException('Unable to decode JSON from Dailymotion');
        }

        if ($loggerEnabled) {

            $this->_logDebug(sprintf('Decoded feed from Dailymotion is visible in the HTML source of this page.<span style="display:none">%s</span>',

                htmlspecialchars(print_r($this->_feedAsArray, true))
            ));
        }

        $this->_apiUtility->checkForApiResponseError($this->_feedAsArray);
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

    private function _addSort($mode, tubepress_api_url_QueryInterface $query)
    {
        $requestedSort = $this->_context->get(tubepress_api_options_Names::FEED_ORDER_BY);
        $finalSort     = $this->_calculateDefaultSortOrder($mode);
        $sortMap       = array(

            tubepress_dailymotion_api_Constants::ORDER_BY_RANKING    => 'ranking',
            tubepress_dailymotion_api_Constants::ORDER_BY_NEWEST     => 'recent',
            tubepress_dailymotion_api_Constants::ORDER_BY_OLDEST     => 'old',
            tubepress_dailymotion_api_Constants::ORDER_BY_RANDOM     => 'random',
            tubepress_dailymotion_api_Constants::ORDER_BY_TRENDING   => 'trending',
            tubepress_dailymotion_api_Constants::ORDER_BY_DEFAULT    => $finalSort,
            tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT => 'visited',
        );

        if ($requestedSort === tubepress_dailymotion_api_Constants::ORDER_BY_RELEVANCE &&
            $mode === tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH) {

            $finalSort = 'relevance';

        } else if (isset($sortMap[$requestedSort])) {

            $finalSort = $sortMap[$requestedSort];
        }

        $query->set('sort', $finalSort);
    }

    private function _calculateDefaultSortOrder($currentMode)
    {
        if ($currentMode === tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH) {

            return 'relevance';
        }

        return 'recent';
    }

    private function _startGalleryUrl($mode, tubepress_api_url_UrlInterface $url)
    {
        switch ($mode) {

            case tubepress_dailymotion_api_Constants::GALLERY_SOURCE_USER:

                $userValue = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_USER_VALUE);

                $url->addPath('user')->addPath($userValue)->addPath('videos');

                return;

            case tubepress_dailymotion_api_Constants::GALLERY_SOURCE_PLAYLIST:

                $playlistValue = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_PLAYLIST_VALUE);

                $url->addPath('playlist')->addPath($playlistValue)->addPath('videos');

                return;

            case tubepress_dailymotion_api_Constants::GALLERY_SOURCE_LIST:

                $ids = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_LIST_VALUE);
                $ids = $this->_implodeCsv($ids);

                $url->addPath('videos')->getQuery()->set('ids', $ids);

                return;

            case tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FAVORITES:

                $favoritesValue = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FAVORITES_VALUE);

                $url->addPath('user')->addPath($favoritesValue)->addPath('favorites');

                return;

            case tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FEATURED:

                $featureValue = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEATURED_VALUE);

                $url->addPath('user')->addPath($featureValue)->addPath('features');

                return;

            case tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH:

                $searchValue = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_SEARCH_VALUE);

                $url->addPath('videos')->getQuery()->set('search', $searchValue);

                return;

            case tubepress_dailymotion_api_Constants::GALLERY_SOURCE_RELATED:

                $relatedValue = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_RELATED_VALUE);

                $url->addPath('video')->addPath($relatedValue)->addPath('related');

                return;

            case tubepress_dailymotion_api_Constants::GALLERY_SOURCE_TAG:

                $tagValue = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_TAG_VALUE);
                $tagValue = $this->_implodeCsv($tagValue);

                $url->addPath('videos')->getQuery()->set('strongtags', $tagValue);

                return;
        }

        throw new InvalidArgumentException(sprintf('Unknown Dailymotion gallery source: %s', $mode));
    }

    private function _addPagination($currentPage, tubepress_api_url_QueryInterface $query)
    {
        if (isset($this->_invokedAtLeastOnce)) {

            $perPage = $this->_context->get(tubepress_api_options_Names::FEED_RESULTS_PER_PAGE);

        } else {

            $perPage = min($this->_context->get(tubepress_api_options_Names::FEED_RESULTS_PER_PAGE), ceil(2.04));
        }

        $query->set('page', $currentPage)
              ->set('limit', $perPage);
    }

    private function _implodeCsv($incoming)
    {
        $incoming = preg_split('/\s*,\s*/', $incoming);
        $incoming = implode(',', $incoming);

        return $incoming;
    }

    private function _addGlobalParams(tubepress_api_url_QueryInterface $query)
    {
        $familyFilter = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER);
        $ssl          = $this->_context->get(tubepress_api_options_Names::HTML_HTTPS);
        $locale       = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE);
        $thumbRatio   = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO);

        $query->set('family_filter',   $familyFilter ? 'on' : 'off')
              ->set('ssl_assets',      $ssl ? 'on' : 'off')
              ->set('localization',    $locale)
              ->set('thumbnail_ratio', $thumbRatio);
    }

    private function _addFieldsParam(tubepress_api_url_QueryInterface $query)
    {
        $fields = array(
            'id',                // we always need the video's ID
            'access_error',      // so that we can determine if the video is available
            'allow_embed',       // so that we can determine if the video is available
            'private',           // so that we can determine if the video is available
            'private_id',        // so that we can determine if the video is available
            'published',         // so that we can determine if the video is available

            'channel.name',      // channel/category name
            'created_time',      // time video uploaded
            'description',       // description
            'duration',          // duration in seconds
            'owner.id',          // uploader ID
            'owner.screenname',  // uploader screenname
            'owner.url',         // uploader URL
            'tags',              // keywords
            'thumbnail_60_url',  // thumb
            'thumbnail_120_url', // thumb
            'thumbnail_180_url', // thumb
            'thumbnail_240_url', // thumb
            'thumbnail_360_url', // thumb
            'thumbnail_480_url', // thumb
            'thumbnail_720_url', // thumb
            'thumbnail_url',     // thumb
            'title',             // title
            'url',               // URL
            'views_total',       // total views
        );

        $implodedFields = implode(',', $fields);

        $query->set('fields', $implodedFields);
    }

    private function _addFilters(tubepress_api_url_QueryInterface $query)
    {
        $country           = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY);
        $detectedLanguage  = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED);
        $declaredLanguages = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED);
        $blacklist         = $this->_context->get(tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST);
        $featuredOnly      = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY);
        $onlyGenre         = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE);
        $notGenre          = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE);
        $hdOnly            = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY);
        $liveFilter        = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER);
        $longerThan        = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN);
        $shorterThan       = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN);
        $premiumFilter     = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER);
        $ownersFilter      = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER);
        $search            = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH);
        $strongTags        = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG);
        $tags              = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS);
        $partnerFilter     = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER);
        $flags             = array();

        if ($country) {

            $query->set('country', $country);
        }

        if ($detectedLanguage) {

            $query->set('detected_language', $detectedLanguage);
        }

        if ($declaredLanguages) {

            $query->set('languages', $this->_implodeCsv($declaredLanguages));
        }

        if ($blacklist) {

            $query->set('exclude_ids', $this->_implodeCsv($blacklist));
        }

        if ($featuredOnly) {

            $flags[] = 'featured';
        }

        if ($onlyGenre) {

            $query->set('genre', $onlyGenre);
        }

        if ($notGenre) {

            $query->set('nogenre', $notGenre);
        }

        if ($hdOnly) {

            $flags[] = 'hd';
        }

        switch ($liveFilter) {

            case tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_OFF:

                $flags[] = 'live_offair';
                break;

            case tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_ON:

                $flags[] = 'live_onair';
                break;

            case tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_ONLY:

                $flags[] = 'live';
                break;

            case tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_UPCOMING:

                $flags[] = 'live_upcoming';
                break;

            case tubepress_dailymotion_api_Constants::FILTER_LIVE_NON_LIVE:

                $flags[] = 'no_live';
                break;
        }

        if ($longerThan > 0) {

            $query->set('longer_than', $longerThan);
        }

        if ($shorterThan) {

            $query->set('shorter_than', $shorterThan);
        }

        switch ($premiumFilter) {

            case tubepress_dailymotion_api_Constants::FILTER_PREMIUM_NON_PREMIUM_ONLY:

                $flags[] = 'no_premium';
                break;

            case tubepress_dailymotion_api_Constants::FILTER_PREMIUM_PREMIUM_ONLY:

                $flags[] = 'premium';
                break;
        }

        if ($ownersFilter) {

            $query->set('owners', $this->_implodeCsv($ownersFilter));
        }

        if ($search && $this->_context->get(tubepress_api_options_Names::GALLERY_SOURCE) !== tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH) {

            $query->set('search', $search);
        }

        if ($strongTags && $this->_context->get(tubepress_api_options_Names::GALLERY_SOURCE) !== tubepress_dailymotion_api_Constants::GALLERY_SOURCE_TAG) {

            $query->set('strongtags', $this->_implodeCsv($strongTags));
        }

        if ($tags) {

            $query->set('tags', $this->_implodeCsv($tags));
        }

        switch ($partnerFilter) {

            case tubepress_dailymotion_api_Constants::FILTER_PARTNER_NON_PARTNER_ONLY:

                $flags[] = 'ugc';
                break;

            case tubepress_dailymotion_api_Constants::FILTER_PARTNER_PARTNER_ONLY:

                $flags[] = 'partner';
                break;
        }

        if (count($flags) > 0) {

            $query->set('flags', implode(',', $flags));
        }
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
        $items = $this->_arrayReader->getAsArray($this->_feedAsArray, 'list', array());
        $id    = '';

        if (isset($items[$index])) {

            $item = $items[$index];
            $id   = $this->_arrayReader->getAsString($item, 'id');
        }

        if ($id === '') {

            return null;
        }

        return $id;
    }

    public function __invoke()
    {
        $this->_invokedAtLeastOnce = true;
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Dailymotion Feed Handler) %s', $msg));
    }
}