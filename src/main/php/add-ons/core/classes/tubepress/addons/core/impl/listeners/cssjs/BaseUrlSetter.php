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
class tubepress_addons_core_impl_listeners_cssjs_BaseUrlSetter
{
    public function onJsConfig(tubepress_api_event_EventInterface $event)
    {
        /**
         * @var $config array
         */
        $config              = $event->getSubject();
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        if (!isset($config['urls'])) {

            $config['urls'] = array();
        }

        $config['urls']['base'] = $environmentDetector->getBaseUrl();
        $config['urls']['usr']  = $environmentDetector->getUserContentUrl();

        $event->setSubject($config);
    }
}