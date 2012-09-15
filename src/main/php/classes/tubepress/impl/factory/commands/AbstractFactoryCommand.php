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
 * Base class for factory commands.
 */
abstract class tubepress_impl_factory_commands_AbstractFactoryCommand implements ehough_chaingang_api_Command
{
    /**
     * @var tubepress_spi_context_ExecutionContext
     */
    private $_context;

    /**
     * Execute a unit of processing work to be performed.
     *
     * This Command may either complete the required processing and return true,
     * or delegate remaining processing to the next Command in a Chain containing
     * this Command by returning false.
     *
     * @param ehough_chaingang_api_Context $context The Context to be processed by this Command.
     *
     * @return boolean True if the processing of this Context has been completed, or false if the
     *                 processing of this Context should be delegated to a subsequent Command
     *                 in an enclosing Chain.
     */
    public final function execute(ehough_chaingang_api_Context $context)
    {
        $logger = $this->getLogger();

        /* grab the arguments */
        $feed = $context->get(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_RAW_FEED);

        if (! $this->_canHandleFeed($feed)) {

            return false;
        }

        /* give the command a chance to do some initial processing */
        $this->_preExecute($feed);

        $this->_context = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $results        = array();
        $total          = $this->_countVideosInFeed($feed);

        if ($logger->isDebugEnabled()) {

            $logger->debug(sprintf('Now attempting to build %d video(s) from raw feed', $total));
        }

        for ($index = 0; $index < $total; $index++) {

            if (! $this->_canHandleVideo($index)) {

                if ($logger->isDebugEnabled()) {

                    $logger->debug(sprintf('Skipping video at index %d', $index));
                }

                continue;
            }

            /* build the video */
            $results[] = $this->_buildVideo($index);
        }

        /* give the command a chance to do some post processing */
        $this->_postExecute($feed);

        if ($logger->isDebugEnabled()) {

            $logger->debug(sprintf('Built %d video(s) from raw feed', sizeof($results)));
        }

        $context->put(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_VIDEO_ARRAY, $results);

        return true;
    }

    /**
     * Choose a thumbnail URL for the video.
     *
     * @param array $urls An array of URLs from which to choose.
     *
     * @return string A single thumbnail URL.
     */
    protected function _pickThumbnailUrl($urls)
    {
        if (! is_array($urls) || sizeof($urls) == 0) {

            return '';
        }

        $random = $this->_context->get(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);

        if ($random) {

            return $urls[array_rand($urls)];

        } else {

            return $urls[0];
        }
    }

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
     * Determine if we can handle this feed.
     *
     * @param mixed $feed The feed to handle.
     *
     * @return boolean True if this command can handle the feed, false otherwise.
     */
    protected abstract function _canHandleFeed($feed);

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

    /**
     * @return ehough_epilog_api_ILogger Get the logger for this command.
     */
    protected abstract function getLogger();

    /**
     * Perform post-construction activites for the feed.
     *
     * @param mixed $feed The feed we used.
     *
     * @return void
     */
    protected abstract function _postExecute($feed);

    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed $feed The feed to construct.
     *
     * @return void
     */
    protected abstract function _preExecute($feed);

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
        $limit = $this->_context->get(tubepress_api_const_options_names_Meta::DESC_LIMIT);

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

        if ($this->_context->get(tubepress_api_const_options_names_Meta::RELATIVE_DATES)) {

            return tubepress_impl_util_TimeUtils::getRelativeTime($unixTime);
        }

        return @date($this->_context->get(tubepress_api_const_options_names_Meta::DATEFORMAT), $unixTime);
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
