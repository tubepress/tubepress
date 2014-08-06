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
 *
 */
class tubepress_youtube2_impl_listeners_player_PlayerListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_api_options_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onPlayerLocationAcceptableValues(tubepress_lib_api_event_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $current = array_merge($current, array('youtube' =>
            'from the video\'s original YouTube page'));    //>(translatable)<'))

        $event->setSubject($current);
    }

    public function onNewMediaItem(tubepress_lib_api_event_EventInterface $event)
    {
        if (!$this->_context->get(tubepress_app_api_options_Names::PLAYER_LOCATION) === 'youtube') {

            return;
        }

        /**
         * @var $mediaItem tubepress_app_api_media_MediaItem
         */
        $mediaItem = $event->getSubject();

        $mediaItem->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_INVOKING_ANCHOR_REL,    'external nofollow');
        $mediaItem->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_INVOKING_ANCHOR_TARGET, '_blank');
        $mediaItem->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_INVOKING_ANCHOR_HREF,
            sprintf('https://youtube.com/watch?v=%s', $mediaItem->getId()));
    }
}