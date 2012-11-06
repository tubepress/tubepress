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
 * Abstract feed-fetcher-based video provider.
 */
abstract class tubepress_impl_provider_AbstractFetchingAndBuildingPluggableVideoProviderService extends tubepress_impl_provider_AbstractDispatchingPluggableVideoProviderService
{
    /**
     * @var ehough_epilog_api_ILogger
     */
    private $_logger;

    /**
     * @var tubepress_spi_context_ExecutionContext
     */
    private $_executionContext;

    /**
     * Fetch a video gallery page.
     *
     * @param int $currentPage The requested page number of the gallery.
     *
     * @return tubepress_api_video_VideoGalleryPage The video gallery page for this page. May be empty, never null.
     */
    protected final function fetchVideoGalleryPageNoDispatch($currentPage)
    {
        $this->_cacheLogger();
        $this->_cacheExecutionContext();

        $result = new tubepress_api_video_VideoGalleryPage();

        $feedFetcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getFeedFetcher();

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Current page number is %d', $currentPage));
        }

        /* build the request URL */
        $url = $this->buildGalleryUrl($currentPage);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('URL to fetch is <code>%s</code>', $url));
        }

        /* make the request */
        $useCache = $this->_executionContext->get(tubepress_api_const_options_names_Cache::CACHE_ENABLED);
        $rawFeed  = $feedFetcher->fetch($url, $useCache);

        /* give the command a chance to do some initial processing */
        $this->_preFactoryExecution($rawFeed);

        /* get the count */
        $totalCount = $this->getTotalResultCount();

        if ($totalCount == 0) {

            $result->setTotalResultCount(0);
            $result->setVideos(array());

            return $result;
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Reported total result count is %d video(s)', $totalCount));
        }

        /* convert the XML to objects */
        $videos  = $this->_feedToVideoArray($rawFeed);

        if (count($videos) == 0) {

            $result->setTotalResultCount(0);
            $result->setVideos(array());

            return $result;
        }

        $result->setTotalResultCount($totalCount);
        $result->setVideos($videos);

        return $result;
    }

    /**
     * Fetch a single video.
     *
     * @param string $videoId The video ID to fetch.
     *
     * @return tubepress_api_video_Video The video, or null if unable to retrive.
     */
    public final function fetchSingleVideo($videoId)
    {
        $this->_cacheLogger();
        $this->_cacheExecutionContext();

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Fetching video with ID <code>%s</code>', $videoId));
        }

        $feedFetcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getFeedFetcher();

        $videoUrl   = $this->buildSingleVideoUrl($videoId);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('URL to fetch is <a href="%s">this</a>', $videoUrl));
        }

        $results    = $feedFetcher->fetch($videoUrl, $this->_executionContext->get(tubepress_api_const_options_names_Cache::CACHE_ENABLED));

        /* give the command a chance to do some initial processing */
        $this->_preFactoryExecution($results);

        $videoArray = $this->_feedToVideoArray($results);

        if (empty($videoArray)) {

            return null;
        }

        return $videoArray[0];
    }

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return string The request URL for this gallery.
     */
    protected abstract function buildGalleryUrl($currentPage);

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return string The URL for the single video given.
     */
    protected abstract function buildSingleVideoUrl($id);

    /**
     * Count the total videos in this feed result.
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     */
    protected abstract function getTotalResultCount();

    protected abstract function getLogger();

    /**
     * Determine if we can build a video from this element of the feed.
     *
     * @param integer $index The index into the feed.
     *
     * @return boolean True if we can build a video from this element, false otherwise.
     */
    protected abstract function _canHandleVideo($index);

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @param mixed $feed The feed.
     *
     * @return integer An estimated count of videos in this feed.
     */
    protected abstract function _countVideosInFeed($feed);

    protected abstract function _getAuthorDisplayName($index);
    protected abstract function _getAuthorUid($index);
    protected abstract function _getCategory($index);
    protected abstract function _getRawCommentCount($index);
    protected abstract function _getDescription($index);
    protected abstract function _getDurationInSeconds($index);
    protected abstract function _getHomeUrl($index);
    protected abstract function _getId($index);
    protected abstract function _getKeywordsArray($index);
    protected abstract function _getRawLikeCount($index);
    protected abstract function _getRatingAverage($index);
    protected abstract function _getRawRatingCount($index);
    protected abstract function _getThumbnailUrlsArray($index);
    protected abstract function _getTimeLastUpdatedInUnixTime($index);
    protected abstract function _getTimePublishedInUnixTime($index);
    protected abstract function _getTitle($index);
    protected abstract function _getRawViewCount($index);

    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed $feed The feed to construct.
     *
     * @return void
     */
    protected function _preFactoryExecution($feed)
    {
        //override point
    }

    /**
     * Perform post-construction activites for the feed.
     *
     * @param mixed $feed The feed we used.
     *
     * @return void
     */
    protected function _postFactoryExecution($feed)
    {
        //override point
    }

    /**
     * Builds a video from the index of the feed.
     *
     * @param integer $index The index into the feed.
     *
     * @return tubepress_api_video_Video The constructed video.
     */
    private function _buildVideo($index)
    {
        /* collect the pieces of the video */
        $authorDisplayName     = $this->_getAuthorDisplayName($index);
        $authorUid             = $this->_getAuthorUid($index);
        $category              = $this->_getCategory($index);
        $commentCount          = self::_fancyNumber($this->_getRawCommentCount($index));
        $description           = $this->_trimDescription($this->_getDescription($index));
        $durationInSeconds     = $this->_getDurationInSeconds($index);
        $duration              = tubepress_impl_util_TimeUtils::secondsToHumanTime($durationInSeconds);
        $homeUrl               = $this->_getHomeUrl($index);
        $id                    = $this->_getId($index);
        $keywordsArray         = $this->_getKeywordsArray($index);
        $likesCount            = self::_fancyNumber($this->_getRawLikeCount($index));
        $ratingAverage         = $this->_getRatingAverage($index);
        $ratingCount           = self::_fancyNumber($this->_getRawRatingCount($index));
        $thumbUrl              = $this->_pickThumbnailUrl($this->_getThumbnailUrlsArray($index));
        $timeLastUpdated       = $this->_unixTimeToHumanReadable($this->_getTimeLastUpdatedInUnixTime($index));
        $timePublishedUnixTime = $this->_getTimePublishedInUnixTime($index);
        $timePublished         = $this->_unixTimeToHumanReadable($timePublishedUnixTime);
        $title                 = $this->_getTitle($index);
        $viewCount             = self::_fancyNumber($this->_getRawViewCount($index));

        /* now build a video out of them */
        $vid = new tubepress_api_video_Video();

        $vid->setAuthorDisplayName($authorDisplayName);
        $vid->setAuthorUid($authorUid);
        $vid->setCategory($category);
        $vid->setCommentCount($commentCount);
        $vid->setDescription($description);
        $vid->setDuration($duration);
        $vid->setHomeUrl($homeUrl);
        $vid->setId($id);
        $vid->setKeywords($keywordsArray);
        $vid->setLikesCount($likesCount);
        $vid->setRatingAverage($ratingAverage);
        $vid->setRatingCount($ratingCount);
        $vid->setThumbnailUrl($thumbUrl);
        $vid->setTimeLastUpdated($timeLastUpdated);
        $vid->setTimePublished($timePublished);
        $vid->setTitle($title);
        $vid->setViewCount($viewCount);

        $vid->setAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, $timePublishedUnixTime);
        $vid->setAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS, $durationInSeconds);
        $vid->setAttribute(tubepress_api_video_Video::ATTRIBUTE_PROVIDER_NAME, $this->getName());

        return $vid;
    }

    /**
     * Optionally trims the description.
     *
     * @param string $description The incoming description.
     *
     * @return string The optionally trimmed description.
     */
    private function _trimDescription($description)
    {
        $limit = $this->_executionContext->get(tubepress_api_const_options_names_Meta::DESC_LIMIT);

        if ($limit > 0 && strlen($description) > $limit) {

            $description = substr($description, 0, $limit) . '...';
        }

        return $description;
    }

    /**
     * Given a unix time, return a human-readable version.
     *
     * @param mixed $unixTime The given unix time.
     *
     * @return string A human readable time.
     */
    private function _unixTimeToHumanReadable($unixTime)
    {
        if ($unixTime == '') {

            return '';
        }

        if ($this->_executionContext->get(tubepress_api_const_options_names_Meta::RELATIVE_DATES)) {

            return tubepress_impl_util_TimeUtils::getRelativeTime($unixTime);
        }

        return @date($this->_executionContext->get(tubepress_api_const_options_names_Meta::DATEFORMAT), $unixTime);
    }

    private function _cacheLogger()
    {
        if (! isset($this->_logger)) {

            $this->_logger = $this->getLogger();
        }
    }

    private function _cacheExecutionContext()
    {
        if (! isset($this->_executionContext)) {

            $this->_executionContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        }
    }

    /**
     * Converts raw video feeds to TubePress videos
     *
     * @param mixed $feed The raw feed result from the video provider
     *
     * @return array an array of TubePress videos generated from the feed (may be empty).
     */
    private function _feedToVideoArray($feed)
    {
        $videos = $this->_buildVideoArrayFromFeed($feed);

        if (count($videos) === 0) {

            /** short circuit. */
            return $videos;
        }

        $eventDispatcherService = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        /**
         * Throw up an construction event for each video.
         */
        for ($x = 0; $x < count($videos); $x++) {

            $videoConstructionEvent = new tubepress_api_event_TubePressEvent(

                $videos[$x]
            );

            $eventDispatcherService->dispatch(

                tubepress_api_const_event_CoreEventNames::VIDEO_CONSTRUCTION,
                $videoConstructionEvent
            );

            $videos[$x] = $videoConstructionEvent->getSubject();
        }

        return $videos;
    }

    /**
     * Converts raw video feeds to TubePress videos
     *
     * @param mixed $feed The raw feed result from the video provider
     *
     * @return array an array of TubePress videos generated from the feed (may be empty).
     */
    private function _buildVideoArrayFromFeed($feed)
    {
        $results        = array();
        $total          = $this->_countVideosInFeed($feed);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Now attempting to build %d video(s) from raw feed', $total));
        }

        for ($index = 0; $index < $total; $index++) {

            if (! $this->_canHandleVideo($index)) {

                if ($this->_logger->isDebugEnabled()) {

                    $this->_logger->debug(sprintf('Skipping video at index %d', $index));
                }

                continue;
            }

            /* build the video */
            $results[] = $this->_buildVideo($index);
        }

        /* give the command a chance to do some post processing */
        $this->_postFactoryExecution($feed);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Built %d video(s) from raw feed', sizeof($results)));
        }

        return $results;
    }

    /**
     * Choose a thumbnail URL for the video.
     *
     * @param array $urls An array of URLs from which to choose.
     *
     * @return string A single thumbnail URL.
     */
    private function _pickThumbnailUrl($urls)
    {
        if (! is_array($urls) || sizeof($urls) == 0) {

            return '';
        }

        $random = $this->_executionContext->get(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);

        if ($random) {

            return $urls[array_rand($urls)];

        } else {

            return $urls[0];
        }
    }

    /**
     * Builds a "fancy" number for the given number.
     *
     * @param mixed $num The candidate.
     *
     * @return string A formatted number, or "N/A" if non-numeric.
     */
    private static function _fancyNumber($num)
    {
        if (! is_numeric($num)) {

            return 'N/A';
        }

        return number_format($num);
    }
}
