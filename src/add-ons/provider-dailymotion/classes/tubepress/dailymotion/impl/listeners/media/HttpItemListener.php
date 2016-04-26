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
class tubepress_dailymotion_impl_listeners_media_HttpItemListener
{
    /**
     * @var tubepress_api_media_AttributeFormatterInterface
     */
    private $_attributeFormatter;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_array_ArrayReaderInterface
     */
    private $_arrayReader;

    public function __construct(tubepress_api_media_AttributeFormatterInterface $attributeFormatter,
                                tubepress_api_options_ContextInterface          $context,
                                tubepress_api_url_UrlFactoryInterface           $urlFactory,
                                tubepress_api_array_ArrayReaderInterface        $arrayReader)
    {
        $this->_attributeFormatter = $attributeFormatter;
        $this->_context            = $context;
        $this->_urlFactory         = $urlFactory;
        $this->_arrayReader        = $arrayReader;
    }

    public function onHttpItem(tubepress_api_event_EventInterface $event)
    {
        $mediaItem    = $event->getSubject();
        $attributeMap = $this->_buildAttributeMap($event);

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
    private function _buildAttributeMap(tubepress_api_event_EventInterface $event)
    {
        $toReturn = array();
        $feed     = $event->getArgument('feedAsArray');
        $index    = $event->getArgument('zeroBasedIndex'); // starts at 0

        $this->_applyTitle($toReturn, $feed, $index);
        $this->_applyDuration($toReturn, $feed, $index);
        $this->_applyAuthor($toReturn, $feed, $index);
        $this->_applyKeywords($toReturn, $feed, $index);
        $this->_applyHomeUrl($toReturn, $feed, $index);
        $this->_applyCategory($toReturn, $feed, $index);
        $this->_applyViewCount($toReturn, $feed, $index);
        $this->_applyTimePublished($toReturn, $feed, $index);
        $this->_applyDescription($toReturn, $feed, $index);
        $this->_applyThumbnail($toReturn, $feed, $index);

        return $toReturn;
    }

    private function _applyTitle(array &$toReturn, array $json, $index)
    {
        $title = $this->_relativeQueryAsString($json, $index, 'title');

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_TITLE] = $title;
    }

    private function _applyDuration(array &$toReturn, array $json, $index)
    {
        $seconds = $this->_relativeQueryAsString($json, $index, 'duration');

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS] = intval($seconds);
    }

    private function _applyAuthor(array &$toReturn, array $json, $index)
    {
        $id          = $this->_relativeQueryAsString($json, $index, 'owner\.id');
        $displayName = $this->_relativeQueryAsString($json, $index, 'owner\.screenname');
        $url         = $this->_relativeQueryAsString($json, $index, 'owner\.url');

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID]      = $id;
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME] = $displayName;
        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_URL]          = $url;
    }

    private function _applyKeywords(array &$toReturn, array $json, $index)
    {
        $tags = $this->_relativeQueryAsArray($json, $index, 'tags');

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY] = $tags;
    }

    private function _applyHomeUrl(array &$toReturn, array $json, $index)
    {
        $url = $this->_relativeQueryAsString($json, $index, 'url');

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL] = $url;
    }

    private function _applyCategory(array &$toReturn, array $json, $index)
    {
        $category = $this->_relativeQueryAsString($json, $index, 'channel\.name');

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME] = $category;
    }

    private function _applyViewCount(array &$toReturn, array $json, $index)
    {
        $viewCountRaw = $this->_relativeQueryAsString($json, $index, 'views_total');

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT] = intval($viewCountRaw);
    }

    private function _applyTimePublished(array &$toReturn, array $json, $index)
    {
        $time = $this->_relativeQueryAsString($json, $index, 'created_time');

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME] = $time;
    }

    private function _applyDescription(array &$toReturn, array $json, $index)
    {
        $description = $this->_relativeQueryAsString($json, $index, 'description');

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION] = $description;
    }

    private function _applyThumbnail(array &$toReturn, array $json, $index)
    {
        $preferredSize = $this->_context->get(tubepress_dailymotion_api_Constants::OPTION_THUMB_SIZE);
        $preferredSize = str_replace('px', '', $preferredSize);
        $url           = $this->_relativeQueryAsString($json, $index, sprintf('thumbnail_%s_url', $preferredSize));

        if (!$url) {

            $url = $this->_relativeQueryAsString($json, $index, 'thumbnail_url');
        }

        $toReturn[tubepress_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL] = $url;
    }

    /**
     * @param $mediaItem
     */
    private function _formatAttributes($mediaItem)
    {
        $this->_attributeFormatter->formatNumberAttribute($mediaItem,
            tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
            tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT_FORMATTED, 0);

        $this->_attributeFormatter->truncateStringAttribute($mediaItem,
            tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
            tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
            tubepress_api_options_Names::META_DESC_LIMIT);

        $this->_attributeFormatter->formatDurationAttribute($mediaItem,
            tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS,
            tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED);

        $this->_attributeFormatter->formatDateAttribute($mediaItem,
            tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME,
            tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED);

        $this->_attributeFormatter->implodeArrayAttribute($mediaItem,
            tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY,
            tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED, ', ');
    }

    private function _relativeQueryAsString(array $json, $index, $query, $default = '')
    {
        $items = $this->_arrayReader->getAsArray($json, 'list');

        if (!isset($items[$index])) {

            return $default;
        }

        $item = $items[$index];

        return $this->_arrayReader->getAsString($item, $query, $default);
    }

    private function _relativeQueryAsArray(array $json, $index, $query, $default = array())
    {
        $items = $this->_arrayReader->getAsArray($json, 'list');

        if (!isset($items[$index])) {

            return $default;
        }

        $item = $items[$index];

        return $this->_arrayReader->getAsArray($item, $query, $default);
    }
}
