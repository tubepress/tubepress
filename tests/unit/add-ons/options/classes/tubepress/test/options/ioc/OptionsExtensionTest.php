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
 * @covers tubepress_options_ioc_OptionsExtension
 */
class tubepress_test_options_ioc_OptionsExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_options_ioc_OptionsExtension
     */
    protected function buildSut()
    {
        return  new tubepress_options_ioc_OptionsExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_api_options_AcceptableValuesInterface::_,
            'tubepress_options_impl_AcceptableValues'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

        $this->expectRegistration(
            tubepress_api_options_ContextInterface::_,
            'tubepress_options_impl_Context'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_));

        $this->expectRegistration(
            tubepress_api_options_ReferenceInterface::_,
            'tubepress_options_impl_DispatchingReference'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_api_options_ReferenceInterface::_,
                'method' => 'setReferences'
            ));

        $this->expectRegistration(
            tubepress_api_options_PersistenceInterface::_,
            'tubepress_options_impl_Persistence'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceBackendInterface::_));
        
        $this->_registerListeners();
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_options_impl_listeners_StringMagicListener',
            'tubepress_options_impl_listeners_StringMagicListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::NVP_FROM_EXTERNAL_INPUT,
                'priority' => 100000,
                'method'   => 'onExternalInput',
            ));

        $this->expectRegistration(
            'tubepress_options_impl_listeners_LoggingListener',
            'tubepress_options_impl_listeners_LoggingListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET,
                'priority' => -100000,
                'method'   => 'onOptionSet',
            ));

        $this->expectRegistration(
            'tubepress_options_impl_listeners_BasicOptionValidity',
            'tubepress_options_impl_listeners_BasicOptionValidity'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_AcceptableValuesInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET,
                'priority' => 200000,
                'method'   => 'onOption',
            ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(
            tubepress_api_event_EventDispatcherInterface::_      => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_options_PersistenceBackendInterface::_ => tubepress_api_options_PersistenceBackendInterface::_,
            tubepress_api_log_LoggerInterface::_            => tubepress_api_log_LoggerInterface::_,
            tubepress_api_util_StringUtilsInterface::_      => tubepress_api_util_StringUtilsInterface::_,
            tubepress_api_translation_TranslatorInterface::_     => tubepress_api_translation_TranslatorInterface::_,
            tubepress_api_util_LangUtilsInterface::_        => tubepress_api_util_LangUtilsInterface::_,
        );
    }
}
