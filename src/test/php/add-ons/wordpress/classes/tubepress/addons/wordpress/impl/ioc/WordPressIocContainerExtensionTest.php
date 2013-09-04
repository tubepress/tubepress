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
class tubepress_addons_wordpress_impl_ioc_WordPressIocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_addons_wordpress_impl_ioc_WordPressIocContainerExtension();
    }

    protected function prepareForLoad()
    {
        $envDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $envDetector->shouldReceive('isWordPress')->once()->andReturn(true);

        $this->expectRegistration(

            'tubepress_impl_options_ui_tabs_GallerySourceTab',
            'tubepress_impl_options_ui_tabs_GallerySourceTab')
            ->withTag(tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME)
            ->withArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/gallery_source_tab.tpl.php')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => 'tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface', 'method' => 'setPluggableOptionsPageParticipants'));


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

            $this->expectRegistration($tab, $tab)
                ->withTag(tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME)
                ->withArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tab.tpl.php')
                ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => 'tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface', 'method' => 'setPluggableOptionsPageParticipants'));
        }

        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator',
            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => tubepress_api_const_event_EventNames::BOOT_COMPLETE, 'method' => 'onBoot', 'priority' => 10000));;

        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_options_WordPressOptionsProvider',
            'tubepress_addons_wordpress_impl_options_WordPressOptionsProvider'
        )->withTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_OPTIONS_UI_MAIN, 'method' => 'onOptionsUiTemplate', 'priority' => 10000));;

        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_cssjs_BaseUrlSetter',
            'tubepress_addons_wordpress_impl_listeners_cssjs_BaseUrlSetter'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => tubepress_api_const_event_EventNames::CSS_JS_GLOBAL_JS_CONFIG, 'method' => 'onJsConfig', 'priority' => 10000));

        $this->expectRegistration(

            tubepress_spi_message_MessageService::_,
            'tubepress_addons_wordpress_impl_message_WordPressMessageService'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_spi_options_StorageManager::_,
            'tubepress_addons_wordpress_impl_options_WordPressStorageManager'
        )->andReturnDefinition();

        $tabsId = 'tubepress_impl_options_ui_DefaultTabsHandler';

        $this->expectRegistration(

            $tabsId, $tabsId

        )->withArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tabs.tpl.php')
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME, 'method' => 'setPluggableOptionsPageTabs'));

        $filterId = 'tubepress_impl_options_ui_fields_FilterMultiSelectField';

        $this->expectRegistration($filterId, $filterId)
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => 'tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface', 'method' => 'setPluggableOptionsPageParticipants'));


        $this->expectRegistration(

            tubepress_spi_options_ui_FormHandler::_,
            'tubepress_impl_options_ui_DefaultFormHandler'

        )->withArgument(new ehough_iconic_Reference($tabsId))
            ->withArgument(new ehough_iconic_Reference($filterId))
            ->withArgument(TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/options_page.tpl.php')
            ->andReturnDefinition();

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_ContentFilter::_,
            'tubepress_addons_wordpress_impl_DefaultContentFilter'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector::_,
            'tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_WidgetHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWidgetHandler'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_WpAdminHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWpAdminHandler'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_,
            'tubepress_addons_wordpress_impl_DefaultWordPressFunctionWrapper'
        )->andReturnDefinition();
    }
}