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
 * @covers tubepress_core_options_ioc_OptionsExtension
 */
class tubepress_test_core_impl_options_OptionsExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_core_options_ioc_OptionsExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_options_ioc_OptionsExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_core_options_impl_listeners_BasicOptionValidity',
            'tubepress_core_options_impl_listeners_BasicOptionValidity'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_AcceptableValuesInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET,
                'priority' => 30000,
                'method'   => 'onOption'
            ));

        $this->expectRegistration(
            'tubepress_core_options_impl_listeners_StringMagic',
            'tubepress_core_options_impl_listeners_StringMagic'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET,
                'method'   => 'onSet',
                'priority' => 30100
            ))->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER,  array(
                'event'    => tubepress_core_options_api_Constants::EVENT_NVP_READ_FROM_EXTERNAL_INPUT,
                'method'   => 'onExternalInput',
                'priority' => 30000
            ));

        $this->expectRegistration(

            'tubepress_core_options_impl_listeners_Logger',
            'tubepress_core_options_impl_listeners_Logger'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET,
                'method'   => 'onOption',
                'priority' => -10000
            ));

        $this->expectRegistration(
            tubepress_core_options_api_AcceptableValuesInterface::_,
            'tubepress_core_options_impl_AcceptableValues'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));

        $this->expectRegistration(
            tubepress_core_options_api_ContextInterface::_,
            'tubepress_core_options_impl_Context'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));

        $this->expectRegistration(
            tubepress_core_options_api_PersistenceInterface::_,
            'tubepress_core_options_impl_Persistence'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceBackendInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));
    }

    protected function getExpectedExternalServicesMap()
    {

        return array(

            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_,
            tubepress_core_options_api_PersistenceBackendInterface::_ => tubepress_core_options_api_PersistenceBackendInterface::_,
            tubepress_core_theme_api_ThemeLibraryInterface::_ => tubepress_core_theme_api_ThemeLibraryInterface::_,
            tubepress_core_translation_api_TranslatorInterface::_ => tubepress_core_translation_api_TranslatorInterface::_,
            tubepress_core_http_api_RequestParametersInterface::_ => tubepress_core_http_api_RequestParametersInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            tubepress_api_util_LangUtilsInterface::_ => tubepress_api_util_LangUtilsInterface::_,
            tubepress_core_options_api_ReferenceInterface::_ => tubepress_core_options_api_ReferenceInterface::_
        );
    }
}
