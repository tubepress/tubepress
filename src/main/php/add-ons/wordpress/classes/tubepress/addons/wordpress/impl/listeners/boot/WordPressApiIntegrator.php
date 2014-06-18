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
 * Registers a few extensions to allow TubePress to work inside WordPress.
 */
class tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator
{
    public function onBoot(tubepress_api_event_EventInterface $event)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        if (!$environmentDetector->isWordPress()) {

            //short circuit
            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);

        /**
         * @var $wpFunctionWrapper tubepress_addons_wordpress_spi_WordPressFunctionWrapper
         */
        $wpFunctionWrapper =
            tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        /** http://code.google.com/p/tubepress/issues/detail?id=495#c2 */
        if (self::_isWordPressMuDomainMapped()) {

            $prefix = self::_getScheme($wpFunctionWrapper) . constant('COOKIE_DOMAIN') . '/wp-content';

        } else {

            $prefix = $wpFunctionWrapper->content_url();
        }

        $environmentDetector->setBaseUrl($prefix . "/plugins/$baseName");

        /* register the plugin's message bundles */
        $wpFunctionWrapper->load_plugin_textdomain('tubepress', false, "$baseName/src/main/resources/i18n");

        $contentFilter    = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_ContentFilter::_);
        $jsAndCssInjector = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector::_);
        $wpAdminHandler   = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpAdminHandler::_);
        $widgetHandler    = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WidgetHandler::_);

        $wpFunctionWrapper->add_filter('the_content', array($contentFilter, 'filterContent'), 10, 1);
        $wpFunctionWrapper->add_action('wp_head',     array($jsAndCssInjector, 'printInHtmlHead'), 10, 1);
        $wpFunctionWrapper->add_action('init',        array($jsAndCssInjector, 'registerStylesAndScripts'), 10, 1);

        $wpFunctionWrapper->add_action('admin_menu',            array($wpAdminHandler, 'registerAdminMenuItem'), 10, 1);
        $wpFunctionWrapper->add_action('admin_enqueue_scripts', array($wpAdminHandler, 'registerStylesAndScripts'), 10, 1);
        $wpFunctionWrapper->add_action('admin_head',            array($wpAdminHandler, 'printHeadMeta'), 10, 1);

        $wpFunctionWrapper->add_action('widgets_init', array($widgetHandler, 'registerWidget'), 10, 1);

        if (version_compare($wpFunctionWrapper->wp_version(), '2.8.alpha', '>')) {

            $filterPoint = 'plugin_row_meta';

        } else {

            $filterPoint = 'plugin_action_links';
        }

        $wpFunctionWrapper->add_filter($filterPoint, array($wpAdminHandler, 'modifyMetaRowLinks'), 10, 2);

        $wpFunctionWrapper->register_activation_hook($baseName . '/tubepress.php', array($this, '__callbackEnsureTubePressContentDirectoryExists'));
    }

    private static function _getScheme(tubepress_addons_wordpress_spi_WordPressFunctionWrapper $wpFunctionWrapper)
    {
        if ($wpFunctionWrapper->is_ssl()) {

            return 'https://';
        }

        return 'http://';
    }

    private static function _isWordPressMuDomainMapped()
    {
        return defined('DOMAIN_MAPPING') && constant('DOMAIN_MAPPING') && defined('COOKIE_DOMAIN');
    }

    public static function __callbackEnsureTubePressContentDirectoryExists()
    {
        /* add the content directory if it's not already there */
        if (!is_dir(WP_CONTENT_DIR . '/tubepress-content')) {

            self::_tryToMirror(
                TUBEPRESS_ROOT . '/src/main/resources/user-content-skeleton/tubepress-content',
                WP_CONTENT_DIR . '/tubepress-content');
        }
    }

    private static function _tryToMirror($source, $dest)
    {
        $fs = tubepress_impl_patterns_sl_ServiceLocator::getFileSystem();

        try {

            $fs->mirror($source, $dest);

        } catch (Exception $e) {

            //ignore
        }
    }
}