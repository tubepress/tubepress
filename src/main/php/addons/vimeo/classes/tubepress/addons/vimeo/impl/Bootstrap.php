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
 * Registers a few extensions to allow TubePress to work with Vimeo.
 */
final class tubepress_addons_vimeo_impl_Bootstrap
{
    public function boot()
    {
        self::_registerEventListeners();
    }

    private static function _registerEventListeners()
    {
        /**
         * @var $eventDispatcher tubepress_api_event_EventDispatcherInterface
         */
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $eventDispatcher->addListenerService(

            tubepress_api_const_event_EventNames::BOOT_COMPLETE,
            array('tubepress_addons_vimeo_impl_listeners_boot_VimeoOptionsRegistrar', 'onBoot')
        );

        $eventDispatcher->addListenerService(

            tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION,
            array('tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener', 'onVideoConstruction')
        );

        $eventDispatcher->addListenerService(

            ehough_shortstop_api_Events::RESPONSE,
            array('tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener', 'onResponse')
        );
    }
}