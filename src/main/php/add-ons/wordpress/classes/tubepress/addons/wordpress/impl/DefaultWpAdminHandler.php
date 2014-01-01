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

class tubepress_addons_wordpress_impl_DefaultWpAdminHandler implements tubepress_addons_wordpress_spi_WpAdminHandler
{
    /**
     * Registers all the styles and scripts for the front end.
     *
     * @param string $hook The WP hook.
     *
     * @return void
     */
    public final function registerStylesAndScripts($hook)
    {
        /* only run on TubePress settings page */
        if ($hook !== 'settings_page_tubepress') {

            return;
        }

        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);
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

    public function printHeadMeta()
    {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">';
    }

    /**
     * Registers ourselves as an admin menu.
     *
     * @return void
     */
    public final function registerAdminMenuItem()
    {
        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        $wpFunctionWrapper->add_options_page('TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this, 'printOptionsPageHtml'));
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function printOptionsPageHtml()
    {
        /* get the form handler */
        $optionsForm   = tubepress_impl_patterns_sl_ServiceLocator::getOptionsPage();
        $hrps          = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $errors        = array();
        $justSubmitted = false;

        /* are we updating? */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hrps->hasParam('tubepress_save')) {

            $justSubmitted = true;

            $errors = $optionsForm->onSubmit();
        }

        print $optionsForm->getHtml($errors, $justSubmitted);
    }

    /**
     * @param array  $links An array of meta links for this plugin.
     * @param string $file  The file.
     *
     * @return array The modified links
     */
    public final function modifyMetaRowLinks($links, $file)
    {
        $wordPressFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        $plugin = $wordPressFunctionWrapper->plugin_basename(basename(TUBEPRESS_ROOT) . '/tubepress.php');

        if ($file != $plugin) {

            return $links;
        }

        return array_merge($links, array(

            sprintf('<a href="options-general.php?page=tubepress.php">%s</a>', $wordPressFunctionWrapper->__('Settings', 'tubepress')),
            sprintf('<a href="http://tubepress.com/documentation/">Documentation</a>'),
            sprintf('<a href="http://tubepress.com/forum/">Support</a>'),
        ));
    }

    private function _getCssMap()
    {
        return array(

            'bootstrap-3.0.2'       => '/src/main/web/options-gui/vendor/bootstrap-3.0.2/css/bootstrap-custom.css',
            'bootstrap-theme'       => '/src/main/web/options-gui/vendor/bootstrap-3.0.2/css/bootstrap-custom-theme.css',
            'bootstrap-multiselect' => '/src/main/web/options-gui/vendor/bootstrap-multiselect-0.9.1/css/bootstrap-multiselect.css',
            'tubepress-extra'       => '/src/main/php/add-ons/wordpress/web/options-gui/css/options-page.css',
            'spectrum'              => '/src/main/web/options-gui/vendor/spectrum-1.1.2/spectrum.css',
        );
    }

    private function _getJsMap()
    {
        $toReturn = array(

            'bootstrap-3.0.2' => '/src/main/web/options-gui/vendor/bootstrap-3.0.2/js/bootstrap.min.js',
        );

        if ($this->_isIE8orLower()) {

            $toReturn = array_merge($toReturn, array(

                'html5-shiv-3.7.0' => '/src/main/web/options-gui/vendor/html5-shiv-3.7.0/html5shiv.js',
                'respond-1.3.0'    => '/src/main/web/options-gui/vendor/respond-1.3.0/respond.min.js',
            ));
        }

        $toReturn = array_merge($toReturn, array(

            'bootstrap-multiselect'         => '/src/main/web/options-gui/vendor/bootstrap-multiselect-0.9.1/js/bootstrap-multiselect.js',
            'spectrum'                      => '/src/main/web/options-gui/vendor/spectrum-1.1.2/spectrum.js',
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
