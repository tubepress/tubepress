<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_plugins_wordpress_impl_DefaultWpAdminHandler implements tubepress_plugins_wordpress_spi_WpAdminHandler
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

        $wpFunctionWrapper    = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);
        $baseName             = basename(TUBEPRESS_ROOT);
        $jqueryUiCssUrl       = $wpFunctionWrapper->plugins_url("$baseName/src/main/web/vendor/jquery-ui/jquery-ui-flick-theme/jquery-ui-1.8.24.custom.css", $baseName);
        $wpOptionsPageCss     = $wpFunctionWrapper->plugins_url("$baseName/src/main/web/css/options-page.css", $baseName);
        $jqueryMultiSelectCss = $wpFunctionWrapper->plugins_url("$baseName/src/main/web/vendor/jquery-ui-multiselect-widget/jquery.multiselect.css", $baseName);
        $jsColorUrl           = $wpFunctionWrapper->plugins_url("$baseName/src/main/web/vendor/jscolor/jscolor.js", $baseName);
        $jqueryUiJsUrl        = $wpFunctionWrapper->plugins_url("$baseName/src/main/web/vendor/jquery-ui/jquery-ui-1.8.24.custom.min.js", $baseName);
        $jqueryMultiSelectJs  = $wpFunctionWrapper->plugins_url("$baseName/src/main/web/vendor/jquery-ui-multiselect-widget/jquery.multiselect.min.js", $baseName);

        $wpFunctionWrapper->wp_register_style('jquery-ui-flick', $jqueryUiCssUrl);
        $wpFunctionWrapper->wp_register_style('tubepress-options-page', $wpOptionsPageCss);
        $wpFunctionWrapper->wp_register_style('jquery-ui-multiselect-widget', $jqueryMultiSelectCss);

        $wpFunctionWrapper->wp_enqueue_style('jquery-ui-flick');
        $wpFunctionWrapper->wp_enqueue_style('tubepress-options-page');
        $wpFunctionWrapper->wp_enqueue_style('jquery-ui-multiselect-widget');

        $wpFunctionWrapper->wp_register_script('jscolor-tubepress', $jsColorUrl);
        $wpFunctionWrapper->wp_register_script('jquery-ui-tubepress', $jqueryUiJsUrl);
        $wpFunctionWrapper->wp_register_script('jquery-ui-multiselect-widget', $jqueryMultiSelectJs);

        $wpFunctionWrapper->wp_enqueue_script('jquery-ui-tubepress');
        $wpFunctionWrapper->wp_enqueue_script('jquery-ui-multiselect-widget');
        $wpFunctionWrapper->wp_enqueue_script('jscolor-tubepress');
    }

    /**
     * Registers ourselves as an admin menu.
     *
     * @return void
     */
    public final function registerAdminMenuItem()
    {
        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);

        $wpFunctionWrapper->add_options_page('TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this, 'printOptionsPageHtml'));
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function printOptionsPageHtml()
    {
        /* get the form handler */
        $optionsForm = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFormHandler();
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
        $wordPressFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);

        $plugin = $wordPressFunctionWrapper->plugin_basename(basename(TUBEPRESS_ROOT) . '/tubepress.php');

        if ($file != $plugin) {

            return $links;
        }

        return array_merge($links, array(

            sprintf('<a href="options-general.php?page=tubepress.php">%s</a>', $wordPressFunctionWrapper->__('Settings', 'tubepress')),
            sprintf('<a href="http://tubepress.org/documentation/">Documentation</a>'),
            sprintf('<a href="http://tubepress.org/forum/">Support</a>'),
        ));
    }

    private static function _verifyNonce()
    {
        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);

        $wpFunctionWrapper->check_admin_referer('tubepress-save', 'tubepress-nonce');
    }

    private static function _error($message)
    {
        if (is_array($message)) {

            $message = implode($message, '<br />');
        }

        echo '<div id="message" class="error fade"><p><strong>' . $message . '</strong></p></div>';
    }
}
