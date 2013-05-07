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
class tubepress_addons_wordpress_impl_patterns_ioc_WordPressIocContainerExtension implements ehough_iconic_extension_ExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ehough_iconic_ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws ehough_iconic_exception_InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public final function load(array $config, ehough_iconic_ContainerBuilder $container)
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

        $container->register(

            'tubepress_impl_options_ui_tabs_GallerySourceTab',
            'tubepress_impl_options_ui_tabs_GallerySourceTab')
            ->addTag(tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME)
            ->addArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/gallery_source_tab.tpl.php');

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
                ->addArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tab.tpl.php');
        }

        $this->_registerListeners($container);
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

    private function _registerListeners(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator',
            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator'
        );

        $container->register(

            'tubepress_addons_wordpress_impl_listeners_boot_WordPressOptionsRegistrar',
            'tubepress_addons_wordpress_impl_listeners_boot_WordPressOptionsRegistrar'
        );

        $container->register(

            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        );
    }

    private function _registerMessageService(ehough_iconic_ContainerBuilder $container)
    {
        $definition = $container->register(

            tubepress_spi_message_MessageService::_,
            'tubepress_addons_wordpress_impl_message_WordPressMessageService'
        );

        $container->setDefinition('tubepress_addons_wordpress_impl_message_WordPressMessageService', $definition);
    }

    private function _registerOptionsStorageManager(ehough_iconic_ContainerBuilder $container)
    {
        $definition = $container->register(

            tubepress_spi_options_StorageManager::_,
            'tubepress_addons_wordpress_impl_options_WordPressStorageManager'
        );

        $container->setDefinition('tubepress_addons_wordpress_impl_options_WordPressStorageManager', $definition);
    }

    private function _registerOptionsUiFormHandler(ehough_iconic_ContainerBuilder $container)
    {
        $tabsId = 'tubepress_impl_options_ui_DefaultTabsHandler';

        $container->register(

            $tabsId, $tabsId

        )->addArgument(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tabs.tpl.php');

        $filterId = 'tubepress_impl_options_ui_fields_FilterMultiSelectField';

        $container->register($filterId, $filterId);

        $definition = $container->register(

            tubepress_spi_options_ui_FormHandler::_,
            'tubepress_impl_options_ui_DefaultFormHandler'

        )->addArgument(new ehough_iconic_Reference($tabsId))
         ->addArgument(new ehough_iconic_Reference($filterId))
         ->addArgument(TUBEPRESS_ROOT . '/src/main/php/addons/wordpress/resources/templates/options_page.tpl.php');

        $container->setDefinition('tubepress_impl_options_ui_DefaultFormHandler', $definition);
    }

    private function _registerContentFilter(ehough_iconic_ContainerBuilder $container)
    {
        $definition = $container->register(

            tubepress_addons_wordpress_spi_ContentFilter::_,
            'tubepress_addons_wordpress_impl_DefaultContentFilter'
        );

        $container->setDefinition('tubepress_addons_wordpress_impl_DefaultContentFilter', $definition);
    }

    private function _registerCssAndJsInjector(ehough_iconic_ContainerBuilder $container)
    {
        $definition = $container->register(

            tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector::_,
            'tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector'
        );

        $container->setDefinition('tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector', $definition);
    }

    private function _registerWidgetHandler(ehough_iconic_ContainerBuilder $container)
    {
        $definition = $container->register(

            tubepress_addons_wordpress_spi_WidgetHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWidgetHandler'
        );

        $container->setDefinition('tubepress_addons_wordpress_impl_DefaultWidgetHandler', $definition);
    }

    private function _registerWpAdminHandler(ehough_iconic_ContainerBuilder $container)
    {
        $definition = $container->register(

            tubepress_addons_wordpress_spi_WpAdminHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWpAdminHandler'
        );

        $container->setDefinition('tubepress_addons_wordpress_impl_DefaultWpAdminHandler', $definition);
    }

    private function _registerWpFunctionWrapper(ehough_iconic_ContainerBuilder $container)
    {
        $definition = $container->register(

            tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_,
            'tubepress_addons_wordpress_impl_DefaultWordPressFunctionWrapper'
        );

        $container->setDefinition('tubepress_addons_wordpress_impl_DefaultWordPressFunctionWrapper', $definition);
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     *
     * @api
     */
    public function getNamespace()
    {
        return null;
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     *
     * @api
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }
}