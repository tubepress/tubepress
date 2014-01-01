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

/**
 * Handles the heavy lifting for Vimeo.
 */
class tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService extends tubepress_impl_provider_AbstractPluggableVideoProviderService
{
    private static $_sources = array(

        tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
        tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
        tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
        tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
        tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
        tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
        tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
        tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY
    );

    /**
     * @var ehough_epilog_psr_LoggerInterface Logger.
     */
    private $_logger;

    /**
     * @var tubepress_spi_provider_UrlBuilder URL builder.
     */
    private $_urlBuilder;

    private $_unserialized;

    private $_videoArray;

    public function __construct(tubepress_spi_provider_UrlBuilder $urlBuilder)
    {
        $this->_logger     = ehough_epilog_LoggerFactory::getLogger('Vimeo Video Provider');
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
     * @return string The human-readable name of this video provider.
     */
    public final function getFriendlyName()
    {
        return 'Vimeo';
    }

    /**
     * @return array An array of meta names
     */
    public final function getAdditionalMetaNames()
    {
        return array(

            tubepress_addons_vimeo_api_const_options_names_Meta::LIKES
        );
    }



    /**
     * Count the total videos in this feed result.
     *
     * @param mixed $feed The raw feed from the provider.
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     */
    protected final function getTotalResultCount($feed)
    {
        return isset($this->_unserialized->videos->total) ? $this->_unserialized->videos->total : 0;
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
    protected final function canWorkWithVideoAtIndex($index)
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
    protected final function countVideosInFeed($feed)
    {
        return sizeof($this->_videoArray);
    }

    protected final function prepareForFeedAnalysis($feed)
    {
        $this->_unserialized = @unserialize($feed);
        $this->_videoArray   = array();

        /*
         * Make sure we can actually unserialize the feed.
         */
        if ($this->_unserialized === false) {

            throw new RuntimeException('Unable to unserialize PHP from Vimeo');
        }

        /*
         * Make sure Vimeo is happy.
         */
        if ($this->_unserialized->stat !== 'ok') {

            throw new RuntimeException($this->_getErrorMessageFromVimeo());
        }

        /*
         * Is this just a single video?
         */
        if (isset($this->_unserialized->video)) {

            $this->_videoArray = (array) $this->_unserialized->video;

            return;
        }

        /*
         * Must be a page of videos.
         */
        if (isset($this->_unserialized->videos) && isset($this->_unserialized->videos->video)) {

            $this->_videoArray = (array) $this->_unserialized->videos->video;
        }
    }

    protected final function onFeedAnalysisComplete($feed)
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
    protected final function buildGalleryUrl($currentPage)
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
    protected final function buildSingleVideoUrl($id)
    {
        if (! $this->recognizesVideoId($id)) {

            throw new InvalidArgumentException("Unable to build Vimeo URL for video with ID $id");
        }

        return $this->_urlBuilder->buildSingleVideoUrl($id);
    }

    protected final function onBeforeFiringVideoConstructionEvent(tubepress_api_event_EventInterface $event)
    {
        $event->setArgument('unserializedFeed', $this->_unserialized);
        $event->setArgument('videoArray', $this->_videoArray);
    }

    private function _getErrorMessageFromVimeo()
    {
        $unserialized = $this->_unserialized;

        if (!$unserialized || !isset($unserialized->stat) || $unserialized->stat !== 'fail') {

            return 'Vimeo responded with an unknown error.';
        }

        return 'Vimeo responded to TubePress with an error: ' . $unserialized->err->msg;
    }
}
