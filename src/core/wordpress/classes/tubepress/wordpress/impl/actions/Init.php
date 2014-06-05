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

class tubepress_wordpress_impl_actions_Init
{
    /**
     * @var tubepress_wordpress_spi_WpFunctionsInterface
     */
    private $_wpFunctions;

    /**
     * @var tubepress_core_theme_api_ThemeLibraryInterface
     */
    private $_themeLibrary;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions,
                                tubepress_core_theme_api_ThemeLibraryInterface      $themeLibrary,
                                tubepress_api_util_StringUtilsInterface             $stringUtils)
    {
        $this->_wpFunctions  = $wpFunctions;
        $this->_themeLibrary = $themeLibrary;
        $this->_stringUtils  = $stringUtils;
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_core_event_api_EventInterface $event)
    {
        /* no need to queue any of this stuff up in the admin section or login page */
        if ($this->_wpFunctions->is_admin() || __FILE__ === 'wp-login.php') {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);

        $jsUrl = $this->_wpFunctions->plugins_url("$baseName/src/core/html/web/js/tubepress.js", $baseName);

        $this->_wpFunctions->wp_register_script('tubepress', $jsUrl);

        $this->_wpFunctions->wp_enqueue_script('jquery', false, array(), false, false);
        $this->_wpFunctions->wp_enqueue_script('tubepress', false, array(), false, false);


        $this->_enqueueThemeResources($this->_wpFunctions);
    }

    private function _enqueueThemeResources(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
    {
        $styles       = $this->_themeLibrary->getStylesUrls();
        $scripts      = $this->_themeLibrary->getScriptsUrls();
        $styleCount   = count($styles);
        $scriptCount  = count($scripts);

        for ($x = 0; $x < $styleCount; $x++) {

            $handle = 'tubepress-theme-' . $x;

            $wpFunctions->wp_register_style($handle, $styles[$x]->toString());
            $wpFunctions->wp_enqueue_style($handle);
        }

        for ($x = 0; $x < $scriptCount; $x++) {

            if ($this->_stringUtils->endsWith($scripts[$x]->toString(), '/src/core/html/web/js/tubepress.js')) {

                //we already loaded this above
                continue;
            }

            $handle = 'tubepress-theme-' . $x;

            $wpFunctions->wp_register_script($handle, $scripts[$x]->toString());
            $wpFunctions->wp_enqueue_script($handle, false, array(), false, false);
        }
    }
}
