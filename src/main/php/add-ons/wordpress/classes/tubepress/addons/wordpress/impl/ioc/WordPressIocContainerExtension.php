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
class tubepress_addons_wordpress_impl_ioc_WordPressIocContainerExtension implements tubepress_api_ioc_ContainerExtensionInterface
{
    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_api_ioc_ContainerInterface $container A tubepress_api_ioc_ContainerInterface instance.
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public function load(tubepress_api_ioc_ContainerInterface $container)
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

        $container->register(

            'tubepress_addons_wordpress_impl_options_WordPressOptionsProvider',
            'tubepress_addons_wordpress_impl_options_WordPressOptionsProvider'
        )->addTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        /**
         * Tabs.
         */

        $container->register(

            'tubepress_impl_options_ui_tabs_GallerySourceTab',
            'tubepress_impl_options_ui_tabs_GallerySourceTab')
            ->addTag(tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME)
            ->addArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/gallery_source_tab.tpl.php')
            ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_options_ui_PluggableOptionsPageParticipant::_, 'method' => 'setPluggableOptionsPageParticipants'));

        $tabs = array(

            'tubepress_impl_options_ui_tabs_ThumbsTab',
            'tubepress_impl_options_ui_tabs_EmbeddedTab',
            'tubepress_impl_options_ui_tabs_MetaTab',
            'tubepress_impl_options_ui_tabs_ThemeTab',
            'tubepress_impl_options_ui_tabs_FeedTab',
            'tubepress_impl_options_ui_tabs_CacheTab',
            'tubepress_impl_options_ui_tabs_AdvancedTab',
        );

        foreach ($tabs as $tab) {

            $container->register($tab, $tab)
                ->addTag(tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME)
                ->addArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tab.tpl.php')
                ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_options_ui_PluggableOptionsPageParticipant::_, 'method' => 'setPluggableOptionsPageParticipants'));
        }

        $this->_registerListeners($container);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator',
            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::BOOT_COMPLETE, 'method' => 'onBoot'));

        $container->register(

            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_OPTIONS_UI_MAIN, 'method' => 'onOptionsUiTemplate'));

        $container->register(

            'tubepress_addons_wordpress_impl_listeners_cssjs_BaseUrlSetter',
            'tubepress_addons_wordpress_impl_listeners_cssjs_BaseUrlSetter'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_GLOBAL_JS_CONFIG, 'method' => 'onJsConfig'));
    }

    private function _registerMessageService(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            tubepress_spi_message_MessageService::_,
            'tubepress_addons_wordpress_impl_message_WordPressMessageService'
        );
    }

    private function _registerOptionsStorageManager(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            tubepress_spi_options_StorageManager::_,
            'tubepress_addons_wordpress_impl_options_WordPressStorageManager'
        );
    }

    private function _registerOptionsUiFormHandler(tubepress_api_ioc_ContainerInterface $container)
    {
        $tabsId = 'tubepress_impl_options_ui_DefaultTabsHandler';

        $container->register(

            $tabsId, $tabsId

        )->addArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tabs.tpl.php')
         ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME, 'method' => 'setPluggableOptionsPageTabs'));

        $filterId = 'tubepress_impl_options_ui_fields_FilterMultiSelectField';

        $container->register($filterId, $filterId)
            ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_options_ui_PluggableOptionsPageParticipant::_, 'method' => 'setPluggableOptionsPageParticipants'));

        $container->register(

            tubepress_spi_options_ui_FormHandler::_,
            'tubepress_impl_options_ui_DefaultFormHandler'

        )->addArgument(new tubepress_impl_ioc_Reference($tabsId))
         ->addArgument(new tubepress_impl_ioc_Reference($filterId))
         ->addArgument(TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/options_page.tpl.php');
    }

    private function _registerContentFilter(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            tubepress_addons_wordpress_spi_ContentFilter::_,
            'tubepress_addons_wordpress_impl_DefaultContentFilter'
        );
    }

    private function _registerCssAndJsInjector(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector::_,
            'tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector'
        );
    }

    private function _registerWidgetHandler(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            tubepress_addons_wordpress_spi_WidgetHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWidgetHandler'
        );
    }

    private function _registerWpAdminHandler(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            tubepress_addons_wordpress_spi_WpAdminHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWpAdminHandler'
        );
    }

    private function _registerWpFunctionWrapper(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_,
            'tubepress_addons_wordpress_impl_DefaultWordPressFunctionWrapper'
        );
    }
}