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

class tubepress_options_ioc_OptionsExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerServices($containerBuilder);
        $this->_registerListeners($containerBuilder);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_options_impl_listeners_StringMagicListener',
            'tubepress_options_impl_listeners_StringMagicListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::NVP_FROM_EXTERNAL_INPUT,
            'priority' => 100000,
            'method'   => 'onExternalInput',
        ));

        $containerBuilder->register(
            'tubepress_options_impl_listeners_LoggingListener',
            'tubepress_options_impl_listeners_LoggingListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_SET,
            'priority' => -100000,
            'method'   => 'onOptionSet',
        ));

        $containerBuilder->register(
            'tubepress_options_impl_listeners_BasicOptionValidity',
            'tubepress_options_impl_listeners_BasicOptionValidity'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_AcceptableValuesInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_SET,
            'priority' => 200000,
            'method'   => 'onOption',
        ));
    }

    private function _registerServices(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_api_options_AcceptableValuesInterface::_,
            'tubepress_options_impl_AcceptableValues'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

        $containerBuilder->register(
            tubepress_api_options_ContextInterface::_,
            'tubepress_options_impl_Context'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_));

        $containerBuilder->register(
            tubepress_api_options_ReferenceInterface::_,
            'tubepress_options_impl_DispatchingReference'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_api_options_ReferenceInterface::_,
            'method' => 'setReferences',
        ));

        $containerBuilder->register(
            tubepress_api_options_PersistenceInterface::_,
            'tubepress_options_impl_Persistence'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_spi_options_PersistenceBackendInterface::_));
    }
}
