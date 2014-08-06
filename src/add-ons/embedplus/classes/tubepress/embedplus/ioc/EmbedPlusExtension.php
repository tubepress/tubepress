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
 * Registers a few extensions to allow TubePress to work with EmbedPlus.
 */
class tubepress_embedplus_ioc_EmbedPlusExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{
    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder A tubepress_platform_api_ioc_ContainerBuilderInterface instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_embedplus_impl_listeners_EmbedPlusListener',
            'tubepress_embedplus_impl_listeners_EmbedPlusListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::GALLERY_INIT_JS,
            'method'   => 'onGalleryInitJs',
            'priority' => 7000
        ))->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_SELECT . '.single/embedded',
            'method'   => 'onEmbeddedTemplateSelect',
            'priority' => 10500
        ))->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL,
            'method'   => 'onPlayerImplAcceptableValues',
            'priority' => 10000,
        ));
    }
}