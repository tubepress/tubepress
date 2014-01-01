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
        $this->_registerOptionsPage($container);
        $this->_registerOptionsStorageManager($container);

        /**
         * WordPress specific stuff.
         */
        $this->_registerContentFilter($container);
        $this->_registerCssAndJsInjector($container);
        $this->_registerWidgetHandler($container);
        $this->_registerWpAdminHandler($container);
        $this->_registerWpFunctionWrapper($container);

        $this->_registerPluggables($container);
        $this->_registerListeners($container);
    }

    private function _registerPluggables(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_addons_wordpress_impl_options_WordPressOptionsProvider',
            'tubepress_addons_wordpress_impl_options_WordPressOptionsProvider'
        )->addTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $this->_registerOptionsPageParticipant($container);
    }

    private function _registerOptionsPageParticipant(tubepress_api_ioc_ContainerInterface $container)
    {
        $fieldIndex = 0;
        $container->register('wordpress_options_field_' . $fieldIndex++, 'tubepress_addons_wordpress_impl_options_ui_fields_WpNonceField');
        $container->register('wordpress_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
            ->addArgument(tubepress_api_const_options_names_Advanced::KEYWORD);

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_impl_ioc_Reference('wordpress_options_field_' . $x);
        }

        $map = array(

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_ADVANCED => array(

                tubepress_api_const_options_names_Advanced::KEYWORD
            )
        );

        $container->register(

            'wordpress_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->addArgument('wordpress_participant')
            ->addArgument('WordPress')   //>(translatable)<
            ->addArgument(array())
            ->addArgument($fieldReferences)
            ->addArgument($map)
            ->addTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _registerListeners(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator',
            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::BOOT_COMPLETE, 'method' => 'onBoot', 'priority' => 10000));

        $container->register(

            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::OPTIONS_PAGE_TEMPLATE,
                'method' => 'onOptionsUiTemplate', 'priority' => 10000));

        $container->register(

            'tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer',
            'tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_STYLESHEETS,
                'method' => 'onCss', 'priority' => 10000))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_SCRIPTS,
                'method' => 'onJs', 'priority' => 10000));
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

    private function _registerOptionsPage(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_spi_options_ui_OptionsPageInterface',
            'tubepress_impl_options_ui_DefaultOptionsPage'

        )->addArgument(TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/options_page.tpl.php')
         ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => 'tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface',
                'method' => 'setOptionsPageParticipants'));
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