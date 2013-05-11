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
            ->withArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/gallery_source_tab.tpl.php');

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
                ->withArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tab.tpl.php');
        }

        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator',
            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::EVENT_LISTENER_TAG, array('event' => tubepress_api_const_event_EventNames::BOOT_COMPLETE, 'method' => 'onBoot'));;

        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_boot_WordPressOptionsRegistrar',
            'tubepress_addons_wordpress_impl_listeners_boot_WordPressOptionsRegistrar'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::EVENT_LISTENER_TAG, array('event' => tubepress_api_const_event_EventNames::BOOT_COMPLETE, 'method' => 'onBoot'));;

        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::EVENT_LISTENER_TAG, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_OPTIONS_UI_MAIN, 'method' => 'onOptionsUiTemplate'));;

        $definition = $this->expectRegistration(

            tubepress_spi_message_MessageService::_,
            'tubepress_addons_wordpress_impl_message_WordPressMessageService'
        )->andReturnDefinition();

        $this->expectDefinition('tubepress_addons_wordpress_impl_message_WordPressMessageService', $definition);

        $definition = $this->expectRegistration(

            tubepress_spi_options_StorageManager::_,
            'tubepress_addons_wordpress_impl_options_WordPressStorageManager'
        )->andReturnDefinition();

        $this->expectDefinition('tubepress_addons_wordpress_impl_options_WordPressStorageManager', $definition);

        $tabsId = 'tubepress_impl_options_ui_DefaultTabsHandler';

        $this->expectRegistration(

            $tabsId, $tabsId

        )->withArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tabs.tpl.php');

        $filterId = 'tubepress_impl_options_ui_fields_FilterMultiSelectField';

        $this->expectRegistration($filterId, $filterId);

        $definition = $this->expectRegistration(

            tubepress_spi_options_ui_FormHandler::_,
            'tubepress_impl_options_ui_DefaultFormHandler'

        )->withArgument(new ehough_iconic_Reference($tabsId))
            ->withArgument(new ehough_iconic_Reference($filterId))
            ->withArgument(TUBEPRESS_ROOT . '/src/main/php/addons/wordpress/resources/templates/options_page.tpl.php')
            ->andReturnDefinition();

        $this->expectDefinition('tubepress_impl_options_ui_DefaultFormHandler', $definition);

        $definition = $this->expectRegistration(

            tubepress_addons_wordpress_spi_ContentFilter::_,
            'tubepress_addons_wordpress_impl_DefaultContentFilter'
        )->andReturnDefinition();

        $this->expectDefinition('tubepress_addons_wordpress_impl_DefaultContentFilter', $definition);

        $definition = $this->expectRegistration(

            tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector::_,
            'tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector'
        )->andReturnDefinition();

        $this->expectDefinition('tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector', $definition);

        $definition = $this->expectRegistration(

            tubepress_addons_wordpress_spi_WidgetHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWidgetHandler'
        )->andReturnDefinition();

        $this->expectDefinition('tubepress_addons_wordpress_impl_DefaultWidgetHandler', $definition);

        $definition = $this->expectRegistration(

            tubepress_addons_wordpress_spi_WpAdminHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWpAdminHandler'
        )->andReturnDefinition();

        $this->expectDefinition('tubepress_addons_wordpress_impl_DefaultWpAdminHandler', $definition);

        $definition = $this->expectRegistration(

            tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_,
            'tubepress_addons_wordpress_impl_DefaultWordPressFunctionWrapper'
        )->andReturnDefinition();

        $this->expectDefinition('tubepress_addons_wordpress_impl_DefaultWordPressFunctionWrapper', $definition);
    }
}