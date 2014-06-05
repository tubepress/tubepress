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
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_core_options_api_ContextInterface $context)
    {
        $this->_context   = $context;
    }

    public function onVideoConstruction(tubepress_core_event_api_EventInterface $event)
    {
        $video = $event->getSubject();

        $mediaProvider = $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER);

        /*
         * Short circuit for videos belonging to someone else.
         */
        if ($mediaProvider->getName() !== 'vimeo') {

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
    protected final function buildAttributeMap(tubepress_core_event_api_EventInterface $event)
    {
        $toReturn   = array();
        $index      = $event->getArgument('zeroBasedFeedIndex');
        $videoArray = $event->getArgument('videoArray');

        /* Author */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME] =
            $videoArray[$index]->owner->display_name;
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_USER_ID] =
            $videoArray[$index]->owner->username;

        /* Description */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION] =
            $videoArray[$index]->description;

        /* Duration */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_SECONDS] =
            $videoArray[$index]->duration;

        /* Home URL */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_HOME_URL] =
            'http://vimeo.com/' . $videoArray[$index]->id;

        /* Keywords */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_KEYWORD_ARRAY] =
            $this->_gatherArrayOfContent($videoArray[$index], 'tags', 'tag');

        /* Likes. */
        if (isset($videoArray[$index]->number_of_likes)) {

            $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_LIKES_COUNT] =
                $videoArray[$index]->number_of_likes;
        }

        /* Thumbnail. */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_THUMBNAIL_URL] =
            $this->_getThumbnailUrl($videoArray, $index);

        /* Time published. Vimeo dates are in US Eastern Time.*/
        $reset = date_default_timezone_get();
        date_default_timezone_set('America/New_York');
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME] =
            strtotime($videoArray[$index]->upload_date);
        date_default_timezone_set($reset);

        /* Title. */
        $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE] =
            $videoArray[$index]->title;

        /* Views. */
        if (isset($videoArray[$index]->number_of_plays)) {
            $toReturn[tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT] =
                $videoArray[$index]->number_of_plays;
        }

        return $toReturn;
    }

    private function _getThumbnailUrl($videoArray, $index)
    {
        $raw = $this->_gatherArrayOfContent($videoArray[$index], 'thumbnails', 'thumbnail');

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