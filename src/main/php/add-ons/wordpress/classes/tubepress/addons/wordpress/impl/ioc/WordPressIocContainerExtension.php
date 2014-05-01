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
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder A tubepress_api_ioc_ContainerBuilderInterface instance.
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        /**
         * Core stuff.
         */
        $this->_registerMessageService($containerBuilder);
        $this->_registerOptionsPage($containerBuilder);
        $this->_registerOptionsStorageManager($containerBuilder);

        /**
         * WordPress specific stuff.
         */
        $this->_registerActions($containerBuilder);
        $this->_registerFilters($containerBuilder);
        $this->_registerWpFunctionWrapper($containerBuilder);
        $this->_registerWpOptionsPage($containerBuilder);
        $this->_registerWidget($containerBuilder);
        $this->_registerActivator($containerBuilder);
        $this->_registerCallback($containerBuilder);

        $this->_registerPluggables($containerBuilder);
        $this->_registerListeners($containerBuilder);
    }

    private function _registerCallback(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_wordpress_impl_Callback',
            'tubepress_addons_wordpress_impl_Callback'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_));
    }

    private function _registerPluggables(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_wordpress_impl_options_WordPressOptionProvider',
            'tubepress_addons_wordpress_impl_options_WordPressOptionProvider'
        )->addTag(tubepress_spi_options_OptionProvider::_);

        $this->_registerOptionsPageParticipant($containerBuilder);
    }

    private function _registerOptionsPageParticipant(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldIndex = 0;
        $containerBuilder->register('wordpress_options_field_' . $fieldIndex++, 'tubepress_addons_wordpress_impl_options_ui_fields_WpNonceField');
        $containerBuilder->register('wordpress_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
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

        $containerBuilder->register(

            'wordpress_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->addArgument('wordpress_participant')
            ->addArgument('WordPress')   //>(translatable)<
            ->addArgument(array())
            ->addArgument($fieldReferences)
            ->addArgument($map)
            ->addArgument(false)
            ->addArgument(false)
            ->addTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::OPTIONS_PAGE_TEMPLATE,
                'method' => 'onOptionsUiTemplate', 'priority' => 10000));

        $containerBuilder->register(

            'tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer',
            'tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_STYLESHEETS,
                'method' => 'onCss', 'priority' => 10000))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_SCRIPTS,
                'method' => 'onJs', 'priority' => 10000));
    }

    private function _registerMessageService(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_message_MessageService::_,
            'tubepress_addons_wordpress_impl_message_WordPressMessageService'
        );
    }

    private function _registerOptionsStorageManager(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_options_StorageManager::_,
            'tubepress_addons_wordpress_impl_options_WordPressStorageManager'
        );
    }

    private function _registerOptionsPage(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_spi_options_ui_OptionsPageInterface',
            'tubepress_impl_options_ui_DefaultOptionsPage'

        )->addArgument(TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/options_page.tpl.php')
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => 'tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface',
                'method' => 'setOptionsPageParticipants'));
    }

    private function _registerWpOptionsPage(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'wordpress.optionsPage',
            'tubepress_addons_wordpress_impl_OptionsPage'
        );
    }

    private function _registerWidget(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'wordpress.widget',
            'tubepress_addons_wordpress_impl_Widget'
        );
    }

    private function _registerWpFunctionWrapper(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_addons_wordpress_spi_WpFunctionsInterface::_,
            'tubepress_addons_wordpress_impl_WpFunctions'
        );
    }

    private function _registerFilters(tubepress_api_ioc_ContainerBuilderInterface $builder)
    {
        $map = array(

            'the_content'         => 'Content',
            'plugin_row_meta'     => 'RowMeta',
            'plugin_action_links' => 'RowMeta',
        );

        foreach ($map as $filterName => $classSuffix) {

            $builder->register(

                "wordpress.filter.$filterName.$classSuffix",
                "tubepress_addons_wordpress_impl_filters_$classSuffix"

            )->addTag(self::TAG_EVENT_LISTENER, array(

                'event'    => "tubepress.wordpress.filter.$filterName",
                'method'   => 'filter',
                'priority' => 10000
            ));
        }
    }

    private function _registerActions(tubepress_api_ioc_ContainerBuilderInterface $builder)
    {
        $map = array(

            'admin_enqueue_scripts' => 'AdminEnqueueScripts',
            'admin_head'            => 'AdminHead',
            'admin_menu'            => 'AdminMenu',
            'init'                  => 'Init',
            'widgets_init'          => 'WidgetsInit',
            'wp_head'               => 'WpHead',
        );

        foreach ($map as $actionName => $classSuffix) {

            $builder->register(

                "wordpress.action.$actionName.$classSuffix",
                "tubepress_addons_wordpress_impl_actions_$classSuffix"

            )->addTag(self::TAG_EVENT_LISTENER, array(

                'event'    => "tubepress.wordpress.action.$actionName",
                'method'   => "action",
                'priority' => 10000
            ));
        }

        $builder->register(

            'wordpress.action.admin_notices',
            'tubepress_addons_wordpress_impl_actions_AdminNotices'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_CurrentUrlServiceInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array(

            'event'    => "tubepress.wordpress.action.admin_notices",
            'method'   => "action",
            'priority' => 10000
        ));
    }

    private function _registerActivator(tubepress_api_ioc_ContainerBuilderInterface $builder)
    {
        $builder->register(

            'wordpress.pluginActivator',
            'tubepress_addons_wordpress_impl_ActivationHook'
        );
    }
}