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
 * @runTestsInSeparateProcess
 * @preserveGlobalState disabled
 * @covers tubepress_wordpress_ioc_WordPressExtension<extended>
 */
class tubepress_test_wordpress_ioc_WordPressIocContainerExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_platform_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_wordpress_ioc_WordPressExtension();
    }

    protected function prepareForLoad()
    {
        define('ABSPATH', 'foo');

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters',
            'tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_enqueue_scripts',
                'method'   => 'onAction_admin_enqueue_scripts',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_head',
                'method'   => 'onAction_admin_head',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_menu',
                'method'   => 'onAction_admin_menu',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_notices',
                'method'   => 'onAction_admin_notices',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.plugin_row_meta',
                'method'   => 'onFilter_row_meta',
                'priority' => 10000))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.plugin_action_links',
                'method'   => 'onFilter_row_meta',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters',
            'tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_theme_api_ThemeLibraryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_html_api_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_AjaxCommandInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_shortcode_api_ParserInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_ajax_nopriv_tubepress',
                'method'   => 'onAction_ajax',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_ajax_tubepress',
                'method'   => 'onAction_ajax',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.init',
                'method'   => 'onAction_init',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.widgets_init',
                'method'   => 'onAction_widgets_init',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_head',
                'method'   => 'onAction_wp_head',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.the_content',
                'method'   => 'onFilter_the_content',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_html_WpHtmlListener',
            'tubepress_wordpress_impl_listeners_html_WpHtmlListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_util_api_UrlUtilsInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'  => tubepress_app_html_api_Constants::EVENT_STYLESHEETS,
                'method' => 'onCss',
                'priority' => 10000))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event' => tubepress_app_html_api_Constants::EVENT_SCRIPTS,
                'method' => 'onJs',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_html_api_Constants::EVENT_GLOBAL_JS_CONFIG,
                'method'   => 'onGlobalJsConfig',
                'priority' => 10000,
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_ui_api_Constants::EVENT_OPTIONS_UI_PAGE_TEMPLATE,
                'method'   => 'onOptionsUiTemplate',
                'priority' => 10000
            ));

        $this->expectRegistration(
            tubepress_lib_translation_api_TranslatorInterface::_,
            'tubepress_wordpress_impl_message_WordPressMessageService'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));

        $fieldIndex = 0;
        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_wordpress_impl_options_ui_fields_WpNonceField'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));
        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_app_options_ui_api_FieldInterface'
        )->withFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_app_shortcode_api_Constants::OPTION_KEYWORD)
            ->withArgument('text');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('wordpress_field_' . $x);
        }

        $this->expectRegistration(
            'tubepress_wordpress_impl_options_ui_WpFieldProvider',
            'tubepress_wordpress_impl_options_ui_WpFieldProvider'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withArgument($fieldReferences)
            ->withTag('tubepress_app_options_ui_api_FieldProviderInterface');

        $this->expectRegistration(
            tubepress_app_options_api_PersistenceBackendInterface::_,
            'tubepress_wordpress_impl_options_PersistenceBackend'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));

        $this->expectRegistration(
            'tubepress_wordpress_impl_wp_ActivationHook',
            'tubepress_wordpress_impl_wp_ActivationHook'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $this->expectRegistration(
            'tubepress_wordpress_impl_Callback',
            'tubepress_wordpress_impl_Callback'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('tubepress_wordpress_impl_wp_ActivationHook'));

        $this->expectRegistration(
            'tubepress_wordpress_impl_wp_OptionsPage',
            'tubepress_wordpress_impl_wp_OptionsPage'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_ui_api_FormInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_OPTIONS_PAGE_INVOKED,
                'method'   => 'run',
                'priority' => 20000
            ));

        $this->expectRegistration(

            'tubepress_wordpress_impl_wp_Widget',
            'tubepress_wordpress_impl_wp_Widget'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_html_api_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_shortcode_api_ParserInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_WIDGET_PUBLIC_HTML,
                'method'   => 'printWidgetHtml',
                'priority' => 20000))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_WIDGET_PRINT_CONTROLS,
                'method'   => 'printControlHtml',
                'priority' => 20000
            ));

        $this->expectRegistration(
            tubepress_wordpress_impl_wp_WpFunctions::_,
            'tubepress_wordpress_impl_wp_WpFunctions'
        );

        $this->expectRegistration(
            'wordpress_optionsPage_template',
            'tubepress_lib_template_api_TemplateInterface'
        )->withFactoryService(tubepress_lib_template_api_TemplateFactoryInterface::_)
            ->withFactoryMethod('fromFilesystem')
            ->withArgument(array(TUBEPRESS_ROOT . '/src/core/integration/wordpress/resources/templates/options_page.tpl.php'))
            ->withTag(tubepress_app_options_ui_api_Constants::IOC_TAG_OPTIONS_PAGE_TEMPLATE);

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_wordpress', array(

            'defaultValues' => array(
                tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE     => 'TubePress',
                tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']'
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockFieldBuilder = $this->mock(tubepress_app_options_ui_api_FieldBuilderInterface::_);
        $mockField        = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $mockFieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockTemplateFactory = $this->mock(tubepress_lib_template_api_TemplateFactoryInterface::_);
        $mockTemplate = $this->mock('tubepress_lib_template_api_TemplateInterface');
        $mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array(TUBEPRESS_ROOT . '/src/core/integration/wordpress/resources/templates/options_page.tpl.php'))->andReturn($mockTemplate);

        return array(

            tubepress_app_options_ui_api_FormInterface::_ => tubepress_app_options_ui_api_FormInterface::_,
            tubepress_app_http_api_RequestParametersInterface::_ => tubepress_app_http_api_RequestParametersInterface::_,
            tubepress_lib_url_api_UrlFactoryInterface::_ => tubepress_lib_url_api_UrlFactoryInterface::_,
            tubepress_app_theme_api_ThemeLibraryInterface::_ => tubepress_app_theme_api_ThemeLibraryInterface::_,
            tubepress_platform_api_util_StringUtilsInterface::_ => tubepress_platform_api_util_StringUtilsInterface::_,
            tubepress_app_options_api_ContextInterface::_ => tubepress_app_options_api_ContextInterface::_,
            tubepress_app_options_api_PersistenceInterface::_ => tubepress_app_options_api_PersistenceInterface::_,
            tubepress_app_html_api_HtmlGeneratorInterface::_ => tubepress_app_html_api_HtmlGeneratorInterface::_,
            tubepress_app_shortcode_api_ParserInterface::_ => tubepress_app_shortcode_api_ParserInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_ => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_lib_template_api_TemplateFactoryInterface::_ => $mockTemplateFactory,
            tubepress_app_html_api_HtmlGeneratorInterface::_ => tubepress_app_html_api_HtmlGeneratorInterface::_,
            tubepress_app_environment_api_EnvironmentInterface::_ => tubepress_app_environment_api_EnvironmentInterface::_,
            tubepress_app_options_ui_api_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_platform_api_boot_BootSettingsInterface::_ => tubepress_platform_api_boot_BootSettingsInterface::_,
            'ehough_filesystem_FilesystemInterface' => 'ehough_filesystem_FilesystemInterface',
            tubepress_app_http_api_AjaxCommandInterface::_ => tubepress_app_http_api_AjaxCommandInterface::_,
            tubepress_lib_util_api_UrlUtilsInterface::_ => tubepress_lib_util_api_UrlUtilsInterface::_
        );
    }
}