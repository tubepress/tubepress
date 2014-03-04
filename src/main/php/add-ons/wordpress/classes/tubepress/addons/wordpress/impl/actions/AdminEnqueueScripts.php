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

class tubepress_addons_wordpress_impl_actions_AdminEnqueueScripts
{
    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_api_event_EventInterface $eventInterface)
    {
        $args = $eventInterface->getSubject();
        $hook = $args[0];

        /* only run on TubePress settings page */
        if ($hook !== 'settings_page_tubepress') {

            return;
        }

        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
        $baseName          = basename(TUBEPRESS_ROOT);

        foreach ($this->_getCssMap() as $cssName => $cssRelativePath) {

            $url = $wpFunctionWrapper->plugins_url($baseName . $cssRelativePath, $baseName);

            $wpFunctionWrapper->wp_register_style($cssName, $url);
            $wpFunctionWrapper->wp_enqueue_style($cssName);
        }

        foreach ($this->_getJsMap() as $jsName => $jsRelativePath) {

            $url = $wpFunctionWrapper->plugins_url($baseName . $jsRelativePath, $baseName);

            $wpFunctionWrapper->wp_register_script($jsName, $url);
            $wpFunctionWrapper->wp_enqueue_script($jsName, false, array(), false, false);
        }
    }

    private function _getCssMap()
    {
        return array(

            'bootstrap-3.1.1'       => '/src/main/web/options-gui/vendor/bootstrap-3.1.1/css/bootstrap-custom.css',
            'bootstrap-theme'       => '/src/main/web/options-gui/vendor/bootstrap-3.1.1/css/bootstrap-custom-theme.css',
            'bootstrap-multiselect' => '/src/main/web/options-gui/vendor/bootstrap-multiselect-0.9.2/css/bootstrap-multiselect.css',
            'tubepress-extra'       => '/src/main/php/add-ons/wordpress/web/options-gui/css/options-page.css',
            'spectrum'              => '/src/main/web/options-gui/vendor/spectrum-1.3.1/spectrum.css',
        );
    }

    private function _getJsMap()
    {
        $toReturn = array(

            'bootstrap-3.1.1' => '/src/main/web/options-gui/vendor/bootstrap-3.1.1/js/bootstrap.min.js',
        );

        if ($this->_isIE8orLower()) {

            $toReturn = array_merge($toReturn, array(

                'html5-shiv-3.7.0' => '/src/main/web/options-gui/vendor/html5-shiv-3.7.0/html5shiv.js',
                'respond-1.4.2'    => '/src/main/web/options-gui/vendor/respond-1.4.2/respond.min.js',
            ));
        }

        $toReturn = array_merge($toReturn, array(

            'bootstrap-multiselect'         => '/src/main/web/options-gui/vendor/bootstrap-multiselect-0.9.2/js/bootstrap-multiselect.js',
            'spectrum'                      => '/src/main/web/options-gui/vendor/spectrum-1.3.1/spectrum.js',
            'bootstrap-field-error-handler' => '/src/main/web/options-gui/js/bootstrap-field-error-handler.js',
            'participant-filter-handler'    => '/src/main/web/options-gui/js/participant-filter-handler.js',
            'spectrum-js-initializer'       => '/src/main/web/options-gui/js/spectrum-js-initializer.js',
            'bootstrap-multiselect-init'    => '/src/main/web/options-gui/js/bootstrap-multiselect-initializer.js',
            'iframe-loader'                 => '/src/main/php/add-ons/wordpress/web/options-gui/js/iframe-loader.js',
        ));

        return $toReturn;
    }

    private function _isIE8orLower()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {

            //no user agent for some reason
            return false;
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        if (stristr($userAgent, 'MSIE') === false) {

            //shortcut - MSIE is not in user-agent header
            return false;
        }

        if (!preg_match('/MSIE (.*?);/i', $userAgent, $m)) {

            //not IE
            return false;
        }

        if (!isset($m[1]) || !is_numeric($m[1])) {

            //couldn't parse version for some reason
            return false;
        }

        $version = (int) $m[1];

        return $version <= 8;
    }
}
