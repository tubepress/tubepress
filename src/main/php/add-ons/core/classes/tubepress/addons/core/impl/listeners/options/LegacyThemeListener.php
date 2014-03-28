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
class tubepress_addons_core_impl_listeners_options_LegacyThemeListener
{
    private static $_LEGACY_THEME_VALUES = array(

        'default', 'youtube', 'vimeo', 'sidebar'
    );

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Legacy Theme Listener');
    }

    public function onPreValidationSet(tubepress_api_event_EventInterface $event)
    {
        $themeValue = $event->getSubject();
        $shouldLog  = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if (in_array($themeValue, self::$_LEGACY_THEME_VALUES)) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Renaming theme value from %s to tubepress/legacy-%s', $themeValue, $themeValue));
            }

            $event->setSubject("tubepress/legacy-$themeValue");
            return;
        }

        $themeHandler = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $themeMap     = $themeHandler->getMapOfAllThemeNamesToTitles();

        if (array_key_exists("unknown/legacy-$themeValue", $themeMap)) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Renaming theme value from %s to unknown/legacy-%s', $themeValue, $themeValue));
            }

            $event->setSubject("unknown/legacy-$themeValue");
        }
    }
}