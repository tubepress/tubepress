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
class tubepress_embedplus_impl_listeners_js_JsOptionsListener
{
    private static $_OPTIONS = 'options';

    /**
     *
     */
    public function onGalleryInitJs(tubepress_lib_event_api_EventInterface $event)
    {
        $args           = $event->getSubject();
        $implementation = $args[self::$_OPTIONS][tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL];

        if ($implementation !== 'embedplus') {

            return;
        }

        $existingHeight = $args[self::$_OPTIONS][tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT];
        $newHeight      = intval($existingHeight) + 30;
        $args[self::$_OPTIONS][tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT] = $newHeight;

        $event->setSubject($args);
    }
}