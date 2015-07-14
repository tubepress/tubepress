<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_test_wordpress_ioc_WordPressExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
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

        $this->_registerListeners();
        $this->_registerOptions();
        $this->_registerOptionsUi();
        $this->_registerSingletons();
        $this->_registerWpServices();
        $this->_registerVendorServices();
    }

    private function _registerOptions()
    {
        $this->expectRegistration('tubepress_app_api_options_Reference__wordpress', 'tubepress_app_api_options_Reference')->withArgument(array(tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE => 'TubePress', tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']', tubepress_app_api_options_Names::SHORTCODE_KEYWORD => 'tubepress',), tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(tubepress_app_api_options_Names::SHORTCODE_KEYWORD => 'Shortcode keyword',  //>(translatable)<
        ),

            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(tubepress_app_api_options_Names::SHORTCODE_KEYWORD => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', //>(translatable)<,

            ),))->withTag(tubepress_app_api_options_ReferenceInterface::_);

        $toValidate = array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(tubepress_app_api_options_Names::SHORTCODE_KEYWORD,),);

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $this->expectRegistration('regex_validator.' . $optionName, 'tubepress_app_api_listeners_options_RegexValidatingListener')->withArgument($type)->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array('event' => tubepress_app_api_event_Events::OPTION_SET . ".$optionName", 'priority' => 100000, 'method' => 'onOption',));
            }
        }
    }

    private function _registerOptionsUi()
    {
        $fieldIndex = 0;
        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_wordpress_impl_options_ui_fields_WpNonceField'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));
        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_app_api_options_ui_FieldInterface'
        )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
         ->withFactoryMethod('newInstance')
         ->withArgument(tubepress_app_api_options_Names::SHORTCODE_KEYWORD)
         ->withArgument('text');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('wordpress_field_' . $x);
        }

        $this->expectRegistration(
            'tubepress_wordpress_impl_options_ui_WpFieldProvider',
            'tubepress_wordpress_impl_options_ui_WpFieldProvider'
        )->withArgument($fieldReferences)
         ->withTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters',
            'tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ui_FormInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_enqueue_scripts',
                'method'   => 'onAction_admin_enqueue_scripts',
                'priority' => 100000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_head',
                'method'   => 'onAction_admin_head',
                'priority' => 100000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_menu',
                'method'   => 'onAction_admin_menu',
                'priority' => 100000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_notices',
                'method'   => 'onAction_admin_notices',
                'priority' => 100000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.plugin_row_meta',
                'method'   => 'onFilter_row_meta',
                'priority' => 100000))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.plugin_action_links',
                'method'   => 'onFilter_row_meta',
                'priority' => 100000
            ))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.puc_request_info_query_args-tubepress',
                'method'   => 'onFilter_PucRequestInfoQueryArgsTubePress',
                'priority' => 100000))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.puc_request_info_result-tubepress',
                'method'   => 'onFilter_PucRequestInfoResultTubePress',
                'priority' => 100000));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters',
            'tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_AjaxInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_ajax_nopriv_tubepress',
                'method'   => 'onAction_ajax',
                'priority' => 100000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_ajax_tubepress',
                'method'   => 'onAction_ajax',
                'priority' => 100000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.init',
                'method'   => 'onAction_init',
                'priority' => 100000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.widgets_init',
                'method'   => 'onAction_widgets_init',
                'priority' => 100000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_head',
                'method'   => 'onAction_wp_head',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_html_WpHtmlListener',
            'tubepress_wordpress_impl_listeners_html_WpHtmlListener'
        )->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . ".cssjs/scripts",
                'method'   => 'onScriptsStylesTemplatePreRender',
                'priority' => 100000,
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . ".cssjs/styles",
                'method'   => 'onScriptsStylesTemplatePreRender',
                'priority' => 100000,
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener',
            'tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ui_FormInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_OPTIONS_PAGE_INVOKED,
                'method'   => 'run',
                'priority' => 100000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_SELECT . '.options-ui/form',
                'method'   => 'onTemplateSelect',
                'priority' => 100000,
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_options_AdminThemeListener',
            'tubepress_wordpress_impl_listeners_options_AdminThemeListener'
        )->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::OPTION_DEFAULT_VALUE . '.' . tubepress_app_api_options_Names::THEME_ADMIN,
                'method'   => 'onDefaultValue',
                'priority' => 100000,
            ));
    }

    private function _registerSingletons()
    {
        $this->expectRegistration(
            tubepress_lib_api_translation_TranslatorInterface::_,
            'tubepress_wordpress_impl_translation_WpTranslator'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));

        $this->expectRegistration(
            tubepress_app_api_options_PersistenceBackendInterface::_,
            'tubepress_wordpress_impl_options_WpPersistence'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));
    }

    private function _registerWpServices()
    {
        $this->expectRegistration(
            'tubepress_wordpress_impl_wp_ActivationHook',
            'tubepress_wordpress_impl_wp_ActivationHook'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $this->expectRegistration(
            'tubepress_wordpress_impl_Callback',
            'tubepress_wordpress_impl_Callback'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('tubepress_wordpress_impl_wp_ActivationHook'));

        $this->expectRegistration(
            'tubepress_wordpress_impl_wp_Widget',
            'tubepress_wordpress_impl_wp_Widget'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_shortcode_ParserInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_ . '.admin'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_WIDGET_PUBLIC_HTML,
                'method'   => 'printWidgetHtml',
                'priority' => 100000))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_WIDGET_PRINT_CONTROLS,
                'method'   => 'printControlHtml',
                'priority' => 100000
            ));

        $this->expectRegistration(
            tubepress_wordpress_impl_wp_WpFunctions::_,
            'tubepress_wordpress_impl_wp_WpFunctions'
        );

        $this->expectRegistration(
            'tubepress_wordpress_impl_wp_TemplatePathProvider',
            'tubepress_wordpress_impl_wp_TemplatePathProvider'
        )->withTag('tubepress_lib_api_template_PathProviderInterface.admin');
    }

    private function _registerVendorServices()
    {
        $this->expectRegistration(
            'ehough_filesystem_FilesystemInterface',
            'ehough_filesystem_Filesystem'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockFieldBuilder = $this->mock(tubepress_app_api_options_ui_FieldBuilderInterface::_);
        $mockField        = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $mockFieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(

            tubepress_app_api_options_ui_FormInterface::_ => tubepress_app_api_options_ui_FormInterface::_,
            tubepress_lib_api_http_RequestParametersInterface::_ => tubepress_lib_api_http_RequestParametersInterface::_,
            tubepress_platform_api_url_UrlFactoryInterface::_ => tubepress_platform_api_url_UrlFactoryInterface::_,
            tubepress_platform_api_util_StringUtilsInterface::_ => tubepress_platform_api_util_StringUtilsInterface::_,
            tubepress_app_api_options_ContextInterface::_ => tubepress_app_api_options_ContextInterface::_,
            tubepress_app_api_html_HtmlGeneratorInterface::_ => tubepress_app_api_html_HtmlGeneratorInterface::_,
            tubepress_app_api_shortcode_ParserInterface::_ => tubepress_app_api_shortcode_ParserInterface::_,
            tubepress_lib_api_event_EventDispatcherInterface::_ => tubepress_lib_api_event_EventDispatcherInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_ => tubepress_lib_api_template_TemplatingInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_ . '.admin' => tubepress_lib_api_template_TemplatingInterface::_,
            tubepress_app_api_html_HtmlGeneratorInterface::_ => tubepress_app_api_html_HtmlGeneratorInterface::_,
            tubepress_app_api_environment_EnvironmentInterface::_ => tubepress_app_api_environment_EnvironmentInterface::_,
            tubepress_app_api_options_ui_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_platform_api_boot_BootSettingsInterface::_ => tubepress_platform_api_boot_BootSettingsInterface::_,
            tubepress_lib_api_http_AjaxInterface::_ => tubepress_lib_api_http_AjaxInterface::_,
            tubepress_app_api_options_ReferenceInterface::_ => tubepress_app_api_options_ReferenceInterface::_,
            tubepress_app_api_options_PersistenceInterface::_ => tubepress_app_api_options_PersistenceInterface::_
        );
    }
}