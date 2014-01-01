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

class tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector implements tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector
{
    /**
     * Prints out HTML and CSS into the HTML <head>.
     *
     * @return void
     */
    public final function printInHtmlHead()
    {
        $wordPressFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        /* no need to print anything in the head of the admin section */
        if ($wordPressFunctionWrapper->is_admin()) {

            return;
        }

        $hh = tubepress_impl_patterns_sl_ServiceLocator::getCssAndJsHtmlGenerator();

        /* this inline JS helps initialize TubePress */
        print $hh->getCssHtml();
        print $hh->getJsHtml();
    }

    /**
     * Registers all the styles and scripts for the front end.
     *
     * @return void
     */
    public final function registerStylesAndScripts()
    {
        $wordPressFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        /* no need to queue any of this stuff up in the admin section or login page */
        if ($wordPressFunctionWrapper->is_admin() || __FILE__ === 'wp-login.php') {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);

        $jsUrl  = $wordPressFunctionWrapper->plugins_url("$baseName/src/main/web/js/tubepress.js", $baseName);
        $cssUrl = $wordPressFunctionWrapper->plugins_url("$baseName/src/main/web/css/tubepress.css", $baseName);

        $wordPressFunctionWrapper->wp_register_script('tubepress', $jsUrl);
        $wordPressFunctionWrapper->wp_register_style('tubepress', $cssUrl);

        $wordPressFunctionWrapper->wp_enqueue_script('jquery', false, array(), false, false);
        $wordPressFunctionWrapper->wp_enqueue_script('tubepress', false, array(), false, false);

        $wordPressFunctionWrapper->wp_enqueue_style('tubepress');
    }
}
