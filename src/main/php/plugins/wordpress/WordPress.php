<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Registers a few extensions to allow TubePress to work inside WordPress.
 */
class tubepress_plugins_wordpress_WordPress
{
    public static function init()
    {
        $environmentDetector = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();

        if (! $environmentDetector->isWordPress()) {

            //short circuit
            return;
        }

        self::_registerWpOptions();
        self::_registerSelfWithWordPressApi();
    }

    private static function _registerSelfWithWordPressApi()
    {
        global $tubepress_base_url;

        $baseName          = basename(TUBEPRESS_ROOT);
        $wpFunctionWrapper =
            tubepress_impl_patterns_ioc_KernelServiceLocator::getService(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);

        /** http://code.google.com/p/tubepress/issues/detail?id=495#c2 */
        if (self::_isWordPressMuDomainMapped()) {

            $prefix = self::_getScheme($wpFunctionWrapper) . constant('COOKIE_DOMAIN') . '/wp-content';

        } else {

            $prefix = $wpFunctionWrapper->content_url();
        }

        $tubepress_base_url = $prefix . "/plugins/$baseName";

        /* register the plugin's message bundles */
        $wpFunctionWrapper->load_plugin_textdomain('tubepress', false, "$baseName/src/main/resources/i18n");

        $contentFilter    =
            tubepress_impl_patterns_ioc_KernelServiceLocator::getService(tubepress_plugins_wordpress_spi_ContentFilter::_);
        $jsAndCssInjector =
            tubepress_impl_patterns_ioc_KernelServiceLocator::getService(tubepress_plugins_wordpress_spi_FrontEndCssAndJsInjector::_);
        $wpAdminHandler   =
            tubepress_impl_patterns_ioc_KernelServiceLocator::getService(tubepress_plugins_wordpress_spi_WpAdminHandler::_);
        $widgetHandler    =
            tubepress_impl_patterns_ioc_KernelServiceLocator::getService(tubepress_plugins_wordpress_spi_WidgetHandler::_);

        $wpFunctionWrapper->add_filter('the_content', array($contentFilter, 'filterContent'), 10, 1);
        $wpFunctionWrapper->add_action('wp_head',     array($jsAndCssInjector, 'printInHtmlHead'), 10, 1);
        $wpFunctionWrapper->add_action('init',        array($jsAndCssInjector, 'registerStylesAndScripts'), 10, 1);

        $wpFunctionWrapper->add_action('admin_menu',            array($wpAdminHandler, 'registerAdminMenuItem'), 10, 1);
        $wpFunctionWrapper->add_action('admin_enqueue_scripts', array($wpAdminHandler, 'registerStylesAndScripts'), 10, 1);

        $wpFunctionWrapper->add_action('widgets_init', array($widgetHandler, 'registerWidget'), 10, 1);

        if (version_compare($wpFunctionWrapper->wp_version(), '2.8.alpha', '>')) {

            $filterPoint = 'plugin_row_meta';

        } else {

            $filterPoint = 'plugin_action_links';
        }

        $wpFunctionWrapper->add_filter($filterPoint, array($wpAdminHandler, 'modifyMetaRowLinks'), 10, 2);
    }

    private static function _registerWpOptions()
    {
        $odr = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionDescriptorReference();

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_wordpress_api_const_options_names_WordPress::WIDGET_TITLE);
        $option->setDefaultValue('TubePress');
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE);
        $option->setDefaultValue('[tubepress thumbHeight=\'105\' thumbWidth=\'135\']');
        $odr->registerOptionDescriptor($option);
    }

    private static function _getScheme(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper $wpFunctionWrapper)
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
}

tubepress_plugins_wordpress_WordPress::init();