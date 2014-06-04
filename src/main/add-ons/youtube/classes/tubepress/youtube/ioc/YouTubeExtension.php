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
class tubepress_youtube_ioc_YouTubeExtension implements tubepress_api_ioc_ContainerExtensionInterface
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

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addTag(tubepress_core_embedded_api_EmbeddedProviderInterface::_);

        $containerBuilder->register(
            'tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler',
            'tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET . '.' . tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE,
            'method'   => 'onPreValidationOptionSet',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener',
            'tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_util_api_TimeUtilsInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event' => tubepress_core_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
            'method' => 'onVideoConstruction',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_youtube_impl_player_YouTubePlayerLocation',
            'tubepress_youtube_impl_player_YouTubePlayerLocation'
        );

        $containerBuilder->register(

            'tubepress_youtube_impl_provider_YouTubeVideoProvider',
            'tubepress_youtube_impl_provider_YouTubeVideoProvider'
        )
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addTag(tubepress_core_provider_api_HttpProviderInterface::_);

        $fieldIndex = 0;

        $containerBuilder->register(

            'youtube_options_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_youtube_api_Constants::OPTION_DEV_KEY)
         ->addArgument('text')
         ->addArgument(array('size' => 120));

        $gallerySourceMap = array(

            array(
                tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                'text',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_TAG_VALUE),

            array(tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                'text',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE),

            array(tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                'text',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE),

            array(tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
                'text',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE),

            array(tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
                'dropdown',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE),

            array(tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED,
                'text',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_RELATED_VALUE),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $containerBuilder->register(

                'youtube_options_subfield_' . $fieldIndex,
                'tubepress_core_options_ui_api_FieldInterface'
            )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[2])
             ->addArgument($gallerySourceFieldArray[1]);

            $containerBuilder->register(

                'youtube_options_field_' . $fieldIndex,
                'tubepress_core_options_ui_api_FieldInterface'
            )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[0])
             ->addArgument('gallerySourceRadio')
             ->addArgument(array('additionalField' => new tubepress_api_ioc_Reference('youtube_options_subfield_' . $fieldIndex++)));
        }

        $fieldMap = array(

            tubepress_youtube_api_Constants::OPTION_AUTOHIDE         => 'dropdown',
            tubepress_youtube_api_Constants::OPTION_CLOSED_CAPTIONS  => 'bool',
            tubepress_youtube_api_Constants::OPTION_DISABLE_KEYBOARD => 'bool',
            tubepress_youtube_api_Constants::OPTION_FULLSCREEN       => 'bool',
            tubepress_youtube_api_Constants::OPTION_MODEST_BRANDING  => 'bool',
            tubepress_youtube_api_Constants::OPTION_SHOW_ANNOTATIONS => 'bool',
            tubepress_youtube_api_Constants::OPTION_SHOW_RELATED     => 'bool',
            tubepress_youtube_api_Constants::OPTION_THEME            => 'dropdown',
            tubepress_youtube_api_Constants::OPTION_SHOW_CONTROLS    => 'dropdown',

            //Feed fields
            tubepress_youtube_api_Constants::OPTION_FILTER               => 'dropdown',
            tubepress_youtube_api_Constants::OPTION_EMBEDDABLE_ONLY      => 'bool',
        );

        foreach ($fieldMap as $id => $class) {

            $containerBuilder->register(

                'youtube_options_field_' . $fieldIndex++,
                'tubepress_core_options_ui_api_FieldInterface'
            )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
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

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
         ->addArgument($fieldReferences)
         ->addTag('tubepress_core_options_ui_api_FieldProviderInterface');
    }
}