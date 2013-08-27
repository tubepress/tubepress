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
class tubepress_addons_vimeo_impl_ioc_VimeoIocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_addons_vimeo_impl_ioc_VimeoIocContainerExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder',
            'tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder'
        );

        $this->expectRegistration(

            'tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService',
            'tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService'

        )->withTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $this->expectRegistration(

            'tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService',
            'tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService'

        )->withArgument(new ehough_iconic_Reference('tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder'))
            ->withTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $this->expectRegistration(

            'tubepress_addons_vimeo_impl_options_ui_VimeoPluggableOptionsPageParticipant',
            'tubepress_addons_vimeo_impl_options_ui_VimeoPluggableOptionsPageParticipant'

        )->withTag(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);

        $this->expectRegistration(

            'tubepress_addons_vimeo_impl_options_VimeoOptionsProvider',
            'tubepress_addons_vimeo_impl_options_VimeoOptionsProvider'
        )->withTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $this->expectRegistration(

            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION, 'method' => 'onVideoConstruction', 'priority' => 10000));


        $this->_expectHttpListenerRegistration();
        $this->_expectOauthClientRegistration();
    }

    private function _expectHttpListenerRegistration()
    {
        $this->expectRegistration(

            'tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener',
            'tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse', 'priority' => 10000));

        $this->expectRegistration(

            'tubepress_addons_vimeo_impl_listeners_http_VimeoOauthRequestListener',
            'tubepress_addons_vimeo_impl_listeners_http_VimeoOauthRequestListener'
        )->withArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_api_v1_ClientInterface'))
         ->withArgument(new tubepress_impl_ioc_Reference(tubepress_spi_context_ExecutionContext::_))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => ehough_shortstop_api_Events::REQUEST, 'method' => 'onRequest', 'priority' => 9000));
    }

    private function _expectOauthClientRegistration()
    {
        $this->expectRegistration(

            'ehough_coauthor_spi_v1_TemporaryCredentialsStorageInterface',
            'ehough_coauthor_impl_v1_SessionCredentialsStorage'
        );

        $this->expectRegistration(

            'ehough_coauthor_spi_v1_SignerInterface',
            'ehough_coauthor_impl_v1_Signer'
        );

        $this->expectRegistration(

            'ehough_coauthor_spi_v1_RemoteCredentialsFetcherInterface',
            'ehough_coauthor_impl_v1_DefaultRemoteCredentialsFetcher'
        )->withArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_api_HttpClientInterface'))
         ->withArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_spi_v1_SignerInterface'));

        $this->expectRegistration(

            'ehough_coauthor_api_v1_ClientInterface',
            'ehough_coauthor_impl_v1_DefaultV1Client'
        )->withArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_spi_v1_TemporaryCredentialsStorageInterface'))
         ->withArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_spi_v1_RemoteCredentialsFetcherInterface'))
         ->withArgument(new tubepress_impl_ioc_Reference('ehough_coauthor_spi_v1_SignerInterface'));
    }
}