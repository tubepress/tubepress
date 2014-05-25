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
 * @covers tubepress_wordpress_impl_ioc_WordPressExtension<extended>
 */
class tubepress_test_wordpress_impl_ioc_WordPressIocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_wordpress_impl_ioc_WordPressExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_AdminEnqueueScripts',
            'tubepress_wordpress_impl_actions_AdminEnqueueScripts'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_enqueue_scripts',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_AdminHead',
            'tubepress_wordpress_impl_actions_AdminHead'
        )->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_head',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_AdminMenu',
            'tubepress_wordpress_impl_actions_AdminMenu'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_OptionsPage'))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_menu',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_AdminNotices',
            'tubepress_wordpress_impl_actions_AdminNotices'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_notices',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_Init',
            'tubepress_wordpress_impl_actions_Init'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_theme_ThemeLibraryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.init',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_WidgetsInit',
            'tubepress_wordpress_impl_actions_WidgetsInit'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_Widget'))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.widgets_init',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_WpHead',
            'tubepress_wordpress_impl_actions_WpHead'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_head',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_filters_Content',
            'tubepress_wordpress_impl_filters_Content'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_shortcode_ParserInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.the_content',
                'method'   => 'filter',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_filters_RowMeta',
            'tubepress_wordpress_impl_filters_RowMeta'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.plugin_row_meta',
                'method'   => 'filter',
                'priority' => 10000))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.plugin_action_links',
                'method'   => 'filter',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_html_CssJsDequerer',
            'tubepress_wordpress_impl_listeners_html_CssJsDequerer'
        )->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'  => tubepress_core_api_const_event_EventNames::CSS_JS_STYLESHEETS,
                'method' => 'onCss',
                'priority' => 10000))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event' => tubepress_core_api_const_event_EventNames::CSS_JS_SCRIPTS,
                'method' => 'onJs',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::OPTIONS_PAGE_TEMPLATE,
                'method'   => 'onOptionsUiTemplate',
                'priority' => 10000
            ));

        $this->expectRegistration(
            tubepress_core_api_translation_TranslatorInterface::_,
            'tubepress_wordpress_impl_message_WordPressMessageService'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_));

        $fieldIndex = 0;
        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_wordpress_impl_options_ui_fields_WpNonceField'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_));
        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_core_api_options_ui_FieldInterface'
        )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_core_api_const_options_Names::KEYWORD)
            ->withArgument('text');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('wordpress_field_' . $x);
        }

        $this->expectRegistration(
            'tubepress_wordpress_impl_options_ui_WpFieldProvider',
            'tubepress_wordpress_impl_options_ui_WpFieldProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
            ->withArgument($fieldReferences)
            ->withTag('tubepress_core_api_options_ui_FieldProviderInterface');

        $this->expectRegistration(
            tubepress_core_api_options_PersistenceBackendInterface::_,
            'tubepress_wordpress_impl_options_PersistenceBackend'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_));

        $this->expectRegistration(
            'tubepress_wordpress_impl_options_WordPressOptionProvider',
            'tubepress_wordpress_impl_options_WordPressOptionProvider'
        )->withTag(tubepress_core_api_options_EasyProviderInterface::_);

        $this->expectRegistration(
            'tubepress_wordpress_impl_ActivationHook',
            'tubepress_wordpress_impl_ActivationHook'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $this->expectRegistration(
            'tubepress_wordpress_impl_Callback',
            'tubepress_wordpress_impl_Callback'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_ActivationHook'));

        $this->expectRegistration(
            'tubepress_wordpress_impl_OptionsPage',
            'tubepress_wordpress_impl_OptionsPage'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FormInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_));

        $this->expectRegistration(

            'tubepress_wordpress_impl_Widget',
            'tubepress_wordpress_impl_Widget'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_shortcode_ParserInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_));

        $this->expectRegistration(

            tubepress_wordpress_spi_WpFunctionsInterface::_,
            'tubepress_wordpress_impl_WpFunctions'
        );

        $this->expectRegistration(

            'wordpress_optionsPage_template',
            'tubepress_core_api_template_TemplateInterface'
        )->withFactoryService(tubepress_core_api_template_TemplateFactoryInterface::_)
            ->withFactoryMethod('fromFilesystem')
            ->withArgument(array(TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/options_page.tpl.php'))
            ->withTag(tubepress_core_api_const_ioc_Tags::OPTIONS_PAGE_TEMPLATE);
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            'tubepress_wordpress_impl_actions_AdminEnqueueScripts' => 'tubepress_wordpress_impl_actions_AdminEnqueueScripts',
            'tubepress_wordpress_impl_actions_AdminHead' => 'tubepress_wordpress_impl_actions_AdminHead',
            'tubepress_wordpress_impl_actions_AdminMenu' => 'tubepress_wordpress_impl_actions_AdminMenu',
            'tubepress_wordpress_impl_actions_AdminNotices' => 'tubepress_wordpress_impl_actions_AdminNotices',
            'tubepress_wordpress_impl_actions_Init' => 'tubepress_wordpress_impl_actions_Init',
            'tubepress_wordpress_impl_actions_WidgetsInit' => 'tubepress_wordpress_impl_actions_WidgetsInit',
            'tubepress_wordpress_impl_actions_WpHead' => 'tubepress_wordpress_impl_actions_WpHead',
            'tubepress_wordpress_impl_filters_Content' => 'tubepress_wordpress_impl_filters_Content',
            'tubepress_wordpress_impl_filters_RowMeta' => 'tubepress_wordpress_impl_filters_RowMeta',
            'tubepress_wordpress_impl_listeners_html_CssJsDequerer' => 'tubepress_wordpress_impl_listeners_html_CssJsDequerer',
            'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener' => 'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            tubepress_core_api_translation_TranslatorInterface::_ => 'tubepress_wordpress_impl_message_WordPressMessageService',
            'wordpress_field_0' => 'tubepress_wordpress_impl_options_ui_fields_WpNonceField',
            'wordpress_field_1' => 'tubepress_core_api_options_ui_FieldInterface',
            'tubepress_wordpress_impl_options_ui_WpFieldProvider' => 'tubepress_wordpress_impl_options_ui_WpFieldProvider',
            tubepress_core_api_options_PersistenceBackendInterface::_ => 'tubepress_wordpress_impl_options_PersistenceBackend',
            'tubepress_wordpress_impl_options_WordPressOptionProvider' => 'tubepress_wordpress_impl_options_WordPressOptionProvider',
            'tubepress_wordpress_impl_ActivationHook' => 'tubepress_wordpress_impl_ActivationHook',
            'tubepress_wordpress_impl_Callback' => 'tubepress_wordpress_impl_Callback',
            'tubepress_wordpress_impl_OptionsPage' => 'tubepress_wordpress_impl_OptionsPage',
            'tubepress_wordpress_impl_Widget' => 'tubepress_wordpress_impl_Widget',
            tubepress_wordpress_spi_WpFunctionsInterface::_ => 'tubepress_wordpress_impl_WpFunctions',
            'wordpress_optionsPage_template' => 'tubepress_core_api_template_TemplateInterface',
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockFieldBuilder = $this->mock(tubepress_core_api_options_ui_FieldBuilderInterface::_);
        $mockField        = $this->mock('tubepress_core_api_options_ui_FieldInterface');
        $mockFieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockTemplateFactory = $this->mock(tubepress_core_api_template_TemplateFactoryInterface::_);
        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');
        $mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array(TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/options_page.tpl.php'))->andReturn($mockTemplate);

        return array(

            tubepress_core_api_options_ui_FormInterface::_ => tubepress_core_api_options_ui_FormInterface::_,
            tubepress_core_api_http_RequestParametersInterface::_ => tubepress_core_api_http_RequestParametersInterface::_,
            tubepress_core_api_url_UrlFactoryInterface::_ => tubepress_core_api_url_UrlFactoryInterface::_,
            tubepress_core_api_theme_ThemeLibraryInterface::_ => tubepress_core_api_theme_ThemeLibraryInterface::_,
            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_,
            tubepress_core_api_options_ContextInterface::_ => tubepress_core_api_options_ContextInterface::_,
            tubepress_core_api_options_PersistenceInterface::_ => tubepress_core_api_options_PersistenceInterface::_,
            tubepress_core_api_html_HtmlGeneratorInterface::_ => tubepress_core_api_html_HtmlGeneratorInterface::_,
            tubepress_core_api_shortcode_ParserInterface::_ => tubepress_core_api_shortcode_ParserInterface::_,
            tubepress_core_api_event_EventDispatcherInterface::_ => tubepress_core_api_event_EventDispatcherInterface::_,
            tubepress_core_api_template_TemplateFactoryInterface::_ => $mockTemplateFactory,
            tubepress_core_api_html_HtmlGeneratorInterface::_ => tubepress_core_api_html_HtmlGeneratorInterface::_,
            tubepress_core_api_environment_EnvironmentInterface::_ => tubepress_core_api_environment_EnvironmentInterface::_,
            tubepress_core_api_options_ui_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_api_boot_BootSettingsInterface::_ => tubepress_api_boot_BootSettingsInterface::_,
            'ehough_filesystem_FilesystemInterface' => 'ehough_filesystem_FilesystemInterface',
        );
    }
}