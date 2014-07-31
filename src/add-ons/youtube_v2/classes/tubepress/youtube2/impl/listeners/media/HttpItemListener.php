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
class tubepress_youtube2_impl_listeners_media_HttpItemListener
{
    /**
     * @var tubepress_app_api_media_AttributeFormatterInterface
     */
    private $_attributeFormatter;

    /**
     * @var tubepress_lib_api_util_TimeUtilsInterface
     */
    private $_timeUtils;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_api_media_AttributeFormatterInterface $attributeFormatter,
                                tubepress_lib_api_util_TimeUtilsInterface           $timeUtils,
                                tubepress_app_api_options_ContextInterface          $context)
    {
        $this->_attributeFormatter = $attributeFormatter;
        $this->_timeUtils          = $timeUtils;
        $this->_context            = $context;
    }

    public function onHttpItem(tubepress_lib_api_event_EventInterface $event)
    {
        $mediaItem = $event->getSubject();
        $args      = $event->getArguments();

        if (!isset($args['api']) || $args['api'] !== 'youtube_v2') {

            return;
        }

        $attributeMap = $this->_buildAttributeMap($event);

        foreach ($attributeMap as $attributeName => $attributeValue) {

            $mediaItem->setAttribute($attributeName, $attributeValue);
        }

        $this->_formatAttributes($mediaItem);
    }

    /**
     * Build a map of attribute names => attribute values for the video construction event.
     *
     * @param tubepress_lib_api_event_EventInterface $event The video construction event.
     *
     * @return array An associative array of attribute names => attribute values
     */
    private function _buildAttributeMap(tubepress_lib_api_event_EventInterface $event)
    {
        $toReturn = array();
        $xpath    = $event->getArgument('xpath');
        $index    = $event->getArgument('zeroBasedIndex');

        /* Author */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME] =
            $this->_relativeQuery($xpath, $index, 'atom:author/atom:name')->item(0)->nodeValue;
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID] =
            $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME];

        /* Category */
        /** @noinspection PhpUndefinedMethodInspection */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME] =
            trim($this->_relativeQuery($xpath, $index, 'media:group/media:category')->item(0)->getAttribute('label'));

        /* Description */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION] =
            $this->_relativeQuery($xpath, $index, 'media:group/media:description')->item(0)->nodeValue;

        /* Duration */
        /** @noinspection PhpUndefinedMethodInspection */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS] =
            $this->_relativeQuery($xpath, $index, 'media:group/yt:duration')->item(0)->getAttribute('seconds');

        /* Home URL */
        /** @noinspection PhpUndefinedMethodInspection */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_HOME_URL] =
            $this->_relativeQuery($xpath, $index, "atom:link[@rel='alternate']")->item(0)->getAttribute('href');

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_ID] =
            $this->_relativeQuery($xpath, $index, 'media:group/yt:videoid')->item(0)->nodeValue;

        /* Keywords. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY] = array();

        /* Rating */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_AVERAGE] = $this->_getRatingAverage($xpath, $index);
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_COUNT]   = $this->_getRatingCount($xpath, $index);

        /* Thumbnail. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL] =
            $this->_pickThumbnailUrl($this->_getThumbnailUrls($xpath, $index));

        /* Time published. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME] =
            $this->_getTimePublishedUnixTime($xpath, $index);

        /* Title. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE] =
            $this->_relativeQuery($xpath, $index, 'atom:title')->item(0)->nodeValue;

        /* Views. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT] =
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
    private function _pickThumbnailUrl($urls)
    {
        if (! is_array($urls) || sizeof($urls) == 0) {

            return '';
        }

        $random = $this->_context->get(tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS);

        if ($random) {

            return $urls[array_rand($urls)];

        } else {

            return $urls[0];
        }
    }

    /**
     * @param $mediaItem
     */
    private function _formatAttributes($mediaItem)
    {
        $this->_attributeFormatter->formatNumberAttribute($mediaItem, tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_COUNT, tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_COUNT, 0);

        $this->_attributeFormatter->formatNumberAttribute($mediaItem, tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, 0);

        $this->_attributeFormatter->formatNumberAttribute($mediaItem, tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_AVERAGE, tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_AVERAGE, 2);

        $this->_attributeFormatter->truncateStringAttribute($mediaItem, tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_app_api_options_Names::META_DESC_LIMIT);

        $this->_attributeFormatter->formatDurationAttribute($mediaItem, tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS, tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED);

        $this->_attributeFormatter->formatDateAttribute($mediaItem, tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED);

        $this->_attributeFormatter->implodeArrayAttribute($mediaItem, tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY, tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED, ', ');
    }
}