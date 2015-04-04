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
        $feed    = $event->getArgument('feed');
        $index    = $event->getArgument('zeroBasedIndex'); // starts at 0

        // extract current item
        $item = $feed['items'][$index];

        /* Description */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION] =
            $item['snippet']['description'];

        /* Title. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE] =
            $item['snippet']['title'];

        /* Thumbnail. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL] =
            $item['snippet']['thumbnails']['default']['url'];

        /* Video id. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_ID] =
            $this->_getVideoId($item);

        /* Author info */    
        $this->_findAndApplyVideoAuthor($toReturn, $item);

        /* Category */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME] =
            isset($item['snippet']['categoryName'])? $item['snippet']['categoryName']: '' ;

        
        /* Duration */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS] = 
            isset($item['contentDetails']['duration'])?$this->_calculateDurationFromTimespan($item['contentDetails']['duration']): 0;

        /* Time published. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME] =
            $this->_getTimePublishedUnixTime($item['snippet']['publishedAt']); 

        /* Views. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT] =
            isset($item['statistics']['viewCount'])? $item['statistics']['viewCount']: 0;

        /* Keywords. */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY] = 
            isset($item['snippet']['tags'])? $item['snippet']['tags']: array();

        /* Home URL */
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_HOME_URL] =
            'https://www.youtube.com/watch?feature=player_embedded&v='. $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_ID];

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_COUNT]   = 
            isset($item['statistics']['likeCount'])? $item['statistics']['likeCount']: 0;


        return $toReturn;

    }

    // TODO: this is a duplicate of FeedHandler->_getVideoId()
    private  function _getVideoId($item)
    {
        if (isset($item['snippet']['resourceId']['videoId']))
        {
            return $item['snippet']['resourceId']['videoId'];
        }    
        if (isset($item['id']['videoId']))
        {
            return $item['id']['videoId'];
        }           
        if (isset($item['id']))
        {
            return $item['id'];
        }   
        return false;
    }

    private function _findAndApplyVideoAuthor(array &$toReturn, $item)
    {

        $channelId = isset($item['snippet']['channelId'])? $item['snippet']['channelId']: $item['id'];
        $channelTitle = isset($item['snippet']['channelTitle'])? $item['snippet']['channelTitle']: $item['snippet']['title'];

        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID]      = $channelId;
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME] = $channelTitle;
        $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_URL]          =
            sprintf('https://www.youtube.com/user
                /%s', $toReturn[tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME]);

    }

    private function _calculateDurationFromTimespan($timespan)
    {
        return $this->_get_duration_seconds($timespan);

        // if we ever stop supporting < 5.3 ...
        // return date_create('@0')->add(new DateInterval($item['contentDetails']['duration']))->getTimestamp();
    }    
            

    private function _parse_duration($iso_duration, $allow_negative = true){
        // Parse duration parts
        $matches = array();
        preg_match('/^(-|)?P([0-9]+Y|)?([0-9]+M|)?([0-9]+D|)?T?([0-9]+H|)?([0-9]+M|)?([0-9]+S|)?$/', $iso_duration, $matches);
        if(!empty($matches)){       
            // Strip all but digits and -
            foreach($matches as &$match){
                $match = preg_replace('/((?!([0-9]|-)).)*/', '', $match);
            }   
            // Fetch min/plus symbol
            $result['symbol'] = ($matches[1] == '-') ? $matches[1] : '+'; // May be needed for actions outside this function.
            // Fetch duration parts
            $m = ($allow_negative) ? $matches[1] : '';
            $result['year']   = intval($m.$matches[2]);
            $result['month']  = intval($m.$matches[3]);
            $result['day']    = intval($m.$matches[4]);
            $result['hour']   = intval($m.$matches[5]);
            $result['minute'] = intval($m.$matches[6]);
            $result['second'] = intval($m.$matches[7]);     
            return $result; 
        }
        else{
            return false;
        }
    }

    private function _get_duration_seconds($iso_duration){
        // Get duration parts
        $duration = $this->_parse_duration($iso_duration, false);
        if($duration){
            extract($duration);
            $dparam  = $symbol; // plus/min symbol
            $dparam .= (!empty($year)) ? $year . 'Year' : '';
            $dparam .= (!empty($month)) ? $month . 'Month' : '';
            $dparam .= (!empty($day)) ? $day . 'Day' : '';
            $dparam .= (!empty($hour)) ? $hour . 'Hour' : '';
            $dparam .= (!empty($minute)) ? $minute . 'Minute' : '';
            $dparam .= (!empty($second)) ? $second . 'Second' : '';
            $date = '19700101UTC';
            return strtotime($date.$dparam) - strtotime($date);
        }
        else{
            // Not a valid iso duration
            return false;
        }
    }

    private function _getTimePublishedUnixTime($rawTime)
    {
        return $this->_timeUtils->rfc3339toUnixTime($rawTime);
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