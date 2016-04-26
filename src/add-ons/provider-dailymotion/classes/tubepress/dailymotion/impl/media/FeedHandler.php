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
     * @var tubepress_dailymotion_impl_dmapi_ApiUtility
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

    /**
     * @var tubepress_api_url_UrlInterface
     */
    private $_urlFetched;

    /**
     * @var bool
     */
    private $_shouldLog;

    private $_invokedAtLeastOnce;

    public function __construct(tubepress_api_log_LoggerInterface           $logger,
        tubepress_api_options_ContextInterface      $context,
        tubepress_api_url_UrlFactoryInterface       $urlFactory,
        tubepress_api_array_ArrayReaderInterface    $arrayReader,
        tubepress_dailymotion_impl_dmapi_ApiUtility $apiUtility)
    {
        $this->_logger      = $logger;
        $this->_context     = $context;
        $this->_urlFactory  = $urlFactory;
        $this->_arrayReader = $arrayReader;
        $this->_apiUtility  = $apiUtility;
        $this->_shouldLog   = $logger->isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dailymotion';
    }

    /**
     * {@inheritdoc}
     */
    public function getNewItemEventArguments(tubepress_api_media_MediaItem $mediaItemId, $index)
    {
        return array(

            'feedAsArray'    => $this->_feedAsArray,
            'zeroBasedIndex' => $index,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlForPage($currentPage)
    {
        $mode  = $this->_context->get(tubepress_api_options_Names::GALLERY_SOURCE);
        $url   = $this->_urlFactory->fromString(self::$_URL_BASE);
        $query = $url->getQuery();

        $this->_startGalleryUrl($mode, $url);
        $this->_addPagination($currentPage, $query);
        $this->_addSort($mode, $query);
        $this->_addFieldsParam($query);

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlForItem($id)
    {
        $url = $this->_urlFactory->fromString(self::$_URL_BASE);
        $id  = str_replace('dailymotion_', '', $id);

        $url->addPath('video')->addPath($id);
        $this->_addFieldsParam($url->getQuery());

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalResultCount()
    {
        $total = $this->_calculateTotalResultCount();

        return $total - $this->_skippedVideoCount;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getCurrentResultCount()
    {
        $items = $this->_arrayReader->getAsArray($this->_feedAsArray, 'list', array());

        return count($items);
    }

    /**
     * {@inheritdoc}
     */
    public function onAnalysisStart($feed, tubepress_api_url_UrlInterface $url)
    {
        $this->_skippedVideoCount = 0;
        $this->_feedAsArray       = json_decode($feed, true);
        $this->_urlFetched        = $url->getClone();

        if ($this->_feedAsArray === null) {

            throw new RuntimeException('Unable to decode JSON from Dailymotion');
        }

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Decoded feed from Dailymotion is visible in the HTML source of this page.<span style="display:none">%s</span>',

                htmlspecialchars(print_r($this->_feedAsArray, true))
            ));
        }

        $this->_apiUtility->checkForApiResponseError($this->_feedAsArray);

        if (isset($this->_feedAsArray['id'])) {

            $item               = $this->_feedAsArray;
            $this->_feedAsArray = array(

                'list' => array($item),
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onAnalysisComplete()
    {
        unset($this->_feedAsArray);
        unset($this->_metadataAsArray);
        unset($this->_urlFetched);
    }

    private function _addSort($mode, tubepress_api_url_QueryInterface $query)
    {
        if ($mode === tubepress_dailymotion_api_Constants::GALLERY_SOURCE_RELATED) {

            //"sort" param is not valid for related videos
            return;
        }

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

        } elseif (isset($sortMap[$requestedSort])) {

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

                $url->addPath('videos')->getQuery()->set('tags', $tagValue);

                return;

            case tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SUBSCRIPTIONS:

                $subValue = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_SUBSCRIPTIONS_VALUE);

                $url->addPath('user')->addPath($subValue)->addPath('subscriptions');

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

    /**
     * {@inheritdoc}
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

        return "dailymotion_$id";
    }

    public function __invoke()
    {
        $this->_invokedAtLeastOnce = true;
    }

    private function _calculateTotalResultCount()
    {
        $total = $this->_arrayReader->getAsInteger(
            $this->_feedAsArray,
            'total',
            -1
        );

        /*
         * Whew!
         */
        if ($total !== -1) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Total result count was reported by Dailymotion to be <code>%s</code>', $total));
            }

            return $total;
        }

        /*
         * Dailymotion doesn't return the total result count, which sucks because that means we'll need to
         * "manually" calculate it in order to show pagination.
         *
         * The maximum page that can be fetched is 100, and the max results per page is 100. This means that there
         * can be no more than 10K (100 * 100) total videos in any single source from Dailymotion. This is good because
         * it actually gives us a change to calculate the total result count with about 6 network requests with a binary
         * search.
         */

        if ($this->_shouldLog) {

            $this->_logDebug('Total result count was not reported by Dailymotion. We will perform a binary search');
        }

        return $this->_findMaxResults(1, 100);
    }

    private function _findMaxResults($minimumPage, $maximumPage)
    {
        $currentPage = floor(($maximumPage + $minimumPage) / 2);

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf(
                'Starting new iteration. Minimum page is <code>%d</code>, maximum page is <code>%d</code>, current page is <code>%d</code>',
                $minimumPage, $maximumPage, $currentPage
            ));
        }

        $url   = $this->_urlFetched->getClone();
        $query = $url->getQuery();

        $query->set('fields', 'id');
        $query->set('limit', 100);
        $query->set('page', $currentPage);

        $result           = $this->_apiUtility->getDecodedApiResponse($url);
        $currentPageCount = count($this->_arrayReader->getAsArray($result, 'list'));
        $hasMore          = $this->_arrayReader->getAsBoolean($result, 'has_more');

        unset($url);
        unset($query);
        unset($result);

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf(
                'Page <code>%d</code> contains <code>%d</code> videos and <code>has_more</code> is <code>%s</code>',
                $currentPage, $currentPageCount, $hasMore ? 'true' : 'false'
            ));
        }

        /*
         * There are 4 possible cases here:
         *
         * 1. We have over 10,000 results.
         * 2. Middle page is the last page in the result set.
         * 3. Middle page overshoots the last page.
         * 4. Middle page undershoots the last page.
         */

        if ($hasMore) {

            //scenario 1
            if (intval($currentPage) === 100) {

                if ($this->_shouldLog) {

                    $this->_logDebug('There are over 10K videos in this result set.');
                }

                // we've hit the max
                return 10000;
            }

            if ($this->_shouldLog) {

                $this->_logDebug('We have undershot the last page in the result set.');
            }

            //scenario 4
            return $this->_findMaxResults($currentPage + 1, $maximumPage);
        }

        // scenario 2
        if ($currentPageCount > 0) {

            if ($this->_shouldLog) {

                $this->_logDebug('Looks like this was the last page in the result set.');
            }

            return (($currentPage - 1) * 100) + $currentPageCount;
        }

        if ($this->_shouldLog) {

            $this->_logDebug('We have overshot the last page in the result set.');
        }

        // scenario 3
        return $this->_findMaxResults($minimumPage, $currentPage - 1);
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Dailymotion Feed Handler) %s', $msg));
    }
}
