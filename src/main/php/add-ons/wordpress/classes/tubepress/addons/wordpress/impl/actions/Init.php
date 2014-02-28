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

class tubepress_addons_wordpress_impl_actions_Init
{
    /**
     * Filter the content (which may be empty).
     */
    public final function execute(array $args)
    {
        /**
         * @var $wordPressFunctionWrapper tubepress_addons_wordpress_spi_WpFunctionsInterface
         */
        $wordPressFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        /* no need to queue any of this stuff up in the admin section or login page */
        if ($wordPressFunctionWrapper->is_admin() || __FILE__ === 'wp-login.php') {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);

        $jsUrl  = $wordPressFunctionWrapper->plugins_url("$baseName/src/main/web/js/tubepress.js", $baseName);

        $wordPressFunctionWrapper->wp_register_script('tubepress', $jsUrl);

        $wordPressFunctionWrapper->wp_enqueue_script('jquery', false, array(), false, false);
        $wordPressFunctionWrapper->wp_enqueue_script('tubepress', false, array(), false, false);


        $this->_enqueueThemeResources($wordPressFunctionWrapper);
    }

    private function _enqueueThemeResources(tubepress_addons_wordpress_spi_WpFunctionsInterface $wpFunctions)
    {
        $themeHandler = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $styles       = $themeHandler->getStyles();
        $scripts      = $themeHandler->getScripts();
        $styleCount   = count($styles);
        $scriptCount  = count($scripts);

        for ($x = 0; $x < $styleCount; $x++) {

            $handle = 'tubepress-theme-' . $x;

            $wpFunctions->wp_register_style($handle, $styles[$x]);
            $wpFunctions->wp_enqueue_style($handle);
        }

        for ($x = 0; $x < $scriptCount; $x++) {

            $handle = 'tubepress-theme-' . $x;

            $wpFunctions->wp_register_script($handle, $scripts[$x]);
            $wpFunctions->wp_enqueue_script($handle, false, array(), false, false);
        }
    }
}
