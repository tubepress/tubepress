<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_theme_impl_listeners_LegacyThemeListener
{
    private static $_LEGACY_THEME_VALUES = array(

        'default', 'youtube', 'vimeo', 'sidebar',
    );

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    public function __construct(tubepress_api_log_LoggerInterface       $logger,
                                tubepress_api_contrib_RegistryInterface $themeRegistry)
    {
        $this->_logger        = $logger;
        $this->_themeRegistry = $themeRegistry;
    }

    public function onPreValidationSet(tubepress_api_event_EventInterface $event)
    {
        $themeValue = $event->getArgument('optionValue');
        $shouldLog  = $this->_logger->isEnabled();

        if (in_array($themeValue, self::$_LEGACY_THEME_VALUES)) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Renaming theme value from %s to tubepress/legacy-%s', $themeValue, $themeValue));
            }

            $event->setArgument('optionValue', "tubepress/legacy-$themeValue");

            return;
        }

        $allThemes = $this->_themeRegistry->getAll();

        foreach ($allThemes as $theme) {

            if ($theme->getName() !== "unknown/legacy-$themeValue") {

                continue;
            }

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Renaming theme value from %s to unknown/legacy-%s', $themeValue, $themeValue));
            }

            $event->setArgument('optionValue', "unknown/legacy-$themeValue");
        }
    }
}
