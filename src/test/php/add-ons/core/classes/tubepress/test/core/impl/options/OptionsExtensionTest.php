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
 * @covers tubepress_core_impl_options_OptionsExtension
 */
class tubepress_test_core_impl_options_OptionsExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_impl_options_OptionsExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_impl_options_OptionsExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_core_api_options_ProviderInterface::_,
            'tubepress_core_impl_options_Provider'
        )->withTag(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_api_options_ProviderInterface::_,
            'method' => 'setAddonOptionProviders'
        ));

        $this->expectRegistration(

            tubepress_core_api_options_PersistenceInterface::_,
            'tubepress_core_impl_options_Persistence'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_PersistenceBackendInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ProviderInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_)) ;

        $this->expectRegistration(

            tubepress_core_api_options_ContextInterface::_,
            'tubepress_core_impl_options_Context'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_PersistenceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference( tubepress_core_api_options_ProviderInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_));

        $this->expectRegistration(

            'tubepress_core_impl_options_MetaOptionNameService',
            'tubepress_core_impl_options_MetaOptionNameService'
        )->withTag(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER, array(

            'tag'    => tubepress_core_api_provider_VideoProviderInterface::_,
            'method' => 'setVideoProviders'
        ));

        $this->expectRegistration(

            'tubepress_core_impl_options_CoreOptionProvider',
            'tubepress_core_impl_options_CoreOptionProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_theme_ThemeLibraryInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER, array(
                'tag' => tubepress_core_api_player_PlayerLocationInterface::_,
                'method' => 'setPlayerLocations'))
            ->withTag(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER, array(
                'tag' => tubepress_core_api_embedded_EmbeddedProviderInterface::_,
                'method' => 'setEmbeddedProviders'))
            ->withTag(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER, array(
                'tag' => tubepress_core_api_provider_VideoProviderInterface::_,
                'method' => 'setVideoProviders'))
            ->withTag(tubepress_core_api_options_EasyProviderInterface::_);

        $this->expectRegistration(

            tubepress_core_api_options_ui_FieldBuilderInterface::_,
            'tubepress_core_impl_options_ui_FieldBuilder'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ProviderInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_api_options_ProviderInterface::_ => 'tubepress_core_impl_options_Provider',
            tubepress_core_api_options_ContextInterface::_ => 'tubepress_core_impl_options_Context',
            tubepress_core_api_options_PersistenceInterface::_ => 'tubepress_core_impl_options_Persistence',
            'tubepress_core_impl_options_MetaOptionNameService' => 'tubepress_core_impl_options_MetaOptionNameService',
            'tubepress_core_impl_options_CoreOptionProvider' => 'tubepress_core_impl_options_CoreOptionProvider',
            tubepress_core_api_options_ui_FieldBuilderInterface::_ => 'tubepress_core_impl_options_ui_FieldBuilder'
        );
    }

    protected function getExpectedExternalServicesMap()
    {

        return array(

            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_core_api_event_EventDispatcherInterface::_ => tubepress_core_api_event_EventDispatcherInterface::_,
            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_,
            tubepress_core_api_options_PersistenceBackendInterface::_ => tubepress_core_api_options_PersistenceBackendInterface::_,
            tubepress_core_api_theme_ThemeLibraryInterface::_ => tubepress_core_api_theme_ThemeLibraryInterface::_,
            tubepress_core_api_translation_TranslatorInterface::_ => tubepress_core_api_translation_TranslatorInterface::_,
            tubepress_core_api_http_RequestParametersInterface::_ => tubepress_core_api_http_RequestParametersInterface::_,
            tubepress_core_api_template_TemplateFactoryInterface::_ => tubepress_core_api_template_TemplateFactoryInterface::_,
            tubepress_api_util_LangUtilsInterface::_ => tubepress_api_util_LangUtilsInterface::_
        );
    }
}
