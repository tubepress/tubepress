<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_youtube3_impl_listeners_media_HttpItemListener
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

    /**
     * @var tubepress_youtube3_impl_ApiUtility
     */
    private $_apiUtility;

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_lib_api_array_ArrayReaderInterface
     */
    private $_arrayReader;

    public function __construct(tubepress_app_api_media_AttributeFormatterInterface $attributeFormatter,
                                tubepress_lib_api_util_TimeUtilsInterface           $timeUtils,
                                tubepress_app_api_options_ContextInterface          $context,
                                tubepress_youtube3_impl_ApiUtility                  $apiUtility,
                                tubepress_platform_api_url_UrlFactoryInterface      $urlFactory,
                                tubepress_lib_api_array_ArrayReaderInterface        $arrayReader)
    {
        $this->_attributeFormatter = $attributeFormatter;
        $this->_timeUtils          = $timeUtils;
        $this->_context            = $context;
        $this->_apiUtility         = $apiUtility;
        $this->_urlFactory         = $urlFactory;
        $this->_arrayReader        = $arrayReader;
    }

    public function onHttpItem(tubepress_lib_api_event_EventInterface $event)
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
     * @param tubepress_lib_api_event_EventInterface $event The video construction event.
     *
     * @return array An associative array of attribute names => attribute values
     */
    private function _buildAttributeMap(tubepress_lib_api_event_EventInterface $event)
    {
        $toReturn = array();
        $metadata = $event->getArgument('metadataAsArray');
        $index    = $event->getArgument('zeroBasedIndex'); // starts at 0

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_UPLOADED)) {

            $this->_applyTimePublished($toReturn, $metadata, $index);
        };

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_AUTHOR)) {

            $this->_applyAuthor($toReturn, $metadata, $index);
        }

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_TITLE)) {

            $this->_applyTitle($toReturn, $metadata, $index);
        }

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_DESCRIPTION)) {

            $this->_applyDescription($toReturn, $metadata, $index);
        }

        $this->_applyThumbnail($toReturn, $metadata, $index);

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_KEYWORDS)) {

            $this->_applyKeywords($toReturn, $metadata, $index);
        }

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_LENGTH)) {

            $this->_applyDuration($toReturn, $metadata, $index);
        }

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_CATEGORY)) {

            $this->_applyCategory($toReturn, $metadata, $index);
        }

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_URL)) {

            $this->_applyHomeUrl($event->getSubject(), $toReturn);
        }

        if ($this->_context->get(tubepress_app_api_options_Names::META_DISPLAY_VIEWS)) {

            $this->_applyViewCount($toReturn, $metadata, $index);
        }

        if ($this->_context->get(tubepress_youtube3_api_Constants::OPTION_META_COUNT_LIKES)) {

            $this->_applyLikes($toReturn, $metadata, $index);
        }

        if ($this->_context->get(tubepress_youtube3_api_Constants::OPTION_META_COUNT_DISLIKES)) {

            $this->_applyDislikes($toReturn, $metadata, $index);
        }

        if ($this->_context->get(tubepress_youtube3_api_Constants::OPTION_META_COUNT_FAVORITES)) {

            $this->_applyFavorites($toReturn, $metadata, $index);
        }

        if ($this->_context->get(tubepress_youtube3_api_Constants::OPTION_META_COUNT_COMMENTS)) {

            $this->_applyComments($toReturn, $metadata, $index);
        }

        return $toReturn;
    }

    //https://developers.google.com/youtube/v3/docs/videos#snippet.publishedAt
    private function _applyTimePublished(array &$toReturn, array $json, $index)
    {
        $value = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET,
            'publishedAt'
        ));

        if ($value !== '') {

            $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME] =
                $this->_timeUtils->rfc3339toUnixTime($value);;
        }
    }

    //https://developers.google.com/youtube/v3/docs/videos#snippet.channelId
    //https://developers.google.com/youtube/v3/docs/videos#snippet.channelTitle
    private function _applyAuthor(array &$toReturn, array $json, $index)
    {
        $channelId = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET_CHANNEL_ID
        ));

        $channelTitle = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET_CHANNEL_TITLE
        ));

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID]      = $channelId;
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME] = $channelTitle;
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_URL]          =
            sprintf('https://www.youtube.com/channel/%s', $channelId);
    }

    private function _applyTitle(array &$toReturn, array $json, $index)
    {
        $title = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET_TITLE
        ));

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE] = $title;
    }

    private function _applyDescription(array &$toReturn, array $json, $index)
    {
        $description = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET_DESCRIPTION
        ));

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION] = nl2br($description);
    }

    private function _applyThumbnail(array &$toReturn, array $json, $index)
    {
        $thumb = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET_THUMBS,
            'default',
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET_THUMBS_URL
        ));

        if ($this->_context->get(tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS)) {

            $choices = array('1', '2', '3');
            $new     = $choices[array_rand($choices)];

            $final = str_replace('default', $new, $thumb);

        } else {

            $final = $thumb;
        }

        $final = str_replace('s://', '://', $final);

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL] = $final;
    }

    private function _applyKeywords(array &$toReturn, array $json, $index)
    {
        $tags = $this->_relativeQueryAsArray($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET_TAGS
        ));

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY] = $tags;
    }

    private function _applyDuration(array &$toReturn, array $json, $index)
    {
        $rawDuration = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_CONTENT_DETAILS,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_CONTENT_DETAILS_DURATION
        ));

        $strippedDuration = str_replace('PT', '', $rawDuration);

        if (strpos($strippedDuration, 'M') === false) {

            //only seconds
            $minutes    = 0;
            $rawSeconds = str_replace('S', '', $strippedDuration);

        } else {

            $explosion  = explode('M', $strippedDuration);
            $rawMinutes = $explosion[0];
            $rawSeconds = str_replace('S', '', $explosion[1]);
            $minutes    = intval($rawMinutes);
        }

        $seconds = intval($rawSeconds);

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS] = ((60 * $minutes) + $seconds);
    }

    private function _applyCategory(array &$toReturn, array $json, $index)
    {
        $categoryId = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET_CATEGORY_ID
        ));

        $categoryUrl = $this->_urlFactory->fromString(tubepress_youtube3_impl_ApiUtility::YOUTUBE_API_URL);
        $categoryUrl->addPath(tubepress_youtube3_impl_ApiUtility::PATH_VIDEO_CATEGORIES);
        $categoryUrl->getQuery()->set(tubepress_youtube3_impl_ApiUtility::QUERY_PART, tubepress_youtube3_impl_ApiUtility::PART_SNIPPET)
            ->set(tubepress_youtube3_impl_ApiUtility::QUERY_CATEGORIES_ID, $categoryId);

        $response      = $this->_apiUtility->getDecodedApiResponse($categoryUrl);
        $responseItems = $this->_arrayReader->getAsArray($response, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS);
        $firstItem     = $responseItems[0];

        $categoryTitle = $this->_arrayReader->getAsString($firstItem, sprintf('%s.%s',

            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_SNIPPET,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_SNIPPET_TITLE
        ));

        if ($categoryTitle !== '') {

            $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME] = $categoryTitle;
        }
    }

    private function _applyHomeUrl(tubepress_app_api_media_MediaItem $item, array &$toReturn)
    {
        $id      = $item->getId();
        $homeUrl = sprintf('https://youtu.be/%s', $id);

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_HOME_URL] = $homeUrl;
    }

    private function _applyViewCount(array &$toReturn, array $json, $index)
    {
        $viewCountRaw = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS_VIEWS
        ));

        if ($viewCountRaw !== '') {

            $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT] = $viewCountRaw;
        }
    }

    private function _applyLikes(array &$toReturn, array $json, $index)
    {
        $count = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS_LIKES
        ));

        if ($count !== '') {

            $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT] = $count;
        }
    }

    private function _applyDislikes(array &$toReturn, array $json, $index)
    {
        $count = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS_DISLIKES
        ));

        if ($count !== '') {

            $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_COUNT_DISLIKES] = $count;
        }
    }

    private function _applyFavorites(array &$toReturn, array $json, $index)
    {
        $count = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS_FAVORITES
        ));

        if ($count !== '') {

            $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_COUNT_FAVORITED] = $count;
        }
    }

    private function _applyComments(array &$toReturn, array $json, $index)
    {
        $count = $this->_relativeQueryAsString($json, $index, array(

            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS,
            tubepress_youtube3_impl_ApiUtility::RESOURCE_VIDEO_STATS_COMMENTS
        ));

        if ($count !== '') {

            $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT] = $count;
        }
    }

    /**
     * @param $mediaItem
     */
    private function _formatAttributes($mediaItem)
    {
        $this->_attributeFormatter->formatNumberAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_COUNT_FAVORITED,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_COUNT_FAVORITED_FORMATTED, 0);

        $this->_attributeFormatter->formatNumberAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT_FORMATTED, 0);

        //keep this for legacy purposes
        $this->_attributeFormatter->formatNumberAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, 0);

        $this->_attributeFormatter->formatNumberAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT_FORMATTED, 0);

        $this->_attributeFormatter->formatNumberAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT_FORMATTED, 0);

        $this->_attributeFormatter->formatNumberAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_COUNT_DISLIKES,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_COUNT_DISLIKES_FORMATTED, 0);

        $this->_attributeFormatter->truncateStringAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
            tubepress_app_api_options_Names::META_DESC_LIMIT);

        $this->_attributeFormatter->formatDurationAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED);

        $this->_attributeFormatter->formatDateAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED);

        $this->_attributeFormatter->implodeArrayAttribute($mediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED, ', ');
    }

    private function _relativeQueryAsString(array $json, $index, array $query, $default = '')
    {
        $items = $this->_arrayReader->getAsArray($json, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS);

        if (!isset($items[$index])) {

            return $default;
        }

        $item = $items[$index];

        return $this->_arrayReader->getAsString($item, implode('.', $query), $default);
    }

    private function _relativeQueryAsArray(array $json, $index, array $query, $default = array())
    {
        $items = $this->_arrayReader->getAsArray($json, tubepress_youtube3_impl_ApiUtility::RESPONSE_ITEMS);

        if (!isset($items[$index])) {

            return $default;
        }

        $item = $items[$index];

        return $this->_arrayReader->getAsArray($item, implode('.', $query), $default);
    }
}