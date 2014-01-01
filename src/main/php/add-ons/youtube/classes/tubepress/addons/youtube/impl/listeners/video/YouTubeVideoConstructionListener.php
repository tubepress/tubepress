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
 * Builds YouTube videos.
 */
class tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener extends tubepress_impl_listeners_video_AbstractVideoConstructionListener
{
    /**
     * Build a map of attribute names => attribute values for the video construction event.
     *
     * @param tubepress_api_event_EventInterface $event The video construction event.
     *
     * @return array An associative array of attribute names => attribute values
     */
    protected final function buildAttributeMap(tubepress_api_event_EventInterface $event)
    {
        $toReturn = array();
        $xpath    = $event->getArgument('xPath');
        $index    = $event->getArgument('zeroBasedFeedIndex');

        /* Author */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_AUTHOR_DISPLAY_NAME] =
            $this->_relativeQuery($xpath, $index, 'atom:author/atom:name')->item(0)->nodeValue;
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_AUTHOR_USER_ID] =
            $toReturn[tubepress_api_video_Video::ATTRIBUTE_AUTHOR_DISPLAY_NAME];

        /* Category */
        /** @noinspection PhpUndefinedMethodInspection */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_CATEGORY_DISPLAY_NAME] =
            trim($this->_relativeQuery($xpath, $index, 'media:group/media:category')->item(0)->getAttribute('label'));

        /* Description */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_DESCRIPTION] =
            $this->trimDescription($this->_relativeQuery($xpath, $index, 'media:group/media:description')->item(0)->nodeValue);

        /* Duration */
        /** @noinspection PhpUndefinedMethodInspection */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS] =
            $this->_relativeQuery($xpath, $index, 'media:group/yt:duration')->item(0)->getAttribute('seconds');
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_DURATION_FORMATTED] =
            tubepress_impl_util_TimeUtils::secondsToHumanTime($toReturn[tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS]);

        /* Home URL */
        /** @noinspection PhpUndefinedMethodInspection */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_HOME_URL] =
            $this->_relativeQuery($xpath, $index, "atom:link[@rel='alternate']")->item(0)->getAttribute('href');

        /* ID */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_ID] =
            $this->_relativeQuery($xpath, $index, 'media:group/yt:videoid')->item(0)->nodeValue;

        /* Keywords. */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_KEYWORD_ARRAY] = array();

        /* Rating */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_RATING_AVERAGE] = $this->_getRatingAverage($xpath, $index);
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_RATING_COUNT] = $this->_getRatingCount($xpath, $index);

        /* Thumbnail. */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_THUMBNAIL_URL] =
            $this->pickThumbnailUrl($this->_getThumbnailUrls($xpath, $index));

        /* Time published. */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME] =
            $this->_getTimePublishedUnixTime($xpath, $index);
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_FORMATTED] =
            $this->unixTimeToHumanReadable($toReturn[tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME]);

        /* Title. */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_TITLE] =
            $this->_relativeQuery($xpath, $index, 'atom:title')->item(0)->nodeValue;

        /* Views. */
        $toReturn[tubepress_api_video_Video::ATTRIBUTE_VIEW_COUNT] =
            $this->fancyNumber($this->_getRawViewCount($xpath, $index));

        return $toReturn;
    }

    /**
     * @return string The name of the provider that this filter handles.
     */
    protected final function getHandledProviderName()
    {
        return 'youtube';
    }

    private function _relativeQuery(DOMXPath $xpath, $index, $query)
    {
        return $xpath->query('//atom:entry[' . ($index + 1) . "]/$query");
    }

    private function _getRatingAverage(DOMXPath $xpath, $index)
    {
        $count = $this->_relativeQuery($xpath, $index, 'gd:rating')->item(0);

        if ($count != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return number_format($count->getAttribute('average'), 2);
        }

        return '';
    }

    private function _getRatingCount(DOMXPath $xpath, $index)
    {
        $count = $this->_relativeQuery($xpath, $index, 'gd:rating')->item(0);

        if ($count != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return number_format($count->getAttribute('numRaters'));
        }

        return '';
    }

    private function _getThumbnailUrls(DOMXPath $xpath, $index)
    {
        $thumbs = $this->_relativeQuery($xpath, $index, 'media:group/media:thumbnail');
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

    private function _getTimePublishedUnixTime(DOMXPath $xpath, $index)
    {
        $publishedNode = $this->_relativeQuery($xpath, $index, 'media:group/yt:uploaded');

        if ($publishedNode->length == 0) {

            return '';
        }

        $rawTime = $publishedNode->item(0)->nodeValue;

        return tubepress_impl_util_TimeUtils::rfc3339toUnixTime($rawTime);
    }

    private function _getRawViewCount(DOMXPath $xpath, $index)
    {
        $stats = $this->_relativeQuery($xpath, $index, 'yt:statistics')->item(0);

        if ($stats != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return $stats->getAttribute('viewCount');
        }

        return '';
    }
}