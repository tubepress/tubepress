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
class tubepress_addons_youtube_impl_ioc_YouTubeIocContainerExtension implements tubepress_api_ioc_ContainerExtensionInterface
{
    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder A tubepress_api_ioc_ContainerBuilderInterface instance.
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerPluggables($containerBuilder);

        $this->_registerListeners($containerBuilder);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener',
            'tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION, 'method' => 'onVideoConstruction', 'priority' => 10000));

        $containerBuilder->register(

            'tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistHandler',
            'tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistHandler'
        )->addTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE,
            'method'   => 'onPreValidationOptionSet',
            'priority' => 10000
        ));
    }

    private function _registerPluggables(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {

        $containerBuilder->register(

            'tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder',
            'tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder'
        );

        $containerBuilder->register(

            'tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService',
            'tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService'

        )->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $containerBuilder->register(

            'tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService',
            'tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService'

        )->addArgument(new tubepress_impl_ioc_Reference('tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder'))
         ->addTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $containerBuilder->register(

            'tubepress_addons_youtube_impl_options_YouTubeOptionProvider',
            'tubepress_addons_youtube_impl_options_YouTubeOptionProvider'
        )->addTag(tubepress_spi_options_OptionProvider::_);

        $this->_registerOptionsPageParticipant($containerBuilder);
    }

    private function _registerOptionsPageParticipant(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldIndex = 0;

        $containerBuilder->register('youtube_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
            ->addArgument(tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY)
            ->addMethodCall('setSize', array(120));

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

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                'tubepress_impl_options_ui_fields_DropdownField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE),

            array(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
                'tubepress_impl_options_ui_fields_TextField',
                tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_RELATED_VALUE),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $containerBuilder->register('youtube_options_subfield_' . $fieldIndex, $gallerySourceFieldArray[1])->addArgument($gallerySourceFieldArray[2]);

            $containerBuilder->register('youtube_options_field_' . $fieldIndex, 'tubepress_impl_options_ui_fields_GallerySourceRadioField')
                ->addArgument($gallerySourceFieldArray[0])
                ->addArgument(new tubepress_impl_ioc_Reference('youtube_options_subfield_' . $fieldIndex++));
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

            $containerBuilder->register('youtube_options_field_' . $fieldIndex++, $class)->addArgument($id);
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
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
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

        $containerBuilder->register(

            'youtube_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->addArgument('youtube_participant')
            ->addArgument('YouTube')   //>(translatable)<
            ->addArgument(array())
            ->addArgument($fieldReferences)
            ->addArgument($map)
            ->addTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }
}