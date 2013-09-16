<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_addons_youtube_impl_ioc_YouTubeIocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_addons_youtube_impl_ioc_YouTubeIocContainerExtension();
    }

    protected function prepareForLoad()
    {
        $this->_expectPluggables();

        $this->_expectListeners();
    }

    private function _expectPluggables()
    {
        $this->expectRegistration(

            'tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder',
            'tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder'
        );

        $this->expectRegistration(

            'tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService',
            'tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService'

        )->withTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $this->expectRegistration(

            'tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService',
            'tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService'

        )->withArgument(new ehough_iconic_Reference('tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder'))
            ->withTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $this->expectRegistration(

            'tubepress_addons_youtube_impl_options_YouTubeOptionsProvider',
            'tubepress_addons_youtube_impl_options_YouTubeOptionsProvider'
        )->withTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $this->_expectOptionsPageParticipant();
    }

    private function _expectListeners()
    {
        $this->expectRegistration(

            'tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener',
            'tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION, 'method' => 'onVideoConstruction', 'priority' => 10000));;

        $this->expectRegistration(

            'tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener',
            'tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse', 'priority' => 10000));;

        $this->expectRegistration(

            'tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistPlPrefixRemover',
            'tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistPlPrefixRemover'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, 'method' => 'onPreValidationOptionSet', 'priority' => 10000));
    }

    private function _expectOptionsPageParticipant()
    {
        $fieldIndex = 0;

        $this->expectRegistration('youtube_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
            ->withArgument(tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY)
            ->withMethodCall('setSize', array(120));

        $gallerySourceMap = array(

            array(
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_FEATURED_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TRENDING_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_SHARED_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_RELATED_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_RESPONSES_VALUE),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $this->expectRegistration('youtube_options_subfield_' . $fieldIndex, $gallerySourceFieldArray[1])->withArgument($gallerySourceFieldArray[2]);

            $this->expectRegistration('youtube_options_field_' . $fieldIndex, 'tubepress_impl_options_ui_fields_GallerySourceRadioField')
                ->withArgument($gallerySourceFieldArray[0])
                ->withArgument(new tubepress_impl_ioc_Reference('youtube_options_subfield_' . $fieldIndex++));
        }

        $fieldMap = array(

            tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE         => 'tubepress_impl_options_ui_fields_DropdownField',
            tubepress_addons_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS  => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_addons_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN       => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING  => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED     => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_addons_youtube_api_const_options_names_Embedded::THEME            => 'tubepress_impl_options_ui_fields_DropdownField',
            tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_CONTROLS    => 'tubepress_impl_options_ui_fields_DropdownField',

            //Feed fields
            tubepress_addons_youtube_api_const_options_names_Feed::FILTER               => 'tubepress_impl_options_ui_fields_DropdownField',
            tubepress_addons_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY      => 'tubepress_impl_options_ui_fields_BooleanField',
        );


        foreach ($fieldMap as $id => $class) {

            $this->expectRegistration('youtube_options_field_' . $fieldIndex++, $class)->withArgument($id);
        }

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_impl_ioc_Reference('youtube_options_field_' . $x);
        }

        $map = array(

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_GALLERYSOURCE => array(

                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_PLAYER => array(

                tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE,
                tubepress_addons_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS,
                tubepress_addons_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD,
                tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN,
                tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING,
                tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS,
                tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED,
                tubepress_addons_youtube_api_const_options_names_Embedded::THEME,
                tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_CONTROLS,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_FEED => array(

                tubepress_addons_youtube_api_const_options_names_Feed::FILTER,
                tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY,
                tubepress_addons_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY,
            )
        );

        $this->expectRegistration(

            'youtube_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->withArgument('youtube_participant')
            ->withArgument('YouTube')   //>(translatable)<
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($map)
            ->withTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }
}