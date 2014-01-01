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
 * Handles the heavy lifting for YouTube.
 */
class tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService extends tubepress_impl_provider_AbstractPluggableVideoProviderService
{
    private static $_NAMESPACE_OPENSEARCH = 'http://a9.com/-/spec/opensearch/1.1/';
    private static $_NAMESPACE_APP        = 'http://www.w3.org/2007/app';
    private static $_NAMESPACE_ATOM       = 'http://www.w3.org/2005/Atom';
    private static $_NAMESPACE_MEDIA      = 'http://search.yahoo.com/mrss/';
    private static $_NAMESPACE_YT         = 'http://gdata.youtube.com/schemas/2007';
    private static $_NAMESPACE_GD         = 'http://schemas.google.com/g/2005';

    private static $_sources = array(

        tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
        tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
        tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
        tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
        tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
        tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
    );

    /**
     * @var DOMDocument DOM Document.
     */
    private $_domDocument;

    /**
     * @var DOMXPath XPath.
     */
    private $_xpath;

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var tubepress_spi_provider_UrlBuilder URL builder.
     */
    private $_urlBuilder;

    public function __construct(tubepress_spi_provider_UrlBuilder $urlBuilder)
    {
        $this->_logger     = ehough_epilog_LoggerFactory::getLogger('YouTube Video Provider');
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
        return preg_match_all('/^[A-Za-z0-9-_]{11}$/', $videoId, $matches) === 1;
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
        return 'youtube';
    }

    /**
     * @return string The human-readable name of this video provider.
     */
    public final function getFriendlyName()
    {
        return 'YouTube';
    }

    /**
     * @return array An array of meta names
     */
    public final function getAdditionalMetaNames()
    {
        return array(

            tubepress_addons_youtube_api_const_options_names_Meta::RATING,
            tubepress_addons_youtube_api_const_options_names_Meta::RATINGS
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
        $total        = $this->_domDocument->getElementsByTagNameNS(self::$_NAMESPACE_OPENSEARCH, '*');
        $node         = $total->item(0);
        $totalResults = $node->nodeValue;

        self::_makeSureNumeric($totalResults);

        return $totalResults;
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
        $states = $this->_relativeQuery($index, 'app:control/yt:state');

        /* no state applied? we're good to go */
        if ($states->length == 0) {

            return true;
        }

        /* if state is other than limitedSyndication, it's not available */
        return $this->_relativeQuery($index, "app:control/yt:state[@reasonCode='limitedSyndication']")->length !== 0;
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
        return $this->_xpath->query('//atom:entry')->length;
    }

    protected final function prepareForFeedAnalysis($feed)
    {
        $this->_createDomDocument($feed);
        $this->_xpath = $this->_createXPath($this->_domDocument);
    }

    protected final function onFeedAnalysisComplete($feed)
    {
        unset($this->_domDocument);
        unset($this->_xpath);
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

            throw new InvalidArgumentException("Unable to build YouTube URL for video with ID $id");
        }

        return $this->_urlBuilder->buildSingleVideoUrl($id);
    }

    protected final function onBeforeFiringVideoConstructionEvent(tubepress_api_event_EventInterface $event)
    {
        $event->setArgument('domDocument', $this->_domDocument);
        $event->setArgument('xPath', $this->_xpath);
    }

    private static function _makeSureNumeric($result)
    {
        if (is_numeric($result) === false) {

            throw new RuntimeException("YouTube returned a non-numeric result count: $result");
        }
    }

    private function _createXPath(DOMDocument $doc)
    {
        if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            $this->_logger->debug('Building xpath to parse XML');
        }

        if (! class_exists('DOMXPath')) {

            throw new RuntimeException('Class DOMXPath not found');
        }

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('atom', self::$_NAMESPACE_ATOM);
        $xpath->registerNamespace('yt', self::$_NAMESPACE_YT);
        $xpath->registerNamespace('gd', self::$_NAMESPACE_GD);
        $xpath->registerNamespace('media', self::$_NAMESPACE_MEDIA);
        $xpath->registerNamespace('app', self::$_NAMESPACE_APP);

        return $xpath;
    }

    private function _createDomDocument($feed)
    {
        if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            $this->_logger->debug('Attempting to load XML from YouTube');
        }

        if (! class_exists('DOMDocument')) {

            throw new RuntimeException('DOMDocument class not found');
        }

        $doc = new DOMDocument();

        if ($doc->loadXML($feed) === false) {

            throw new RuntimeException('Could not parse XML from YouTube');
        }

        if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            $this->_logger->debug('Successfully loaded XML from YouTube');
        }

        $this->_domDocument = $doc;
    }

    private function _relativeQuery($index, $query)
    {
        return $this->_xpath->query('//atom:entry[' . ($index + 1) . "]/$query");
    }
}
