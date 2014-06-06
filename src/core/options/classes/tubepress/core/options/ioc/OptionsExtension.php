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
 *
 */
class tubepress_core_options_ioc_OptionsExtension implements tubepress_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_core_options_impl_listeners_BasicOptionValidity',
            'tubepress_core_options_impl_listeners_BasicOptionValidity'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_AcceptableValuesInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET,
            'priority' => 30000,
            'method'   => 'onOption'
        ));

        $containerBuilder->register(
            'tubepress_core_options_impl_listeners_StringMagic',
            'tubepress_core_options_impl_listeners_StringMagic'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET,
            'method'   => 'onSet',
            'priority' => 30100
        ))->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER,  array(
            'event'    => tubepress_core_options_api_Constants::EVENT_NVP_READ_FROM_EXTERNAL_INPUT,
            'method'   => 'onExternalInput',
            'priority' => 30000
        ));

        $containerBuilder->register(

            'tubepress_core_options_impl_listeners_Logger',
            'tubepress_core_options_impl_listeners_Logger'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET,
            'method'   => 'onOption',
            'priority' => -10000
        ));

        $containerBuilder->register(
            tubepress_core_options_api_AcceptableValuesInterface::_,
            'tubepress_core_options_impl_AcceptableValues'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));

        $containerBuilder->register(
            tubepress_core_options_api_ContextInterface::_,
            'tubepress_core_options_impl_Context'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));

        $containerBuilder->register(
            tubepress_core_options_api_PersistenceInterface::_,
            'tubepress_core_options_impl_Persistence'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceBackendInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));
    }
}