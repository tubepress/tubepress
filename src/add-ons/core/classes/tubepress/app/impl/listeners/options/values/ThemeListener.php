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
 *
 */
class tubepress_app_impl_listeners_options_values_ThemeListener
{
    /**
     * @var tubepress_platform_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    public function __construct(tubepress_platform_api_contrib_RegistryInterface $themeRegistry)
    {
        $this->_themeRegistry = $themeRegistry;
    }

    public function onAcceptableValues(tubepress_lib_api_event_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $result    = array();
        $allThemes = $this->_themeRegistry->getAll();

        foreach ($allThemes as $theme) {

            $result[$theme->getName()] = $theme->getTitle();
        }

        ksort($result);

        $toSet = array_merge($current, $result);

        $event->setSubject($toSet);
    }
}