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
class tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener
{
    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_util_api_TimeUtilsInterface
     */
    private $_timeUtils;

    public function __construct(tubepress_core_options_api_ContextInterface $context,
                                tubepress_core_util_api_TimeUtilsInterface  $timeUtils)
    {
        $this->_context   = $context;
        $this->_timeUtils = $timeUtils;
    }

    public function onVideoConstruction(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $video tubepress_core_media_item_api_MediaItem
         */
        $video = $event->getSubject();

        /**
         * @var $provider tubepress_core_media_provider_api_MediaProviderInterface
         */
        $provider = $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER);

        /*
         * Short circuit for videos belonging to someone else.
         */
        if ($provider->getName() !== 'youtube') {

            return;
        }

        $attributeMap = $this->buildAttributeMap($event);

        foreach ($attributeMap as $attributeName => $attributeValue) {

            $video->setAttribute($attributeName, $attributeValue);
        }
    }

    /**
     * Build a map of attribute names => attribute values for the video construction event.
     *
     * @param tubepress_core_event_api_EventInterface $event The video construction event.
     *
     * @return array An associative array of attribute names => attribute values
     */
    protected function buildAttributeMap(tubepress_core_event_api_EventInterface $event)
    {
        $toReturn = array();
        $xpath    = $event->getArgument('xPath');
        $index    = $event->getArgument('zeroBasedFeedIndex');

        /* Author */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME] =
            $this->_relativeQuery($xpath, $index, 'atom:author/atom:name')->item(0)->nodeValue;
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_USER_ID] =
            $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME];

        /* Category */
        /** @noinspection PhpUndefinedMethodInspection */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_CATEGORY_DISPLAY_NAME] =
            trim($this->_relativeQuery($xpath, $index, 'media:group/media:category')->item(0)->getAttribute('label'));

        /* Description */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION] =
            $this->_relativeQuery($xpath, $index, 'media:group/media:description')->item(0)->nodeValue;

        /* Duration */
        /** @noinspection PhpUndefinedMethodInspection */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_SECONDS] =
            $this->_relativeQuery($xpath, $index, 'media:group/yt:duration')->item(0)->getAttribute('seconds');

        /* Home URL */
        /** @noinspection PhpUndefinedMethodInspection */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_HOME_URL] =
            $this->_relativeQuery($xpath, $index, "atom:link[@rel='alternate']")->item(0)->getAttribute('href');

        /* Keywords. */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_KEYWORD_ARRAY] = array();

        /* Rating */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_AVERAGE] = $this->_getRatingAverage($xpath, $index);
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_COUNT] = $this->_getRatingCount($xpath, $index);

        /* Thumbnail. */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_THUMBNAIL_URL] =
            $this->pickThumbnailUrl($this->_getThumbnailUrls($xpath, $index));

        /* Time published. */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME] =
            $this->_getTimePublishedUnixTime($xpath, $index);

        /* Title. */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE] =
            $this->_relativeQuery($xpath, $index, 'atom:title')->item(0)->nodeValue;

        /* Views. */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT] =
            $this->_getRawViewCount($xpath, $index);

        return $toReturn;
    }

    private function _getRatingAverage(DOMXPath $xpath, $index)
    {
        $count = $this->_relativeQuery($xpath, $index, 'gd:rating')->item(0);

        if ($count != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return $count->getAttribute('average');
        }

        return '';
    }

    private function _getRatingCount(DOMXPath $xpath, $index)
    {
        $count = $this->_relativeQuery($xpath, $index, 'gd:rating')->item(0);

        if ($count != null) {

            /** @noinspection PhpUndefinedMethodInspection */
            return $count->getAttribute('numRaters');
        }

        return '';
    }

    private function _relativeQuery(DOMXPath $xpath, $index, $query)
    {
        return $xpath->query('//atom:entry[' . ($index + 1) . "]/$query");
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

        return $this->_timeUtils->rfc3339toUnixTime($rawTime);
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

    /**
     * Choose a thumbnail URL for the video.
     *
     * @param array $urls An array of URLs from which to choose.
     *
     * @return string A single thumbnail URL.
     */
    protected function pickThumbnailUrl($urls)
    {
        if (! is_array($urls) || sizeof($urls) == 0) {

            return '';
        }

        $random = $this->_context->get(tubepress_core_html_gallery_api_Constants::OPTION_RANDOM_THUMBS);

        if ($random) {

            return $urls[array_rand($urls)];

        } else {

            return $urls[0];
        }
    }
}