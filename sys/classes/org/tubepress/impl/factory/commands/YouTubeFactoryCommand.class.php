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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_spi_patterns_cor_Command',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_url_Url',
    'org_tubepress_api_video_Video',
    'org_tubepress_impl_factory_commands_AbstractFactoryCommand',
));

/**
 * Video factory for YouTube
 */
class org_tubepress_impl_factory_commands_YouTubeFactoryCommand extends org_tubepress_impl_factory_commands_AbstractFactoryCommand
{
    /* shorthands for the namespaces */
    const NS_APP   = 'http://www.w3.org/2007/app';
    const NS_ATOM  = 'http://www.w3.org/2005/Atom';
    const NS_MEDIA = 'http://search.yahoo.com/mrss/';
    const NS_YT    = 'http://gdata.youtube.com/schemas/2007';
    const NS_GD    = 'http://schemas.google.com/g/2005';

    const LOG_PREFIX = 'YouTube Video Factory';

    private $_xpath;

    protected function _canHandleFeed($feed)
    {
        return is_string($feed) && strpos($feed, "http://gdata.youtube.com/schemas/2007") !== false;
    }

    protected function getXpath()
    {
        return $this->_xpath;
    }

    protected function _preExecute($feed)
    {
        $this->_xpath = $this->_createXPath($this->_createDomDocument($feed));
    }

    protected function _postExecute($feed)
    {
        unset($this->_xpath);
    }

    protected function _countVideosInFeed($feed)
    {
        return $this->_xpath->query('//atom:entry')->length;
    }

    protected function _canHandleVideo($index)
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
        return $this->_relativeQuery($index, 'media:group/yt:duration')->item(0)->getAttribute('seconds');
    }

    protected function _getHomeUrl($index)
    {
        $rawUrl = $this->_relativeQuery($index, "atom:link[@rel='alternate']")->item(0)->getAttribute('href');
        $url    = new org_tubepress_api_url_Url($rawUrl);

        return $url->toString(true);
    }

    protected function _getId($index)
    {
        $link    = $this->_relativeQuery($index, "atom:link[@type='text/html']")->item(0);
        $matches = array();
        preg_match('/.*v=(.{11}).*/', $link->getAttribute('href'), $matches);

        return $matches[1];
    }

    protected function _getKeywordsArray($index)
    {
        $rawKeywords = $this->_relativeQuery($index, 'media:group/media:keywords')->item(0);
        $raw         = trim($rawKeywords->nodeValue);

        return explode(", ", $raw);
    }

    protected function _getRawLikeCount($index)
    {
        return '';
    }

    protected function _getRatingAverage($index)
    {
        $count = $this->_relativeQuery($index, 'gd:rating')->item(0);
        if ($count != null) {
            return number_format($count->getAttribute('average'), 2);
        }
        return '';
    }

    protected function _getRawRatingCount($index)
    {
        $count = $this->_relativeQuery($index, 'gd:rating')->item(0);
        if ($count != null) {
            return $count->getAttribute('numRaters');
        }
        return '';
    }

    protected function _getThumbnailUrlsArray($index)
    {
        $thumbs = $this->_relativeQuery($index, 'media:group/media:thumbnail');
        $result = array();

        foreach ($thumbs as $thumb) {

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
        return org_tubepress_impl_util_TimeUtils::rfc3339toUnixTime($rawTime);
    }

    protected function _getTitle($index)
    {
        return $this->_relativeQuery($index, 'atom:title')->item(0)->nodeValue;
    }

    protected function _getRawViewCount($index)
    {
        $stats = $this->_relativeQuery($index, 'yt:statistics')->item(0);
        if ($stats != null) {
            return $stats->getAttribute('viewCount');
        }
        return '';
    }

    protected function _relativeQuery($index, $query)
    {
        return $this->_xpath->query('//atom:entry[' . ($index + 1) . "]/$query");
    }

    private function _createXPath(DOMDocument $doc)
    {
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Building xpath to parse XML');

        if (!class_exists('DOMXPath')) {
            throw new Exception('Class DOMXPath not found');
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
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Attempting to load XML from YouTube');

        if (!class_exists('DOMDocument')) {
            throw new Exception('DOMDocument class not found');
        }

        $doc = new DOMDocument();
        if ($doc->loadXML($feed) === false) {
            throw new Exception('Could not parse XML from YouTube');
        }
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Successfully loaded XML from YouTube');
        return $doc;
    }
}
