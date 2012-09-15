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
 * Video factory for YouTube
 */
class tubepress_impl_factory_commands_YouTubeFactoryCommand extends tubepress_impl_factory_commands_AbstractFactoryCommand
{
    /* shorthands for the namespaces */
    const NS_APP   = 'http://www.w3.org/2007/app';
    const NS_ATOM  = 'http://www.w3.org/2005/Atom';
    const NS_MEDIA = 'http://search.yahoo.com/mrss/';
    const NS_YT    = 'http://gdata.youtube.com/schemas/2007';
    const NS_GD    = 'http://schemas.google.com/g/2005';

    /** @var DOMXPath */
    private $_xpath;

    /** @var ehough_epilog_api_ILogger */
    private $_logger;

    /**
     * Determine if we can handle this feed.
     *
     * @param mixed $feed The feed to handle.
     *
     * @return boolean True if this command can handle the feed, false otherwise.
     */
    protected final function _canHandleFeed($feed)
    {
        return is_string($feed) && strpos($feed, "http://gdata.youtube.com/schemas/2007") !== false;
    }

    /**
     * @return DOMXPath XPath.
     */
    protected final function getXpath()
    {
        return $this->_xpath;
    }

    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed $feed The feed to construct.
     *
     * @return void
     */
    protected final function _preExecute($feed)
    {
        $this->_xpath = $this->_createXPath($this->_createDomDocument($feed));
    }

    /**
     * Perform post-construction activites for the feed.
     *
     * @param mixed $feed The feed we used.
     *
     * @return void
     */
    protected final function _postExecute($feed)
    {
        unset($this->_xpath);
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

    protected function _getAuthorDisplayName($index)
    {
        return $this->_getAuthorUid($index);
    }

    protected function _getAuthorUid($index)
    {
        return $this->_relativeQuery($index, 'atom:author/atom:name')->item(0)->nodeValue;
    }

    protected function _getCategory($index)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return trim($this->_relativeQuery($index, 'media:group/media:category')->item(0)->getAttribute('label'));
    }

    protected function _getRawCommentCount($index)
    {
        return '';
    }

    protected function _getDescription($index)
    {
        return $this->_relativeQuery($index, 'media:group/media:description')->item(0)->nodeValue;
    }

    protected function _getDurationInSeconds($index)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->_relativeQuery($index, 'media:group/yt:duration')->item(0)->getAttribute('seconds');
    }

    protected function _getHomeUrl($index)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $rawUrl = $this->_relativeQuery($index, "atom:link[@rel='alternate']")->item(0)->getAttribute('href');
        $url    = new ehough_curly_Url($rawUrl);

        return $url->toString(true);
    }

    protected function _getId($index)
    {
        $link    = $this->_relativeQuery($index, "atom:link[@type='text/html']")->item(0);

        /** @noinspection PhpUndefinedMethodInspection */
        preg_match('/.*v=(.{11}).*/', $link->getAttribute('href'), $matches);

        return $matches[1];
    }

    protected function _getKeywordsArray($index)
    {
        $rawKeywords = $this->_relativeQuery($index, 'media:group/media:keywords')->item(0);
        $raw         = trim($rawKeywords->nodeValue);

        return explode(', ', $raw);
    }

    protected function _getRawLikeCount($index)
    {
        return '';
    }

    protected function _getRatingAverage($index)
    {
        $count = $this->_relativeQuery($index, 'gd:rating')->item(0);

        if ($count != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return number_format($count->getAttribute('average'), 2);
        }

        return '';
    }

    protected function _getRawRatingCount($index)
    {
        $count = $this->_relativeQuery($index, 'gd:rating')->item(0);

        if ($count != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return $count->getAttribute('numRaters');
        }

        return '';
    }

    protected function _getThumbnailUrlsArray($index)
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

    protected function _getTimeLastUpdatedInUnixTime($index)
    {
        return '';
    }

    protected function _getTimePublishedInUnixTime($index)
    {
        $publishedNode = $this->_relativeQuery($index, 'media:group/yt:uploaded');

        if ($publishedNode->length == 0) {

            return '';
        }

        $rawTime = $publishedNode->item(0)->nodeValue;

        return tubepress_impl_util_TimeUtils::rfc3339toUnixTime($rawTime);
    }

    protected function _getTitle($index)
    {
        return $this->_relativeQuery($index, 'atom:title')->item(0)->nodeValue;
    }

    protected function _getRawViewCount($index)
    {
        $stats = $this->_relativeQuery($index, 'yt:statistics')->item(0);

        if ($stats != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return $stats->getAttribute('viewCount');
        }

        return '';
    }

    /**
     * @param $index
     * @param $query
     *
     * @return DOMNodeList DOM node list.
     */
    protected function _relativeQuery($index, $query)
    {
        return $this->_xpath->query('//atom:entry[' . ($index + 1) . "]/$query");
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
        $xpath->registerNamespace('atom', self::NS_ATOM);
        $xpath->registerNamespace('yt', self::NS_YT);
        $xpath->registerNamespace('gd', self::NS_GD);
        $xpath->registerNamespace('media', self::NS_MEDIA);
        $xpath->registerNamespace('app', self::NS_APP);
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

        return $doc;
    }

    /**
     * @return ehough_epilog_api_ILogger Get the logger for this command.
     */
    protected final function getLogger()
    {
        if (! isset($this->_logger)) {

            $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('YouTube Video Factory');
        }

        return $this->_logger;
    }
}
