<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Registers a few extensions to allow TubePress to work with YouTube.
 */
class tubepress_addons_youtube_impl_ioc_YouTubeIocContainerExtension implements tubepress_api_ioc_ContainerExtensionInterface
{
    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_api_ioc_ContainerInterface $container A tubepress_api_ioc_ContainerInterface instance.
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public function load(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder',
            'tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder'
        );

        $container->register(

            'tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService',
            'tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService'

        )->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $container->register(

            'tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService',
            'tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService'

        )->addArgument(new tubepress_impl_ioc_Reference('tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder'))
         ->addTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $container->register(

            'tubepress_addons_youtube_impl_options_ui_YouTubeOptionsPageParticipant',
            'tubepress_addons_youtube_impl_options_ui_YouTubeOptionsPageParticipant'

        )->addTag(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);

        $container->register(

            'tubepress_addons_youtube_impl_options_YouTubeOptionsProvider',
            'tubepress_addons_youtube_impl_options_YouTubeOptionsProvider'
        )->addTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $container->register(

            'tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener',
            'tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION, 'method' => 'onVideoConstruction'));

        $container->register(

            'tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener',
            'tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse'));

        $container->register(

            'tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistPlPrefixRemover',
            'tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistPlPrefixRemover'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, 'method' => 'onPreValidationOptionSet'));
    }
}