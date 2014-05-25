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
class tubepress_youtube_impl_ioc_YouTubeExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
        $containerBuilder->register(

            'tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider',
            'tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addTag(tubepress_core_api_embedded_EmbeddedProviderInterface::_);

        $containerBuilder->register(

            'tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler',
            'tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE,
            'method'   => 'onPreValidationOptionSet',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener',
            'tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_util_TimeUtilsInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_CONSTRUCTION,
            'method' => 'onVideoConstruction',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_youtube_impl_options_YouTubeOptionProvider',
            'tubepress_youtube_impl_options_YouTubeOptionProvider'
        )->addTag(tubepress_core_api_options_EasyProviderInterface::_);

        $containerBuilder->register(

            'tubepress_youtube_impl_player_YouTubePlayerLocation',
            'tubepress_youtube_impl_player_YouTubePlayerLocation'
        )->addTag(tubepress_core_api_player_PlayerLocationInterface::_);

        $containerBuilder->register(

            'tubepress_youtube_impl_provider_YouTubeVideoProvider',
            'tubepress_youtube_impl_provider_YouTubeVideoProvider'
        )
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_core_api_provider_EasyHttpProviderInterface::_);

        $fieldIndex = 0;

        $containerBuilder->register(

            'youtube_options_field_' . $fieldIndex++,
            'tubepress_core_api_options_ui_FieldInterface'
        )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_youtube_api_const_options_Names::DEV_KEY)
         ->addArgument('text')
         ->addArgument(array('size' => 120));

        $gallerySourceMap = array(

            array(
                tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH,
                'text',
                tubepress_youtube_api_const_options_Names::YOUTUBE_TAG_VALUE),

            array(tubepress_youtube_api_const_options_Values::YOUTUBE_USER,
                'text',
                tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE),

            array(tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
                'text',
                tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE),

            array(tubepress_youtube_api_const_options_Values::YOUTUBE_FAVORITES,
                'text',
                tubepress_youtube_api_const_options_Names::YOUTUBE_FAVORITES_VALUE),

            array(tubepress_youtube_api_const_options_Values::YOUTUBE_MOST_POPULAR,
                'dropdown',
                tubepress_youtube_api_const_options_Names::YOUTUBE_MOST_POPULAR_VALUE),

            array(tubepress_youtube_api_const_options_Values::YOUTUBE_RELATED,
                'text',
                tubepress_youtube_api_const_options_Names::YOUTUBE_RELATED_VALUE),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $containerBuilder->register(

                'youtube_options_subfield_' . $fieldIndex,
                'tubepress_core_api_options_ui_FieldInterface'
            )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[2])
             ->addArgument($gallerySourceFieldArray[1]);

            $containerBuilder->register(

                'youtube_options_field_' . $fieldIndex,
                'tubepress_core_api_options_ui_FieldInterface'
            )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[0])
             ->addArgument('gallerySourceRadio')
             ->addArgument(array('additionalField' => new tubepress_api_ioc_Reference('youtube_options_subfield_' . $fieldIndex++)));
        }

        $fieldMap = array(

            tubepress_youtube_api_const_options_Names::AUTOHIDE         => 'dropdown',
            tubepress_youtube_api_const_options_Names::CLOSED_CAPTIONS  => 'bool',
            tubepress_youtube_api_const_options_Names::DISABLE_KEYBOARD => 'bool',
            tubepress_youtube_api_const_options_Names::FULLSCREEN       => 'bool',
            tubepress_youtube_api_const_options_Names::MODEST_BRANDING  => 'bool',
            tubepress_youtube_api_const_options_Names::SHOW_ANNOTATIONS => 'bool',
            tubepress_youtube_api_const_options_Names::SHOW_RELATED     => 'bool',
            tubepress_youtube_api_const_options_Names::THEME            => 'dropdown',
            tubepress_youtube_api_const_options_Names::SHOW_CONTROLS    => 'dropdown',

            //Feed fields
            tubepress_youtube_api_const_options_Names::FILTER               => 'dropdown',
            tubepress_youtube_api_const_options_Names::EMBEDDABLE_ONLY      => 'bool',
        );

        foreach ($fieldMap as $id => $class) {

            $containerBuilder->register(

                'youtube_options_field_' . $fieldIndex++,
                'tubepress_core_api_options_ui_FieldInterface'
            )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
             ->setFactoryMethod('newInstance')
             ->addArgument($id)
             ->addArgument($class);
        }

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_api_ioc_Reference('youtube_options_field_' . $x);
        }

        $containerBuilder->register(

            'tubepress_youtube_impl_options_ui_YouTubeFieldProvider',
            'tubepress_youtube_impl_options_ui_YouTubeFieldProvider'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->addArgument($fieldReferences)
         ->addTag('tubepress_core_api_options_ui_FieldProviderInterface');
    }
}