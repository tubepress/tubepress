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
class tubepress_test_wordpress_ioc_WordPressIocContainerExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_wordpress_ioc_WordPressExtension();
    }

    protected function prepareForLoad()
    {
        define('ABSPATH', 'foo');

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_AdminEnqueueScripts',
            'tubepress_wordpress_impl_actions_AdminEnqueueScripts'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_enqueue_scripts',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_AdminHead',
            'tubepress_wordpress_impl_actions_AdminHead'
        )->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_head',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_AdminMenu',
            'tubepress_wordpress_impl_actions_AdminMenu'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_OptionsPage'))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_menu',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_AdminNotices',
            'tubepress_wordpress_impl_actions_AdminNotices'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_notices',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_Init',
            'tubepress_wordpress_impl_actions_Init'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_theme_api_ThemeLibraryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.init',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_WidgetsInit',
            'tubepress_wordpress_impl_actions_WidgetsInit'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_Widget'))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.widgets_init',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_actions_WpHead',
            'tubepress_wordpress_impl_actions_WpHead'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_html_api_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_head',
                'method'   => 'action',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_filters_Content',
            'tubepress_wordpress_impl_filters_Content'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_html_api_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_shortcode_api_ParserInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.the_content',
                'method'   => 'filter',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_filters_RowMeta',
            'tubepress_wordpress_impl_filters_RowMeta'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.plugin_row_meta',
                'method'   => 'filter',
                'priority' => 10000))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.plugin_action_links',
                'method'   => 'filter',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_html_CssJsDequerer',
            'tubepress_wordpress_impl_listeners_html_CssJsDequerer'
        )->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'  => tubepress_core_html_api_Constants::EVENT_STYLESHEETS,
                'method' => 'onCss',
                'priority' => 10000))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event' => tubepress_core_html_api_Constants::EVENT_SCRIPTS,
                'method' => 'onJs',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_environment_api_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_ui_api_Constants::EVENT_OPTIONS_UI_PAGE_TEMPLATE,
                'method'   => 'onOptionsUiTemplate',
                'priority' => 10000
            ));

        $this->expectRegistration(
            tubepress_core_translation_api_TranslatorInterface::_,
            'tubepress_wordpress_impl_message_WordPressMessageService'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_));

        $fieldIndex = 0;
        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_wordpress_impl_options_ui_fields_WpNonceField'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_));
        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_core_shortcode_api_Constants::OPTION_KEYWORD)
            ->withArgument('text');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('wordpress_field_' . $x);
        }

        $this->expectRegistration(
            'tubepress_wordpress_impl_options_ui_WpFieldProvider',
            'tubepress_wordpress_impl_options_ui_WpFieldProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument($fieldReferences)
            ->withTag('tubepress_core_options_ui_api_FieldProviderInterface');

        $this->expectRegistration(
            tubepress_core_options_api_PersistenceBackendInterface::_,
            'tubepress_wordpress_impl_options_PersistenceBackend'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_));

        $this->expectRegistration(
            'tubepress_wordpress_impl_ActivationHook',
            'tubepress_wordpress_impl_ActivationHook'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $this->expectRegistration(
            'tubepress_wordpress_impl_Callback',
            'tubepress_wordpress_impl_Callback'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_environment_api_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_ActivationHook'));

        $this->expectRegistration(
            'tubepress_wordpress_impl_OptionsPage',
            'tubepress_wordpress_impl_OptionsPage'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_ui_api_FormInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_));

        $this->expectRegistration(

            'tubepress_wordpress_impl_Widget',
            'tubepress_wordpress_impl_Widget'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_html_api_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_shortcode_api_ParserInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_spi_WpFunctionsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_));

        $this->expectRegistration(
            tubepress_wordpress_spi_WpFunctionsInterface::_,
            'tubepress_wordpress_impl_WpFunctions'
        );

        $this->expectRegistration(
            'wordpress_optionsPage_template',
            'tubepress_core_template_api_TemplateInterface'
        )->withFactoryService(tubepress_core_template_api_TemplateFactoryInterface::_)
            ->withFactoryMethod('fromFilesystem')
            ->withArgument(array(TUBEPRESS_ROOT . '/src/core/wordpress/resources/templates/options_page.tpl.php'))
            ->withTag(tubepress_core_options_ui_api_Constants::IOC_TAG_OPTIONS_PAGE_TEMPLATE);

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE, array(

            'defaultValues' => array(
                tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE     => 'TubePress',
                tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']'
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockFieldBuilder = $this->mock(tubepress_core_options_ui_api_FieldBuilderInterface::_);
        $mockField        = $this->mock('tubepress_core_options_ui_api_FieldInterface');
        $mockFieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockTemplateFactory = $this->mock(tubepress_core_template_api_TemplateFactoryInterface::_);
        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array(TUBEPRESS_ROOT . '/src/core/wordpress/resources/templates/options_page.tpl.php'))->andReturn($mockTemplate);

        return array(

            tubepress_core_options_ui_api_FormInterface::_ => tubepress_core_options_ui_api_FormInterface::_,
            tubepress_core_http_api_RequestParametersInterface::_ => tubepress_core_http_api_RequestParametersInterface::_,
            tubepress_core_url_api_UrlFactoryInterface::_ => tubepress_core_url_api_UrlFactoryInterface::_,
            tubepress_core_theme_api_ThemeLibraryInterface::_ => tubepress_core_theme_api_ThemeLibraryInterface::_,
            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_,
            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_options_api_PersistenceInterface::_ => tubepress_core_options_api_PersistenceInterface::_,
            tubepress_core_html_api_HtmlGeneratorInterface::_ => tubepress_core_html_api_HtmlGeneratorInterface::_,
            tubepress_core_shortcode_api_ParserInterface::_ => tubepress_core_shortcode_api_ParserInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => $mockTemplateFactory,
            tubepress_core_html_api_HtmlGeneratorInterface::_ => tubepress_core_html_api_HtmlGeneratorInterface::_,
            tubepress_core_environment_api_EnvironmentInterface::_ => tubepress_core_environment_api_EnvironmentInterface::_,
            tubepress_core_options_ui_api_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_api_boot_BootSettingsInterface::_ => tubepress_api_boot_BootSettingsInterface::_,
            'ehough_filesystem_FilesystemInterface' => 'ehough_filesystem_FilesystemInterface',
        );
    }
}