<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_wordpress_ioc_WordPressExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder A tubepress_api_ioc_ContainerBuilderInterface instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!defined('ABSPATH')) {

            /**
             * Skip all this if we're not in WP.
             */
            return;
        }

        $this->_registerOptions($containerBuilder);
        $this->_registerListeners($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
        $this->_registerSingletons($containerBuilder);
        $this->_registerWpServices($containerBuilder);
        $this->_registerVendorServices($containerBuilder);
        $this->_registerHttpOauth2Services($containerBuilder);
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__wordpress',
            'tubepress_api_options_Reference'
        )->addArgument(array(
            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE     => 'TubePress',
                tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']',
                tubepress_api_options_Names::SHORTCODE_KEYWORD             => 'tubepress',
                tubepress_api_options_Names::TUBEPRESS_API_KEY             => null,
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD => 'Shortcode keyword',  //>(translatable)<,
                tubepress_api_options_Names::TUBEPRESS_API_KEY => 'tubepress.com API Key',            //>(translatable)<,
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', //>(translatable)<,
                tubepress_api_options_Names::TUBEPRESS_API_KEY => sprintf('Enable automatic plugin updates by supplying your <a href="%s" target="_blank">TubePress API key</a>.',                           //>(translatable)<,
                    'https://dashboard.tubepress.com/profile'
                ),
            ),
        ))
        ->addArgument(array(
            tubepress_api_options_Reference::PROPERTY_PRO_ONLY => array(
                tubepress_api_options_Names::TUBEPRESS_API_KEY,
            )
        ))->addTag(tubepress_api_options_ReferenceInterface::_);

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS => array(
                tubepress_api_options_Names::TUBEPRESS_API_KEY,
            )
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $containerBuilder->register(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                 ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                    'priority' => 100000,
                    'method'   => 'onOption',
                ));
            }
        }
    }


    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldIndex = 0;
        $containerBuilder->register(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_wordpress_impl_options_ui_fields_WpNonceField'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));

        $containerBuilder->register(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_api_options_Names::SHORTCODE_KEYWORD)
         ->addArgument('text');

        $containerBuilder->register(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
            ->setFactoryMethod('newInstance')
            ->addArgument(tubepress_api_options_Names::TUBEPRESS_API_KEY)
            ->addArgument('text');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('wordpress_field_' . $x);
        }

        $containerBuilder->register(
            'tubepress_wordpress_impl_options_ui_WpFieldProvider',
            'tubepress_wordpress_impl_options_ui_WpFieldProvider'
        )->addArgument($fieldReferences)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters',
            'tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ui_FormInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_popup_AuthorizationInitiator'))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_popup_RedirectionCallback'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.admin_enqueue_scripts',
            'method'   => 'onAction_admin_enqueue_scripts',
            'priority' => 100000
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.admin_head',
            'method'   => 'onAction_admin_head',
            'priority' => 100000
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.admin_menu',
            'method'   => 'onAction_admin_menu',
            'priority' => 100000
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.admin_notices',
            'method'   => 'onAction_admin_notices',
            'priority' => 100000
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.filter.plugin_row_meta',
            'method'   => 'onFilter_row_meta',
            'priority' => 100000))
          ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.filter.plugin_action_links',
            'method'   => 'onFilter_row_meta',
            'priority' => 100000))
          ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.filter.puc_request_info_query_args-tubepress',
            'method'   => 'onFilter_PucRequestInfoQueryArgsTubePress',
            'priority' => 100000))
          ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.filter.puc_request_info_result-tubepress',
            'method'   => 'onFilter_PucRequestInfoResultTubePress',
            'priority' => 100000))
          ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.admin_print_scripts-settings_page_tubepress',
            'method'   => 'onAction_admin_print_scripts',
            'priority' => 100000))
          ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.load-admin_page_tubepress_oauth2_start',
            'method'   => 'onAction_load_admin_page_tubepress_oauth2_start',
            'priority' => 100000))
          ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.load-admin_page_tubepress_oauth2',
            'method'   => 'onAction_load_admin_page_tubepress_oauth2',
            'priority' => 100000))
          ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => sprintf('tubepress.wordpress.action.in_plugin_update_message-%s/tubepress.php', basename(TUBEPRESS_ROOT)),
            'method'   => 'onAction_in_plugin_update_message',
            'priority' => 100000));

        $containerBuilder->register(
            'tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters',
            'tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_html_HtmlGeneratorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_AjaxInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.wp_ajax_nopriv_tubepress',
            'method'   => 'onAction_ajax',
            'priority' => 100000
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.wp_ajax_tubepress',
            'method'   => 'onAction_ajax',
            'priority' => 100000
         ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.init',
            'method'   => 'onAction_init',
            'priority' => 100000
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.widgets_init',
            'method'   => 'onAction_widgets_init',
            'priority' => 100000
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.action.wp_head',
            'method'   => 'onAction_wp_head',
            'priority' => 100000
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => 'tubepress.wordpress.filter.jetpack_photon_skip_for_url',
            'method'   => 'onFilter_jetpack_photon_skip_for_url',
            'priority' => 100000,
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_listeners_html_WpHtmlListener',
            'tubepress_wordpress_impl_listeners_html_WpHtmlListener'
        )->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . ".cssjs/scripts",
            'method'   => 'onScriptsStylesTemplatePreRender',
            'priority' => 100000,
         ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . ".cssjs/styles",
            'method'   => 'onScriptsStylesTemplatePreRender',
            'priority' => 100000,
         ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener',
            'tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ui_FormInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_wordpress_api_Constants::EVENT_OPTIONS_PAGE_INVOKED,
            'method'   => 'run',
            'priority' => 100000
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_SELECT . '.options-ui/form',
            'method'   => 'onTemplateSelect',
            'priority' => 100000,
        ));

        $containerBuilder->register(
            'tubepress_wordpress_impl_listeners_options_AdminThemeListener',
            'tubepress_wordpress_impl_listeners_options_AdminThemeListener'
        )->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_DEFAULT_VALUE . '.' . tubepress_api_options_Names::THEME_ADMIN,
            'method'   => 'onDefaultValue',
            'priority' => 100000,
        ));
    }

    private function _registerSingletons(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_api_translation_TranslatorInterface::_,
            'tubepress_wordpress_impl_translation_WpTranslator'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));

        $containerBuilder->register(
            tubepress_spi_options_PersistenceBackendInterface::_,
            'tubepress_wordpress_impl_options_WpPersistence'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));
    }

    private function _registerWpServices(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_wordpress_impl_wp_ActivationHook',
            'tubepress_wordpress_impl_wp_ActivationHook'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('filesystem'));

        $containerBuilder->register(
            'tubepress_wordpress_impl_Callback',
            'tubepress_wordpress_impl_Callback'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_html_HtmlGeneratorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_wp_ActivationHook'));

        $containerBuilder->register(
            'tubepress_wordpress_impl_wp_Widget',
            'tubepress_wordpress_impl_wp_Widget'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_html_HtmlGeneratorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_shortcode_ParserInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_wordpress_api_Constants::EVENT_WIDGET_PUBLIC_HTML,
            'method'   => 'printWidgetHtml',
            'priority' => 100000))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_wordpress_api_Constants::EVENT_WIDGET_PRINT_CONTROLS,
            'method'   => 'printControlHtml',
            'priority' => 100000
        ));

        $containerBuilder->register(
            tubepress_wordpress_impl_wp_WpFunctions::_,
            'tubepress_wordpress_impl_wp_WpFunctions'
        );

        $containerBuilder->register(
            'tubepress_wordpress_impl_wp_TemplatePathProvider',
            'tubepress_wordpress_impl_wp_TemplatePathProvider'
        )->addTag('tubepress_spi_template_PathProviderInterface.admin');
    }

    private function _registerVendorServices(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'filesystem',
            'Symfony\Component\Filesystem\Filesystem'
        );
    }

    private function _registerHttpOauth2Services(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_,
            'tubepress_wordpress_impl_http_oauth2_Oauth2Environment'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_);
    }
}