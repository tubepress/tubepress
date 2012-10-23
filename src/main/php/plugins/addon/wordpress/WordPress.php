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
    public static function registerWordPressListeners()
    {
        $environmentDetector = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();

        if (! $environmentDetector->isWordPress()) {

            //short circuit
            return;
        }

        /**
         * Build a WP-specific IOC container.
         */
        $iocContainer   = new tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer();
        tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::setCoreIocContainer($iocContainer);

        self::_registerWpOptions();
        self::_registerOptionsPageItems();
        self::_registerSelfWithWordPressApi();
    }

    private static function _registerSelfWithWordPressApi()
    {
        global $tubepress_base_url;

        $baseName          = basename(TUBEPRESS_ROOT);
        $wpFunctionWrapper = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getWordPressFunctionWrapper();

        /** http://code.google.com/p/tubepress/issues/detail?id=495#c2 */
        if (self::_isWordPressMuDomainMapped()) {

            $prefix = self::_getScheme($wpFunctionWrapper) . constant('COOKIE_DOMAIN') . '/wp-content';

        } else {

            $prefix = $wpFunctionWrapper->content_url();
        }

        $tubepress_base_url = $prefix . "/plugins/$baseName";

        /* register the plugin's message bundles */
        $wpFunctionWrapper->load_plugin_textdomain('tubepress', false, "$baseName/src/main/resources/i18n");

        $contentFilter    = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getContentFilter();
        $jsAndCssInjector = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getFrontEndCssAndJsInjector();
        $wpAdminHandler   = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getWpAdminHandler();
        $widgetHandler    = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getWidgetHandler();

        $wpFunctionWrapper->add_filter('the_content', array($contentFilter, 'filterContent'), 10, 1);
        $wpFunctionWrapper->add_action('wp_head', array($jsAndCssInjector, 'printInHtmlHead'), 10, 1);
        $wpFunctionWrapper->add_action('init', array($jsAndCssInjector, 'registerStylesAndScripts'), 10, 1);

        $wpFunctionWrapper->add_action('admin_menu', array($wpAdminHandler, 'registerAdminMenuItem'), 10, 1);
        $wpFunctionWrapper->add_action('admin_enqueue_scripts', array($wpAdminHandler, 'registerStylesAndScripts'), 10, 1);

        $wpFunctionWrapper->add_action('widgets_init', array($widgetHandler, 'registerWidget'), 10, 1);

        if (version_compare($wpFunctionWrapper->wp_version(), '2.8.alpha', '>')) {

            $filterPoint = 'plugin_row_meta';
        } else {

            $filterPoint = 'plugin_action_links';
        }

        $wpFunctionWrapper->add_filter($filterPoint, array($wpAdminHandler, 'modifyMetaRowLinks'), 10, 2);
    }

    private static function _registerOptionsPageItems()
    {
        $wordPressFunctionWrapper = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getWordPressFunctionWrapper();

        if (! $wordPressFunctionWrapper->is_admin()) {

            //we only want to do this stuff on the admin page
            return;
        }

        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        $tabs = array(

            new tubepress_impl_options_ui_tabs_GallerySourceTab(),
            new tubepress_impl_options_ui_tabs_ThumbsTab(),
            new tubepress_impl_options_ui_tabs_EmbeddedTab(),
            new tubepress_impl_options_ui_tabs_MetaTab(),
            new tubepress_impl_options_ui_tabs_ThemeTab(),
            new tubepress_impl_options_ui_tabs_FeedTab(),
            new tubepress_impl_options_ui_tabs_CacheTab(),
            new tubepress_impl_options_ui_tabs_AdvancedTab()
        );

        foreach ($tabs as $tab) {

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME,
                $tab
            );
        }
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

tubepress_plugins_wordpress_WordPress::registerWordPressListeners();