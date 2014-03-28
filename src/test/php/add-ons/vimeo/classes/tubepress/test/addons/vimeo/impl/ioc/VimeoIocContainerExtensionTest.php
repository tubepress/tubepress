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
 * @covers tubepress_addons_vimeo_impl_ioc_VimeoIocContainerExtension
 */
class tubepress_test_addons_vimeo_impl_ioc_VimeoIocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
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
        $this->_expectPluggables();
        $this->_expectListeners();
        $this->_expectHttpListenerRegistration();
        $this->_expectOauthClientRegistration();
    }

    private function _expectPluggables()
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

            'tubepress_addons_vimeo_impl_options_VimeoOptionProvider',
            'tubepress_addons_vimeo_impl_options_VimeoOptionProvider'
        )->withTag(tubepress_spi_options_OptionProvider::_);

        $this->_expectOptionsPageParticipant();
    }

    private function _expectOptionsPageParticipant()
    {
        $fieldIndex = 0;

        $this->expectRegistration('vimeo_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
            ->withArgument(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY)
            ->withMethodCall('setSize', array(40));

        $this->expectRegistration('vimeo_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
            ->withArgument(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET)
            ->withMethodCall('setSize', array(40));

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

            $this->expectRegistration('vimeo_options_subfield_' . $fieldIndex, $gallerySourceFieldArray[1])->withArgument($gallerySourceFieldArray[2]);

            $this->expectRegistration('vimeo_options_field_' . $fieldIndex, 'tubepress_impl_options_ui_fields_GallerySourceRadioField')
                ->withArgument($gallerySourceFieldArray[0])
                ->withArgument(new tubepress_impl_ioc_Reference('vimeo_options_subfield_' . $fieldIndex++));
        }

        $this->expectRegistration('vimeo_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_SpectrumColorField')
            ->withArgument(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);

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

        $this->expectRegistration(

            'vimeo_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->withArgument('vimeo_participant')
            ->withArgument('Vimeo')   //>(translatable)<
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($map)
            ->withTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _expectListeners()
    {
        $this->expectRegistration(

            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION, 'method' => 'onVideoConstruction', 'priority' => 10000));

        $this->expectRegistration(

            'vimeo_color_sanitizer',
            'tubepress_impl_listeners_options_ColorSanitizingListener'

        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR,
            'method'   => 'onPreValidationOptionSet',
            'priority' => 9500
        ));
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
        )->withArgument(false);

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