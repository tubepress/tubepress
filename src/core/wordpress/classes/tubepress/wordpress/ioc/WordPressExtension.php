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
class tubepress_wordpress_ioc_WordPressExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
        if (!defined('ABSPATH')) {

            /**
             * Skip all this if we're not in WP.
             */
            return;
        }

        $containerBuilder->register(
            'tubepress_wordpress_impl_actions_AdminEnqueueScripts',
            'tubepress_wordpress_impl_actions_AdminEnqueueScripts'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.admin_enqueue_scripts',
            'method'   => 'action',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_actions_AdminHead',
            'tubepress_wordpress_impl_actions_AdminHead'
        )->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.admin_head',
            'method'   => 'action',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_actions_AdminMenu',
            'tubepress_wordpress_impl_actions_AdminMenu'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_OptionsPage'))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.admin_menu',
            'method'   => 'action',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_actions_AdminNotices',
            'tubepress_wordpress_impl_actions_AdminNotices'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.admin_notices',
            'method'   => 'action',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_actions_Ajax',
            'tubepress_wordpress_impl_actions_Ajax'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_AjaxCommandInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.wp_ajax_nopriv_tubepress',
            'method'   => 'action',
            'priority' => 10000
        ))->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_NVP_READ_FROM_EXTERNAL_INPUT . '.action',
            'method'   => 'onReadActionFromExternalInput',
            'priority' => 9000
         ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_actions_Init',
            'tubepress_wordpress_impl_actions_Init'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_theme_api_ThemeLibraryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.init',
            'method'   => 'action',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_actions_WidgetsInit',
            'tubepress_wordpress_impl_actions_WidgetsInit'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_Widget'))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.widgets_init',
            'method'   => 'action',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_actions_WpHead',
            'tubepress_wordpress_impl_actions_WpHead'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_html_api_HtmlGeneratorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.wp_head',
            'method'   => 'action',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_filters_Content',
            'tubepress_wordpress_impl_filters_Content'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_html_api_HtmlGeneratorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_shortcode_api_ParserInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.filter.the_content',
            'method'   => 'filter',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_filters_RowMeta',
            'tubepress_wordpress_impl_filters_RowMeta'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.filter.plugin_row_meta',
            'method'   => 'filter',
            'priority' => 10000))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.filter.plugin_action_links',
            'method'   => 'filter',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_listeners_html_CssJsDequerer',
            'tubepress_wordpress_impl_listeners_html_CssJsDequerer'
        )->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'  => tubepress_core_html_api_Constants::EVENT_STYLESHEETS,
                'method' => 'onCss',
                'priority' => 10000))
            ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event' => tubepress_core_html_api_Constants::EVENT_SCRIPTS,
                'method' => 'onJs',
                'priority' => 10000
            ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_listeners_html_GlobalJsConfig',
            'tubepress_wordpress_impl_listeners_html_GlobalJsConfig'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_html_api_Constants::EVENT_GLOBAL_JS_CONFIG,
            'method'   => 'onConfig',
            'priority' => 10000,
         ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_environment_api_EnvironmentInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_ui_api_Constants::EVENT_OPTIONS_UI_PAGE_TEMPLATE,
                'method'   => 'onOptionsUiTemplate',
                'priority' => 10000
            ));

        $containerBuilder->register(
            tubepress_core_translation_api_TranslatorInterface::_,
            'tubepress_wordpress_impl_message_WordPressMessageService'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_));

        $fieldIndex = 0;
        $containerBuilder->register(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_wordpress_impl_options_ui_fields_WpNonceField'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_));
        $containerBuilder->register(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_core_shortcode_api_Constants::OPTION_KEYWORD)
         ->addArgument('text');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('wordpress_field_' . $x);
        }

        $containerBuilder->register(
            'tubepress_wordpress_impl_options_ui_WpFieldProvider',
            'tubepress_wordpress_impl_options_ui_WpFieldProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
         ->addArgument($fieldReferences)
         ->addTag('tubepress_core_options_ui_api_FieldProviderInterface');

        $containerBuilder->register(
            tubepress_core_options_api_PersistenceBackendInterface::_,
            'tubepress_wordpress_impl_options_PersistenceBackend'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_));

        $containerBuilder->register(
            'tubepress_wordpress_impl_ActivationHook',
            'tubepress_wordpress_impl_ActivationHook'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $containerBuilder->register(
            'tubepress_wordpress_impl_Callback',
            'tubepress_wordpress_impl_Callback'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_environment_api_EnvironmentInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_ActivationHook'));

        $containerBuilder->register(
            'tubepress_wordpress_impl_OptionsPage',
            'tubepress_wordpress_impl_OptionsPage'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_ui_api_FormInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_));

        $containerBuilder->register(

            'tubepress_wordpress_impl_Widget',
            'tubepress_wordpress_impl_Widget'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_html_api_HtmlGeneratorInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_shortcode_api_ParserInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_));

        $containerBuilder->register(
            tubepress_wordpress_spi_WpFunctionsInterface::_,
            'tubepress_wordpress_impl_WpFunctions'
        );

        $containerBuilder->register(
            'wordpress_optionsPage_template',
            'tubepress_core_template_api_TemplateInterface'
        )->setFactoryService(tubepress_core_template_api_TemplateFactoryInterface::_)
         ->setFactoryMethod('fromFilesystem')
         ->addArgument(array(TUBEPRESS_ROOT . '/src/core/wordpress/resources/templates/options_page.tpl.php'))
         ->addTag(tubepress_core_options_ui_api_Constants::IOC_TAG_OPTIONS_PAGE_TEMPLATE);

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE, array(

            'defaultValues' => array(
                tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE     => 'TubePress',
                tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']'
            )
        ));
    }
}