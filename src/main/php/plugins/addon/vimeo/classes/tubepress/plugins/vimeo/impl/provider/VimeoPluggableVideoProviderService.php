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
 * Handles the heavy lifting for Vimeo.
 */
class tubepress_plugins_vimeo_impl_provider_VimeoPluggableVideoProviderService extends tubepress_impl_provider_AbstractFetchingAndBuildingPluggableVideoProviderService
{
    private static $_sources = array(

        tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
        tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
        tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
        tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
        tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
        tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
        tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
        tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY
    );

    /**
     * @var ehough_epilog_api_ILogger Logger.
     */
    private $_logger;

    /**
     * @var tubepress_spi_provider_UrlBuilder URL builder.
     */
    private $_urlBuilder;

    private $_unserialized;

    public function __construct(

        tubepress_spi_provider_UrlBuilder $urlBuilder
    )
    {
        $this->_logger     = ehough_epilog_api_LoggerFactory::getLogger('Vimeo Video Provider');
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Ask this video provider if it recognizes the given video ID.
     *
     * @param string $videoId The globally unique video identifier.
     *
     * @return boolean True if this provider recognizes the given video ID, false otherwise.
     */
    public final function recognizesVideoId($videoId)
    {
        return is_numeric($videoId);
    }

    /**
     * @return array An array of the valid option values for the "mode" option.
     */
    public final function getGallerySourceNames()
    {
        return self::$_sources;
    }

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'vimeo';
    }

    /**
     * Count the total videos in this feed result.
     *
     * @param mixed $rawFeed The raw video feed (varies depending on provider)
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     */
    protected final function getTotalResultCount($rawFeed)
    {
        $feed = @unserialize($rawFeed);

        if ($feed === false) {

            return 0;
        }

        if (isset($feed->stat) && $feed->stat === 'fail') {

            return 0;
        }

        $this->_unserialized = $feed;

        return isset($feed->videos->total) ? $feed->videos->total : 0;
    }

    protected final function getLogger()
    {
        return $this->_logger;
    }

    /**
     * Determine if we can build a video from this element of the feed.
     *
     * @param integer $index The index into the feed.
     *
     * @return boolean True if we can build a video from this element, false otherwise.
     */
    protected final function _canHandleVideo($index)
    {
        return $this->_videoArray[$index]->embed_privacy !== 'nowhere';
    }

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @param mixed $feed The feed.
     *
     * @return integer An estimated count of videos in this feed.
     */
    protected final function _countVideosInFeed($feed)
    {
        return sizeof($this->_videoArray);
    }

    protected final function _getAuthorDisplayName($index)
    {
        return $this->_videoArray[$index]->owner->display_name;
    }

    protected final function _getAuthorUid($index)
    {
        return $this->_videoArray[$index]->owner->username;
    }

    protected final function _getCategory($index)
    {
        return '';
    }

    protected final function _getRawCommentCount($index)
    {
        return '';
    }

    protected final function _getDescription($index)
    {
        return $this->_videoArray[$index]->description;
    }

    protected final function _getDurationInSeconds($index)
    {
        return $this->_videoArray[$index]->duration;
    }

    protected final function _getHomeUrl($index)
    {
        return 'http://vimeo.com/' . $this->_videoArray[$index]->id;
    }

    protected final function _getId($index)
    {
        return $this->_videoArray[$index]->id;
    }

    protected final function _getKeywordsArray($index)
    {
        return self::_gatherArrayOfContent($this->_videoArray[$index], 'tags', 'tag');
    }

    protected final function _getRawLikeCount($index)
    {
        return $this->_videoArray[$index]->number_of_likes;
    }

    protected final function _getRatingAverage($index)
    {
        return '';
    }

    protected final function _getRawRatingCount($index)
    {
        return '';
    }

    protected final function _getThumbnailUrlsArray($index)
    {
        $raw = self::_gatherArrayOfContent($this->_videoArray[$index], 'thumbnails', 'thumbnail');

        return array($raw[0]);
    }

    protected final function _getTimeLastUpdatedInUnixTime($index)
    {
        return '';
    }

    protected final function _getTimePublishedInUnixTime($index)
    {
        return @strtotime($this->_videoArray[$index]->upload_date);
    }

    protected final function _getTitle($index)
    {
        return $this->_videoArray[$index]->title;
    }

    protected final function _getRawViewCount($index)
    {
        return $this->_videoArray[$index]->number_of_plays;
    }

    protected function _preFactoryExecution($feed)
    {
        if (! isset($this->_unserialized)) {

            $this->getTotalResultCount($feed);
        }

        $unserialized = $this->_unserialized;

        if (isset($unserialized->video)) {

            $this->_videoArray = (array) $unserialized->video;

            unset($this->_unserialized);

            return;
        }

        if (isset($unserialized->videos) && isset($unserialized->videos->video)) {

            $this->_videoArray = (array) $unserialized->videos->video;

            unset($this->_unserialized);

            return;
        }

        unset($this->_unserialized);

        $this->_videoArray = array();
    }

    protected function _postFactoryExecution($feed)
    {
        unset($this->_videoArray);
        unset($this->_unserialized);
    }

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return string The request URL for this gallery.
     */
    protected function buildGalleryUrl($currentPage)
    {
        return $this->_urlBuilder->buildGalleryUrl($currentPage);
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return string The URL for the single video given.
     */
    protected function buildSingleVideoUrl($id)
    {
        if (! $this->recognizesVideoId($id)) {

            throw new InvalidArgumentException("Unable to build Vimeo URL for video with ID $id");
        }

        return $this->_urlBuilder->buildSingleVideoUrl($id);
    }

    protected static function _gatherArrayOfContent($node, $firstDimension, $secondDimension)
    {
        $results = array();

        if (isset($node->$firstDimension) && is_array($node->$firstDimension->$secondDimension)) {

            foreach ($node->$firstDimension->$secondDimension as $item) {

                $results[] = $item->_content;
            }
        }

        return $results;
    }
}
