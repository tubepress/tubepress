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
class tubepress_deprecated_ioc_DeprecatedExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{
    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_platform_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_platform_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
//        $containerBuilder->register(
//            'tubepress_deprecated_impl_listeners_LegacyMetadataTemplateListener',
//            'tubepress_deprecated_impl_listeners_LegacyMetadataTemplateListener'
//        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
//         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
//         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))
//         ->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
//            'tag'    => tubepress_app_api_media_MediaProviderInterface::_,
//            'method' => 'setMediaProviders'))
//         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
//            'event'    => tubepress_app_api_event_Events::GALLERY_TEMPLATE,
//            'method'   => 'onTemplate',
//            'priority' => 10400))
//         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
//            'event'    => tubepress_app_api_event_Events::SINGLE_ITEM_TEMPLATE,
//            'method'   => 'onTemplate',
//            'priority' => 10100))
//         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
//            'event'    => tubepress_app_api_event_Events::SINGLE_ITEM_TEMPLATE,
//            'method'   => 'onSingleTemplate',
//            'priority' => 10100
//         ));
//
//        $containerBuilder->register(
//            'tubepress_deprecated_impl_listeners_LegacyTemplateVarsListener',
//            'tubepress_deprecated_impl_listeners_LegacyTemplateVarsListener'
//        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
//         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
//            'event'    => tubepress_app_api_event_Events::GALLERY_TEMPLATE,
//            'method'   => 'onTemplate',
//            'priority' => 2000))
//         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
//            'event'    => tubepress_app_api_event_Events::SINGLE_ITEM_TEMPLATE,
//            'method'   => 'onTemplate',
//            'priority' => 2000
//         ));
    }
}