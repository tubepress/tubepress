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
 * Adds WordPress-specific services.
 */
class tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainerExtension implements ehough_iconic_api_extension_IExtension
{
    /**
     * Loads a specific configuration.
     *
     * @param ehough_iconic_impl_ContainerBuilder $container A ContainerBuilder instance
     *
     * @return void
     */
    public final function load(ehough_iconic_impl_ContainerBuilder $container)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        if (! $environmentDetector->isWordPress()) {

            //short circuit
            return;
        }

        /**
         * Core stuff.
         */
        $this->_registerMessageService($container);
        $this->_registerOptionsUiFormHandler($container);
        $this->_registerOptionsStorageManager($container);

        /**
         * WordPress specific stuff.
         */
        $this->_registerContentFilter($container);
        $this->_registerCssAndJsInjector($container);
        $this->_registerWidgetHandler($container);
        $this->_registerWpAdminHandler($container);
        $this->_registerWpFunctionWrapper($container);

        /**
         * Tabs.
         */
        $tabs = array(

            'tubepress_impl_options_ui_tabs_GallerySourceTab',
            'tubepress_impl_options_ui_tabs_ThumbsTab',
            'tubepress_impl_options_ui_tabs_EmbeddedTab',
            'tubepress_impl_options_ui_tabs_MetaTab',
            'tubepress_impl_options_ui_tabs_ThemeTab',
            'tubepress_impl_options_ui_tabs_FeedTab',
            'tubepress_impl_options_ui_tabs_CacheTab',
            'tubepress_impl_options_ui_tabs_AdvancedTab',
        );

        foreach ($tabs as $tab) {

            $container->register($tab, $tab)->addTag(tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME);
        }
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public final function getAlias()
    {
        return 'wordpress';
    }

    private function _registerMessageService(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_message_MessageService::_,
            'tubepress_plugins_wordpress_impl_message_WordPressMessageService'

        );
    }

    private function _registerOptionsStorageManager(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_StorageManager::_,
            'tubepress_plugins_wordpress_impl_options_WordPressStorageManager'
        );
    }

    private function _registerOptionsUiFormHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $tabsId = 'tubepress_impl_options_ui_DefaultTabsHandler';

        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            $tabsId, $tabsId

        );

        $filterId = 'tubepress_impl_options_ui_fields_FilterMultiSelectField';

        $container->register($filterId, $filterId);

        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_options_ui_FormHandler::_,
            'tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler'

        )->addArgument(new ehough_iconic_impl_Reference($tabsId))
         ->addArgument(new ehough_iconic_impl_Reference($filterId));
    }

    private function _registerContentFilter(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_plugins_wordpress_spi_ContentFilter::_,
            'tubepress_plugins_wordpress_impl_DefaultContentFilter'
        );
    }

    private function _registerCssAndJsInjector(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_plugins_wordpress_spi_FrontEndCssAndJsInjector::_,
            'tubepress_plugins_wordpress_impl_DefaultFrontEndCssAndJsInjector'
        );
    }


    private function _registerWidgetHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_plugins_wordpress_spi_WidgetHandler::_,
            'tubepress_plugins_wordpress_impl_DefaultWidgetHandler'
        );
    }

    private function _registerWpAdminHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_plugins_wordpress_spi_WpAdminHandler::_,
            'tubepress_plugins_wordpress_impl_DefaultWpAdminHandler'
        );
    }

    private function _registerWpFunctionWrapper(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_,
            'tubepress_plugins_wordpress_impl_DefaultWordPressFunctionWrapper'
        );
    }
}