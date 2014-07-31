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
class tubepress_app_impl_listeners_options_values_PlayerImpl
{
    public function onAcceptableValues(tubepress_lib_api_event_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $event->setSubject(array_merge(array(
            tubepress_app_api_options_AcceptableValues::EMBEDDED_IMPL_PROVIDER_BASED => 'Provider default',  //>(translatable)<
        ), $current));
    }
}