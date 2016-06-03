<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_vimeo3_impl_listeners_media_HttpItemListener
{
    /**
     * @var tubepress_api_media_AttributeFormatterInterface
     */
    private $_attributeFormatter;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_api_media_AttributeFormatterInterface $attributeFormatter,
                                tubepress_api_options_ContextInterface          $context)
    {
        $this->_attributeFormatter = $attributeFormatter;
        $this->_context            = $context;
    }

    public function onHttpItem(tubepress_api_event_EventInterface $event)
    {
        $mediaItem    = $event->getSubject();
        $attributeMap = $this->buildAttributeMap($event);

        foreach ($attributeMap as $attributeName => $attributeValue) {

            $mediaItem->setAttribute($attributeName, $attributeValue);
        }

        $this->_formatAttributes($mediaItem);
    }

    /**
     * Build a map of attribute names => attribute values for the video construction event.
     *
     * @param tubepress_api_event_EventInterface $event The video construction event.
     *
     * @return array An associative array of attribute names => attribute values
     */
    protected function buildAttributeMap(tubepress_api_event_EventInterface $event)
    {
        $toReturn       = array();
        $index          = $event->getArgument('zeroBasedIndex');
        $mediaItemArray = $event->getArgument('videoArray');

        /* Author */
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME] =
            $mediaItemArray[$index]['user']['name'];
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID] =
            substr($mediaItemArray[$index]['user']['uri'], strlen('/users/'));
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_URL] =
            $mediaItemArray[$index]['user']['link'];

        /* Description */
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION] =
            $mediaItemArray[$index]['description'];

        /* Duration */
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS] =
            $mediaItemArray[$index]['duration'];

        /* Home URL */
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL] =
            'https://vimeo.com/' . substr($mediaItemArray[$index]['uri'], strlen('/videos/'));

        /* Keywords */
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY] =
            $this->_gatherArrayOfContent($mediaItemArray[$index], 'tags', 'tag');

        /* Likes. */
        if (isset($mediaItemArray[$index]['stats']['likes'])) {

            $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT] =
                $mediaItemArray[$index]['stats']['likes'];
        }

        /* Thumbnail. */
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL] =
            $this->_getThumbnailUrl($mediaItemArray, $index);

        /* Time published. Vimeo dates are in US Eastern Time.*/
        $reset = date_default_timezone_get();
        date_default_timezone_set('America/New_York');
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME] =
            strtotime($mediaItemArray[$index]['created_time']);
        date_default_timezone_set($reset);

        /* Title. */
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_TITLE] =
            $mediaItemArray[$index]['name'];

        /* Views. */
        if (isset($mediaItemArray[$index]['stats']['plays'])) {
            $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT] =
                $mediaItemArray[$index]['stats']['plays'];
        }

        /* ID */
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_ID] =
            substr($mediaItemArray[$index]['uri'], strlen('/videos/'));

        return $toReturn;
    }

    private function _getThumbnailUrl($mediaItemArray, $index)
    {
        if (isset($mediaItemArray[$index]['pictures'])) {

            return $mediaItemArray[$index]['pictures']['sizes'][0]['link'];
        }

        $raw = $this->_gatherArrayOfContent($mediaItemArray[$index], 'thumbnails', 'thumbnail');

        return $raw[0];
    }

    private function _gatherArrayOfContent($node, $firstDimension, $secondDimension)
    {
        $results = array();

        if (isset($node->$firstDimension) && is_array($node->$firstDimension->$secondDimension)) {

            foreach ($node->$firstDimension->$secondDimension as $item) {

                $results[] = $item->_content;
            }
        }

        return $results;
    }

    private function _formatAttributes(tubepress_api_media_MediaItem $item)
    {
        $this->_attributeFormatter->formatNumberAttribute($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT, tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT, 0);
        $this->_attributeFormatter->formatNumberAttribute($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, 0);
        $this->_attributeFormatter->truncateStringAttribute($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_api_options_Names::META_DESC_LIMIT);
        $this->_attributeFormatter->formatDurationAttribute($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS, tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED);
        $this->_attributeFormatter->formatDateAttribute($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED);
        $this->_attributeFormatter->implodeArrayAttribute($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY, tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED, ', ');
    }
}
