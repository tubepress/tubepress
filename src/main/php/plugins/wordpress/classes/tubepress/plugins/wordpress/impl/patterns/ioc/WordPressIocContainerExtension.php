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

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_plugins_wordpress_impl_message_WordPressMessageService', tubepress_spi_message_MessageService::_);
    }

    private function _registerOptionsStorageManager(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_StorageManager::_,
            'tubepress_plugins_wordpress_impl_options_WordPressStorageManager'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_plugins_wordpress_impl_options_WordPressStorageManager', tubepress_spi_options_StorageManager::_);
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

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler', tubepress_spi_options_ui_FormHandler::_);
    }

    private function _registerContentFilter(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_plugins_wordpress_spi_ContentFilter::_,
            'tubepress_plugins_wordpress_impl_DefaultContentFilter'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_plugins_wordpress_impl_DefaultContentFilter', tubepress_plugins_wordpress_spi_ContentFilter::_);
    }

    private function _registerCssAndJsInjector(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_plugins_wordpress_spi_FrontEndCssAndJsInjector::_,
            'tubepress_plugins_wordpress_impl_DefaultFrontEndCssAndJsInjector'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_plugins_wordpress_impl_DefaultFrontEndCssAndJsInjector', tubepress_plugins_wordpress_spi_FrontEndCssAndJsInjector::_);
    }


    private function _registerWidgetHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_plugins_wordpress_spi_WidgetHandler::_,
            'tubepress_plugins_wordpress_impl_DefaultWidgetHandler'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_plugins_wordpress_impl_DefaultWidgetHandler', tubepress_plugins_wordpress_spi_WidgetHandler::_);
    }

    private function _registerWpAdminHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_plugins_wordpress_spi_WpAdminHandler::_,
            'tubepress_plugins_wordpress_impl_DefaultWpAdminHandler'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_plugins_wordpress_impl_DefaultWpAdminHandler', tubepress_plugins_wordpress_spi_WpAdminHandler::_);
    }

    private function _registerWpFunctionWrapper(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_,
            'tubepress_plugins_wordpress_impl_DefaultWordPressFunctionWrapper'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_plugins_wordpress_impl_DefaultWordPressFunctionWrapper', tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);
    }
}