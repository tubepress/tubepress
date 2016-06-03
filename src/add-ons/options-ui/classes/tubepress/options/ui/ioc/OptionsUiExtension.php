<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 *
 */
class tubepress_options_ui_ioc_OptionsUiExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerOptionsUiSingletons($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerListeners($containerBuilder);
        $this->_registerPathProvider($containerBuilder);
    }

    private function _registerPathProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_template_BasePathProvider__options_ui',
            'tubepress_api_template_BasePathProvider'
        )->addArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/options-ui/templates',
        ))->addTag('tubepress_spi_template_PathProviderInterface.admin');
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_options_ui_impl_listeners_BootstrapIe8Listener',
            'tubepress_options_ui_impl_listeners_BootstrapIe8Listener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::HTML_SCRIPTS_ADMIN,
            'priority' => 100000,
            'method'   => 'onAdminScripts',
        ));

        $containerBuilder->register(
            'tubepress_options_ui_impl_listeners_OptionsPageTemplateListener',
            'tubepress_options_ui_impl_listeners_OptionsPageTemplateListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => 'tubepress_spi_options_ui_FieldProviderInterface',
            'method' => 'setFieldProviders', ))
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => 'tubepress_spi_media_MediaProviderInterface',
            'method' => 'setMediaProviders', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.options-ui/form',
            'priority' => 100000,
            'method'   => 'onOptionsGuiTemplate',
        ));
    }

    private function _registerOptionsUiSingletons(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_api_options_ui_FieldBuilderInterface::_,
            'tubepress_options_ui_impl_FieldBuilder'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_AcceptableValuesInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_PersistenceHelper'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_spi_media_MediaProviderInterface::__,
                'method' => 'setMediaProviders',
            ));

        $containerBuilder->register(
            'tubepress_html_impl_CssAndJsGenerationHelper.admin',
            'tubepress_html_impl_CssAndJsGenerationHelper'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_ . '.admin'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService.admin'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addArgument(tubepress_api_event_Events::HTML_STYLESHEETS_ADMIN)
         ->addArgument(tubepress_api_event_Events::HTML_SCRIPTS_ADMIN)
         ->addArgument('options-ui/cssjs/styles')
         ->addArgument('options-ui/cssjs/scripts');

        $containerBuilder->register(
            tubepress_api_options_ui_FormInterface::_,
            'tubepress_options_ui_impl_Form'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_html_impl_CssAndJsGenerationHelper.admin'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => 'tubepress_spi_options_ui_FieldProviderInterface',
                'method' => 'setFieldProviders',
            ));
    }

    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $categoryReferences = array();
        $categories         = array(
            array(tubepress_api_options_ui_CategoryNames::ADVANCED, 'Advanced'),      //>(translatable)<
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'options_ui_category_' . $categoryIdAndLabel[0];
            $containerBuilder->register(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->addArgument($categoryIdAndLabel[0])
             ->addArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_api_ioc_Reference($serviceId);
        }

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__options_ui',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-options-ui')
         ->addArgument('Options UI')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument($categoryReferences)
         ->addArgument(array())
         ->addArgument(array())
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__options_ui',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => null,
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => 'Only show options applicable to...', //>(translatable)<
            ),

        ))->addArgument(array());
    }
}
