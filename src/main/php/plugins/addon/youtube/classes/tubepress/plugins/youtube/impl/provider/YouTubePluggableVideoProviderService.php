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
 * Handles the heavy lifting for YouTube.
 */
class tubepress_plugins_youtube_impl_provider_YouTubePluggableVideoProviderService extends tubepress_impl_provider_AbstractFetchingAndBuildingPluggableVideoProviderService
{
    private static $_NAMESPACE_OPENSEARCH = 'http://a9.com/-/spec/opensearch/1.1/';
    private static $_NAMESPACE_APP        = 'http://www.w3.org/2007/app';
    private static $_NAMESPACE_ATOM       = 'http://www.w3.org/2005/Atom';
    private static $_NAMESPACE_MEDIA      = 'http://search.yahoo.com/mrss/';
    private static $_NAMESPACE_YT         = 'http://gdata.youtube.com/schemas/2007';
    private static $_NAMESPACE_GD         = 'http://schemas.google.com/g/2005';

    private static $_sources = array(

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
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
     * @var ehough_epilog_api_ILogger Logger.
     */
    private $_logger;

    /**
     * @var tubepress_spi_provider_UrlBuilder URL builder.
     */
    private $_urlBuilder;

    public function __construct(

        tubepress_spi_provider_UrlBuilder $urlBuilder
    )
    {
        $this->_logger     = ehough_epilog_api_LoggerFactory::getLogger('YouTube Video Provider');
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
     * @param string $name The name of the option to test.
     *
     * @return boolean True if this provider provided the given option, false otherwise.
     */
    public final function isOptionApplicable($name)
    {
        return in_array($name, array(


        ));
    }

    /**
     * @param string $name The name of the gallery source to test.
     *
     * @return boolean True if this provider supplies the given gallery source, false otherwise.
     */
    public final function providesGallerySource($name)
    {
        return in_array($name, array(


        ));
    }

    /**
     * Count the total videos in this feed result.
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     */
    protected final function getTotalResultCount()
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
    protected final function _canHandleVideo($index)
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
    protected final function _countVideosInFeed($feed)
    {
        return $this->_xpath->query('//atom:entry')->length;
    }

    protected final function _getAuthorDisplayName($index)
    {
        return $this->_getAuthorUid($index);
    }

    protected final function _getAuthorUid($index)
    {
        return $this->_relativeQuery($index, 'atom:author/atom:name')->item(0)->nodeValue;
    }

    protected final function _getCategory($index)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return trim($this->_relativeQuery($index, 'media:group/media:category')->item(0)->getAttribute('label'));
    }

    protected final function _getRawCommentCount($index)
    {
        return '';
    }

    protected final function _getDescription($index)
    {
        return $this->_relativeQuery($index, 'media:group/media:description')->item(0)->nodeValue;
    }

    protected final function _getDurationInSeconds($index)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->_relativeQuery($index, 'media:group/yt:duration')->item(0)->getAttribute('seconds');
    }

    protected final function _getHomeUrl($index)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $rawUrl = $this->_relativeQuery($index, "atom:link[@rel='alternate']")->item(0)->getAttribute('href');
        $url    = new ehough_curly_Url($rawUrl);

        return $url->toString(true);
    }

    protected final function _getId($index)
    {
        $link    = $this->_relativeQuery($index, "atom:link[@type='text/html']")->item(0);

        /** @noinspection PhpUndefinedMethodInspection */
        preg_match('/.*v=(.{11}).*/', $link->getAttribute('href'), $matches);

        return $matches[1];
    }

    protected final function _getKeywordsArray($index)
    {
        $rawKeywords = $this->_relativeQuery($index, 'media:group/media:keywords')->item(0);
        $raw         = trim($rawKeywords->nodeValue);

        return explode(', ', $raw);
    }

    protected final function _getRawLikeCount($index)
    {
        return '';
    }

    protected final function _getRatingAverage($index)
    {
        $count = $this->_relativeQuery($index, 'gd:rating')->item(0);

        if ($count != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return number_format($count->getAttribute('average'), 2);
        }

        return '';
    }

    protected final function _getRawRatingCount($index)
    {
        $count = $this->_relativeQuery($index, 'gd:rating')->item(0);

        if ($count != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return $count->getAttribute('numRaters');
        }

        return '';
    }

    protected final function _getThumbnailUrlsArray($index)
    {
        $thumbs = $this->_relativeQuery($index, 'media:group/media:thumbnail');
        $result = array();

        foreach ($thumbs as $thumb) {

            /** @noinspection PhpUndefinedMethodInspection */
            $url = $thumb->getAttribute('url');

            if (strpos($url, 'hqdefault') === false && strpos($url, 'mqdefault') === false) {

                $result[] = $url;
            }
        }

        return $result;
    }

    protected final function _getTimeLastUpdatedInUnixTime($index)
    {
        return '';
    }

    protected final function _getTimePublishedInUnixTime($index)
    {
        $publishedNode = $this->_relativeQuery($index, 'media:group/yt:uploaded');

        if ($publishedNode->length == 0) {

            return '';
        }

        $rawTime = $publishedNode->item(0)->nodeValue;

        return tubepress_impl_util_TimeUtils::rfc3339toUnixTime($rawTime);
    }

    protected final function _getTitle($index)
    {
        return $this->_relativeQuery($index, 'atom:title')->item(0)->nodeValue;
    }

    protected final function _getRawViewCount($index)
    {
        $stats = $this->_relativeQuery($index, 'yt:statistics')->item(0);

        if ($stats != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return $stats->getAttribute('viewCount');
        }

        return '';
    }

    protected function _preFactoryExecution($feed)
    {
        $this->_createDomDocument($feed);
        $this->_xpath = $this->_createXPath($this->_domDocument);
    }

    protected function _postFactoryExecution($feed)
    {
        unset($this->_domDocument);
        unset($this->_xpath);
    }

    private static function _makeSureNumeric($result)
    {
        if (is_numeric($result) === false) {

            throw new RuntimeException("YouTube returned a non-numeric result count: $result");
        }
    }

    private function _createXPath(DOMDocument $doc)
    {
        if ($this->_logger->isDebugEnabled()) {

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
        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug('Attempting to load XML from YouTube');
        }

        if (! class_exists('DOMDocument')) {

            throw new RuntimeException('DOMDocument class not found');
        }

        $doc = new DOMDocument();

        if ($doc->loadXML($feed) === false) {

            throw new RuntimeException('Could not parse XML from YouTube');
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug('Successfully loaded XML from YouTube');
        }

        $this->_domDocument = $doc;
    }

    private function _relativeQuery($index, $query)
    {
        return $this->_xpath->query('//atom:entry[' . ($index + 1) . "]/$query");
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

            throw new InvalidArgumentException("Unable to build YouTube URL for video with ID $id");
        }

        return $this->_urlBuilder->buildSingleVideoUrl($id);
    }

    /**
     * @return array An array of meta names
     */
    public final function getAdditionalMetaNames()
    {
        return array(

            tubepress_plugins_youtube_api_const_options_names_Meta::RATING,
            tubepress_plugins_youtube_api_const_options_names_Meta::RATINGS
        );
    }
}
