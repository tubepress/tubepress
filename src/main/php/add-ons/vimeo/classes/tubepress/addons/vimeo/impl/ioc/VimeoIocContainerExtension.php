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
        $this->_registerPluggables($container);

        $this->_registerListeners($container);

        $this->_registerOauthClient($container);
    }

    private function _registerPluggables(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService',
            'tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService'

        )->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $container->register(

            'tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder',
            'tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder'
        );
        $container->register(

            'tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService',
            'tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService'

        )->addArgument(new tubepress_impl_ioc_Reference('tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder'))
         ->addTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $container->register(

            'tubepress_addons_vimeo_impl_options_VimeoOptionsProvider',
            'tubepress_addons_vimeo_impl_options_VimeoOptionsProvider'
        )->addTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $this->_registerOptionsPageParticipant($container);
    }

    private function _registerOptionsPageParticipant(tubepress_api_ioc_ContainerInterface $container)
    {
        $fieldIndex = 0;

        $container->register('vimeo_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
            ->addArgument(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY)
            ->addMethodCall('setSize', array(40));

        $container->register('vimeo_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
            ->addArgument(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET)
            ->addMethodCall('setSize', array(40));

        $gallerySourceMap = array(

            array(
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE),

            array(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE),

            array(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE),

            array(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE),

            array(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE),

            array(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE),

            array(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE),

            array(tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $container->register('vimeo_options_subfield_' . $fieldIndex, $gallerySourceFieldArray[1])->addArgument($gallerySourceFieldArray[2]);

            $container->register('vimeo_options_field_' . $fieldIndex, 'tubepress_impl_options_ui_fields_GallerySourceRadioField')
                ->addArgument($gallerySourceFieldArray[0])
                ->addArgument(new tubepress_impl_ioc_Reference('vimeo_options_subfield_' . $fieldIndex++));
        }

        $container->register('vimeo_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_SpectrumColorField')
            ->addArgument(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_impl_ioc_Reference('vimeo_options_field_' . $x);
        }

        $map = array(

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_GALLERYSOURCE => array(

                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
                tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_PLAYER => array(

                tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_FEED => array(

                tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY,
                tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET,
            ),
        );

        $container->register(

            'vimeo_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->addArgument('vimeo_participant')
            ->addArgument('Vimeo')   //>(translatable)<
            ->addArgument(array())
            ->addArgument($fieldReferences)
            ->addArgument($map)
            ->addTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _registerListeners(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION, 'method' => 'onVideoConstruction', 'priority' => 10000));

        $container->register(

            'vimeo_color_sanitizer',
            'tubepress_impl_listeners_options_ColorSanitizingListener'

        )->addArgument(array(
                tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR
            ))
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, 'method' => 'onPreValidationOptionSet', 'priority' => 9500));

        $this->_registerHttpListeners($container);
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
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::REQUEST, 'method' => 'onRequest', 'priority' => 9000));
    }

    private function _registerOauthClient(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'ehough_coauthor_spi_v1_TemporaryCredentialsStorageInterface',
            'ehough_coauthor_impl_v1_SessionCredentialsStorage'
        )->addArgument(false);

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