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
 * Builds Vimeo videos.
 */
class tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener
{
    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_options_api_ContextInterface $context)
    {
        $this->_context   = $context;
    }

    public function onVideoConstruction(tubepress_lib_event_api_EventInterface $event)
    {
        $mediaItem = $event->getSubject();

        $mediaProvider = $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_PROVIDER);

        /*
         * Short circuit for videos belonging to someone else.
         */
        if ($mediaProvider->getName() !== 'vimeo') {

            return;
        }

        $attributeMap = $this->buildAttributeMap($event);

        foreach ($attributeMap as $attributeName => $attributeValue) {

            $mediaItem->setAttribute($attributeName, $attributeValue);
        }
    }

    /**
     * Build a map of attribute names => attribute values for the video construction event.
     *
     * @param tubepress_lib_event_api_EventInterface $event The video construction event.
     *
     * @return array An associative array of attribute names => attribute values
     */
    protected function buildAttributeMap(tubepress_lib_event_api_EventInterface $event)
    {
        $toReturn       = array();
        $index          = $event->getArgument('zeroBasedFeedIndex');
        $mediaItemArray = $event->getArgument('videoArray');

        /* Author */
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME] =
            $mediaItemArray[$index]->owner->display_name;
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_AUTHOR_USER_ID] =
            $mediaItemArray[$index]->owner->username;

        /* Description */
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_DESCRIPTION] =
            $mediaItemArray[$index]->description;

        /* Duration */
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_DURATION_SECONDS] =
            $mediaItemArray[$index]->duration;

        /* Home URL */
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_HOME_URL] =
            'http://vimeo.com/' . $mediaItemArray[$index]->id;

        /* Keywords */
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_KEYWORD_ARRAY] =
            $this->_gatherArrayOfContent($mediaItemArray[$index], 'tags', 'tag');

        /* Likes. */
        if (isset($mediaItemArray[$index]->number_of_likes)) {

            $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_LIKES_COUNT] =
                $mediaItemArray[$index]->number_of_likes;
        }

        /* Thumbnail. */
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_THUMBNAIL_URL] =
            $this->_getThumbnailUrl($mediaItemArray, $index);

        /* Time published. Vimeo dates are in US Eastern Time.*/
        $reset = date_default_timezone_get();
        date_default_timezone_set('America/New_York');
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME] =
            strtotime($mediaItemArray[$index]->upload_date);
        date_default_timezone_set($reset);

        /* Title. */
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE] =
            $mediaItemArray[$index]->title;

        /* Views. */
        if (isset($mediaItemArray[$index]->number_of_plays)) {
            $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT] =
                $mediaItemArray[$index]->number_of_plays;
        }

        /* ID */
        $toReturn[tubepress_app_media_item_api_Constants::ATTRIBUTE_ID] =
            $mediaItemArray[$index]->id;

        return $toReturn;
    }

    private function _getThumbnailUrl($mediaItemArray, $index)
    {
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
}