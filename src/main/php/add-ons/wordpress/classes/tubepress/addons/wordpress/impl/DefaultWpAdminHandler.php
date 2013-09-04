<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
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

            $url = $wpFunctionWrapper->plugins_url($baseName . '/src/main/web/' . $cssRelativePath, $baseName);

            $wpFunctionWrapper->wp_register_style($cssName, $url);
            $wpFunctionWrapper->wp_enqueue_style($cssName);
        }

        foreach ($this->_getJsMap() as $jsName => $jsRelativePath) {

            $url = $wpFunctionWrapper->plugins_url($baseName . '/src/main/web/' . $jsRelativePath, $baseName);

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
        $optionsForm = tubepress_impl_patterns_sl_ServiceLocator::getOptionsPage();
        $hrps        = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();

        /* are we updating? */
        if ($hrps->hasParam('tubepress_save')) {

            self::_verifyNonce();

            try {

                $result = $optionsForm->onSubmit();

                if ($result === null) {

                    echo '<div class="updated tubepress-options-updated"><p><strong>Options updated</strong></p></div>';

                } else {

                    self::_error($result);
                }

            } catch (Exception $error) {

                self::_error($error->getMessage());
            }
        }

        print $optionsForm->getHtml();
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

    private static function _verifyNonce()
    {
        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        $wpFunctionWrapper->check_admin_referer('tubepress-save', 'tubepress-nonce');
    }

    private static function _error($message)
    {
        if (is_array($message)) {

            $message = implode($message, '<br />');
        }

        echo '<div id="message" class="error fade"><p><strong>' . $message . '</strong></p></div>';
    }

    private function _getCssMap()
    {
        return array(

            'bootstrap-3.0.0'       => 'vendor/bootstrap-3.0.0/css/bootstrap.min.css',
            'bootstrap-theme'       => 'vendor/bootstrap-3.0.0/css/bootstrap-theme.min.css',
            'bootstrap-multiselect' => 'vendor/bootstrap-multiselect-0.9/css/bootstrap-multiselect.css',
            'tubepress-extra'       => 'css/options-page.css',
        );
    }

    private function _getJsMap()
    {
        return array(

            'bootstrap-3.0.0'       => 'vendor/bootstrap-3.0.0/js/bootstrap.min.js',
            'bootstrap-multiselect' => 'vendor/bootstrap-multiselect-0.9/js/bootstrap-multiselect.js',
        );
    }
}
