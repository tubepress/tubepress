<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Allows TubePress to work with YouTube.
 */
class tubepress_addons_youtube_impl_Bootstrap
{
    public static function init()
    {
        self::_registerEventListeners();
    }

    private static function _registerEventListeners()
    {
        /**
         * @var $eventDispatcher ehough_tickertape_ContainerAwareEventDispatcher
         */
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $eventDispatcher->addListenerService(

            tubepress_api_const_event_EventNames::BOOT_COMPLETE,
            array('tubepress_addons_youtube_impl_listeners_boot_YouTubeOptionsRegistrar', 'onBoot')
        );

        $eventDispatcher->addListenerService(

            tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION,
            array('tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener', 'onVideoConstruction')
        );

        $eventDispatcher->addListenerService(

            ehough_shortstop_api_Events::RESPONSE,
            array('tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener', 'onResponse')
        );
    }
}