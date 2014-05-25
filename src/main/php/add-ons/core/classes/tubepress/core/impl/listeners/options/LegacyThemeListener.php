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
class tubepress_core_impl_listeners_options_LegacyThemeListener
{
    private static $_LEGACY_THEME_VALUES = array(

        'default', 'youtube', 'vimeo', 'sidebar'
    );

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_api_theme_ThemeLibraryInterface
     */
    private $_themeLibrary;

    public function __construct(tubepress_api_log_LoggerInterface $logger,
                                tubepress_core_api_theme_ThemeLibraryInterface $themeLibrary)
    {
        $this->_logger       = $logger;
        $this->_themeLibrary = $themeLibrary;
    }

    public function onPreValidationSet(tubepress_core_api_event_EventInterface $event)
    {
        $themeValue = $event->getSubject();
        $shouldLog  = $this->_logger->isEnabled();

        if (in_array($themeValue, self::$_LEGACY_THEME_VALUES)) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Renaming theme value from %s to tubepress/legacy-%s', $themeValue, $themeValue));
            }

            $event->setSubject("tubepress/legacy-$themeValue");
            return;
        }

        $themeMap = $this->_themeLibrary->getMapOfAllThemeNamesToTitles();

        if (array_key_exists("unknown/legacy-$themeValue", $themeMap)) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Renaming theme value from %s to unknown/legacy-%s', $themeValue, $themeValue));
            }

            $event->setSubject("unknown/legacy-$themeValue");
        }
    }
}