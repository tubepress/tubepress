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
class tubepress_core_theme_impl_listeners_options_AcceptableValues
{
    /**
     * @var tubepress_core_theme_api_ThemeLibraryInterface
     */
    private $_themeLibrary;

    public function __construct(tubepress_core_theme_api_ThemeLibraryInterface $themeLibrary)
    {
        $this->_themeLibrary = $themeLibrary;
    }

    public function onAcceptableValues(tubepress_core_event_api_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $themeNames = $this->_themeLibrary->getMapOfAllThemeNamesToTitles();

        ksort($themeNames);

        $toSet = array_merge($current, $themeNames);

        $event->setSubject($toSet);
    }
}