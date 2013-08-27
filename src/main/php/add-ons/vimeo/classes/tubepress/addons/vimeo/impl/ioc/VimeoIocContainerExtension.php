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
class tubepress_addons_vimeo_impl_ioc_VimeoIocContainerExtension implements tubepress_api_ioc_ContainerExtensionInterface
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

            'tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder',
            'tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder'
        );

        $container->register(

            'tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService',
            'tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService'

        )->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $container->register(

            'tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService',
            'tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService'

        )->addArgument(new tubepress_impl_ioc_Reference('tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder'))
            ->addTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $container->register(

            'tubepress_addons_vimeo_impl_options_ui_VimeoPluggableOptionsPageParticipant',
            'tubepress_addons_vimeo_impl_options_ui_VimeoPluggableOptionsPageParticipant'

        )->addTag(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);

        $container->register(

            'tubepress_addons_vimeo_impl_options_VimeoOptionsProvider',
            'tubepress_addons_vimeo_impl_options_VimeoOptionsProvider'
        )->addTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $container->register(

            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION, 'method' => 'onVideoConstruction', 'priority' => 10000));

        $this->_registerHttpListeners($container);

        $this->_registerOauthClient($container);
    }

    private function _registerHttpListeners(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener',
            'tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse', 'priority' => 10000));

        $container->register(

            'tubepress_addons_vimeo_impl_listeners_http_VimeoOauthRequestListener',
            'tubepress_addons_vimeo_impl_listeners_http_VimeoOauthRequestListener'
        )->addArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_api_v1_ClientInterface'))
         ->addArgument(new tubepress_impl_ioc_Reference(tubepress_spi_context_ExecutionContext::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::REQUEST, 'method' => 'onRequest', 'priority' => 10000));
    }

    private function _registerOauthClient(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'ehough_coauthor_spi_v1_TemporaryCredentialsStorageInterface',
            'ehough_coauthor_impl_v1_SessionCredentialsStorage'
        );

        $container->register(

            'ehough_coauthor_spi_v1_SignerInterface',
            'ehough_coauthor_impl_v1_Signer'
        );

        $container->register(

            'ehough_coauthor_spi_v1_RemoteCredentialsFetcherInterface',
            'ehough_coauthor_impl_v1_DefaultRemoteCredentialsFetcher'
        )->addArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_api_HttpClientInterface'))
         ->addArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_spi_v1_SignerInterface'));

        $container->register(

            'ehough_coauthor_api_v1_ClientInterface',
            'ehough_coauthor_impl_v1_DefaultV1Client'
        )->addArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_spi_v1_TemporaryCredentialsStorageInterface'))
         ->addArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_spi_v1_RemoteCredentialsFetcherInterface'))
         ->addArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_spi_v1_SignerInterface'));
    }
}