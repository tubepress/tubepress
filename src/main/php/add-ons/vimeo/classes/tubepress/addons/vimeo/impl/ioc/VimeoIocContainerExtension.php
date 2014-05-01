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

        $this->_registerOauthClient($containerBuilder);
    }

    private function _registerPluggables(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService',
            'tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $containerBuilder->register(

            'tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder',
            'tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_));

        $containerBuilder->register(

            'tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService',
            'tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService'

        )->addArgument(new tubepress_impl_ioc_Reference('tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder'))
         ->addTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $containerBuilder->register(

            'tubepress_addons_vimeo_impl_options_VimeoOptionProvider',
            'tubepress_addons_vimeo_impl_options_VimeoOptionProvider'
        )->addTag(tubepress_spi_options_OptionProvider::_);

        $this->_registerOptionsPageParticipant($containerBuilder);
    }

    private function _registerOptionsPageParticipant(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldIndex = 0;

        $containerBuilder->register('vimeo_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
            ->addArgument(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY)
            ->addMethodCall('setSize', array(40));

        $containerBuilder->register('vimeo_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
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

            $containerBuilder->register('vimeo_options_subfield_' . $fieldIndex, $gallerySourceFieldArray[1])->addArgument($gallerySourceFieldArray[2]);

            $containerBuilder->register('vimeo_options_field_' . $fieldIndex, 'tubepress_impl_options_ui_fields_GallerySourceRadioField')
                ->addArgument($gallerySourceFieldArray[0])
                ->addArgument(new tubepress_impl_ioc_Reference('vimeo_options_subfield_' . $fieldIndex++));
        }

        $containerBuilder->register('vimeo_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_SpectrumColorField')
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

        $containerBuilder->register(

            'vimeo_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->addArgument('vimeo_participant')
            ->addArgument('Vimeo')   //>(translatable)<
            ->addArgument(array())
            ->addArgument($fieldReferences)
            ->addArgument($map)
            ->addTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION, 'method' => 'onVideoConstruction', 'priority' => 10000));

        $containerBuilder->register(

            'vimeo_color_sanitizer',
            'tubepress_impl_listeners_options_ColorSanitizingListener'

        )->addTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR,
                'method'   => 'onPreValidationOptionSet',
                'priority' => 9500
        ));
    }

    private function _registerOauthClient(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {

    }
}