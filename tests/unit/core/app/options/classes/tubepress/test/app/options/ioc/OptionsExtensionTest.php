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
 * @covers tubepress_app_options_ioc_OptionsExtension
 */
class tubepress_test_app_impl_options_OptionsExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_app_options_ioc_OptionsExtension
     */
    protected function buildSut()
    {
        return  new tubepress_app_options_ioc_OptionsExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_app_options_impl_listeners_BasicOptionValidity',
            'tubepress_app_options_impl_listeners_BasicOptionValidity'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ReferenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_AcceptableValuesInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_SET,
                'priority' => 30000,
                'method'   => 'onOption'
            ));

        $this->expectRegistration(
            'tubepress_app_options_impl_listeners_StringMagic',
            'tubepress_app_options_impl_listeners_StringMagic'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER,  array(
                'event'    => tubepress_app_options_api_Constants::EVENT_NVP_READ_FROM_EXTERNAL_INPUT,
                'method'   => 'onExternalInput',
                'priority' => 30000
            ));

        $this->expectRegistration(

            'tubepress_app_options_impl_listeners_Logger',
            'tubepress_app_options_impl_listeners_Logger'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_SET,
                'method'   => 'onOptionSet',
                'priority' => -10000
            ));

        $this->expectRegistration(
            tubepress_app_options_api_AcceptableValuesInterface::_,
            'tubepress_app_options_impl_AcceptableValues'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_));

        $this->expectRegistration(
            tubepress_app_options_api_ContextInterface::_,
            'tubepress_app_options_impl_Context'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ReferenceInterface::_));

        $this->expectRegistration(
            tubepress_app_options_api_PersistenceInterface::_,
            'tubepress_app_options_impl_Persistence'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_PersistenceBackendInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ReferenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_));
    }

    protected function getExpectedExternalServicesMap()
    {

        return array(

            tubepress_platform_api_log_LoggerInterface::_ => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_ => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_platform_api_util_StringUtilsInterface::_ => tubepress_platform_api_util_StringUtilsInterface::_,
            tubepress_app_options_api_PersistenceBackendInterface::_ => tubepress_app_options_api_PersistenceBackendInterface::_,
            tubepress_app_theme_api_ThemeLibraryInterface::_ => tubepress_app_theme_api_ThemeLibraryInterface::_,
            tubepress_lib_translation_api_TranslatorInterface::_ => tubepress_lib_translation_api_TranslatorInterface::_,
            tubepress_app_http_api_RequestParametersInterface::_ => tubepress_app_http_api_RequestParametersInterface::_,
            tubepress_lib_template_api_TemplateFactoryInterface::_ => tubepress_lib_template_api_TemplateFactoryInterface::_,
            tubepress_platform_api_util_LangUtilsInterface::_ => tubepress_platform_api_util_LangUtilsInterface::_,
            tubepress_app_options_api_ReferenceInterface::_ => tubepress_app_options_api_ReferenceInterface::_
        );
    }
}
