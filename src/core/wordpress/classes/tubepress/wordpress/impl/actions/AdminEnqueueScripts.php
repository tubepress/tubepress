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

class tubepress_wordpress_impl_actions_AdminEnqueueScripts
{
    /**
     * @var tubepress_wordpress_spi_WpFunctionsInterface
     */
    private $_wpFunctions;

    public function __construct(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_core_event_api_EventInterface $eventInterface)
    {
        $args = $eventInterface->getSubject();
        $hook = $args[0];

        /* only run on TubePress settings page */
        if ($hook !== 'settings_page_tubepress') {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);

        foreach ($this->_getCssMap() as $cssName => $cssRelativePath) {

            $url = $this->_wpFunctions->plugins_url($baseName . $cssRelativePath, $baseName);

            $this->_wpFunctions->wp_register_style($cssName, $url);
            $this->_wpFunctions->wp_enqueue_style($cssName);
        }

        foreach ($this->_getJsMap() as $jsName => $jsRelativePath) {

            $url = $this->_wpFunctions->plugins_url($baseName . $jsRelativePath, $baseName);

            $this->_wpFunctions->wp_register_script($jsName, $url);
            $this->_wpFunctions->wp_enqueue_script($jsName, false, array(), false, false);
        }
    }

    private function _getCssMap()
    {
        return array(

            'bootstrap-3.1.1'         => '/src/core/options-ui/web/vendor/bootstrap-3.1.1/css/bootstrap-custom.css',
            'bootstrap-theme'         => '/src/core/options-ui/web/vendor/bootstrap-3.1.1/css/bootstrap-custom-theme.css',
            'bootstrap-multiselect'   => '/src/core/options-ui/web/vendor/bootstrap-multiselect-0.9.2/css/bootstrap-multiselect.css',
            'blueimp-gallery-2.14.0'  => '/src/core/options-ui/web/vendor/blueimp-gallery-2.14.0/css/blueimp-gallery.min.css',
            'bootstrap-image-gallery' => '/src/core/options-ui/web/vendor/bootstrap-image-gallery-3.1.0/css/bootstrap-image-gallery.css',
            'tubepress-options-gui'   => '/src/core/options-ui/web/css/options-page.css',
            'wordpress-options-gui'   => '/src/core/wordpress/web/options-gui/css/options-page.css',
            'spectrum'                => '/src/core/options-ui/web/vendor/spectrum-1.3.1/spectrum.css',
        );
    }

    private function _getJsMap()
    {
        $toReturn = array(

            'bootstrap-3.1.1' => '/src/core/options-ui/web/vendor/bootstrap-3.1.1/js/bootstrap.min.js',
        );

        if ($this->_isIE8orLower()) {

            $toReturn = array_merge($toReturn, array(

                'html5-shiv-3.7.0' => '/src/core/options-ui/web/vendor/html5-shiv-3.7.0/html5shiv.js',
                'respond-1.4.2'    => '/src/core/options-ui/web/vendor/respond-1.4.2/respond.min.js',
            ));
        }

        $toReturn = array_merge($toReturn, array(

            'bootstrap-multiselect'         => '/src/core/options-ui/web/vendor/bootstrap-multiselect-0.9.2/js/bootstrap-multiselect.js',
            'spectrum'                      => '/src/core/options-ui/web/vendor/spectrum-1.3.1/spectrum.js',
            'blueimp-gallery-2.14.0'        => '/src/core/options-ui/web/vendor/blueimp-gallery-2.14.0/js/blueimp-gallery.min.js',
            'bootstrap-image-gallery'       => '/src/core/options-ui/web/vendor/bootstrap-image-gallery-3.1.0/js/bootstrap-image-gallery.js',
            'bootstrap-field-error-handler' => '/src/core/options-ui/web/js/bootstrap-field-error-handler.js',
            'participant-filter-handler'    => '/src/core/options-ui/web/js/participant-filter-handler.js',
            'spectrum-js-initializer'       => '/src/core/options-ui/web/js/spectrum-js-initializer.js',
            'bootstrap-multiselect-init'    => '/src/core/options-ui/web/js/bootstrap-multiselect-initializer.js',
            'theme-field-handler'           => '/src/core/options-ui/web/js/theme-field-handler.js',
            'theme-reminder'                => '/src/core/wordpress/web/options-gui/js/theme-reminder.js',
            'iframe-loader'                 => '/src/core/wordpress/web/options-gui/js/iframe-loader.js',
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
