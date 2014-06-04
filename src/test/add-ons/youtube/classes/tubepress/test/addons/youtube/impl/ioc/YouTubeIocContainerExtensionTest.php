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
 * @covers tubepress_youtube_impl_ioc_YouTubeExtension<extended>
 */
class tubepress_test_youtube_impl_ioc_YouTubeIocContainerExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_youtube_ioc_YouTubeExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider',
            'tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withTag(tubepress_core_embedded_api_EmbeddedProviderInterface::_);

        $this->expectRegistration(
            'tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler',
            'tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET . '.' . tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE,
                'method'   => 'onPreValidationOptionSet',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener',
            'tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_util_api_TimeUtilsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event' => tubepress_core_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
                'method' => 'onVideoConstruction',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_youtube_impl_player_YouTubePlayerLocation',
            'tubepress_youtube_impl_player_YouTubePlayerLocation'
        );

        $this->expectRegistration(

            'tubepress_youtube_impl_provider_YouTubeVideoProvider',
            'tubepress_youtube_impl_provider_YouTubeVideoProvider'
        )
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withTag(tubepress_core_provider_api_HttpProviderInterface::_);

        $fieldIndex = 0;

        $this->expectRegistration(

            'youtube_options_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_youtube_api_Constants::OPTION_DEV_KEY)
            ->withArgument('text')
            ->withArgument(array('size' => 120));

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

            $this->expectRegistration(

                'youtube_options_subfield_' . $fieldIndex,
                'tubepress_core_options_ui_api_FieldInterface'
            )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[2])
                ->withArgument($gallerySourceFieldArray[1]);

            $this->expectRegistration(

                'youtube_options_field_' . $fieldIndex,
                'tubepress_core_options_ui_api_FieldInterface'
            )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[0])
                ->withArgument('gallerySourceRadio')
                ->withArgument(array('additionalField' => new tubepress_api_ioc_Reference('youtube_options_subfield_' . $fieldIndex++)));
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

            $this->expectRegistration(

                'youtube_options_field_' . $fieldIndex++,
                'tubepress_core_options_ui_api_FieldInterface'
            )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($id)
                ->withArgument($class);
        }

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_api_ioc_Reference('youtube_options_field_' . $x);
        }

        $this->expectRegistration(

            'tubepress_youtube_impl_options_ui_YouTubeFieldProvider',
            'tubepress_youtube_impl_options_ui_YouTubeFieldProvider'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument($fieldReferences)
            ->withTag('tubepress_core_options_ui_api_FieldProviderInterface');


    }

    protected function getExpectedExternalServicesMap()
    {
        $mockField = $this->mock('tubepress_core_options_ui_api_FieldInterface');
        $mockFieldBuilder = $this->mock(tubepress_core_options_ui_api_FieldBuilderInterface::_);
        $mockFieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(

            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_api_util_LangUtilsInterface::_ => tubepress_api_util_LangUtilsInterface::_,
            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_,
            tubepress_core_url_api_UrlFactoryInterface::_ => tubepress_core_url_api_UrlFactoryInterface::_,
            tubepress_core_util_api_TimeUtilsInterface::_ => tubepress_core_util_api_TimeUtilsInterface::_,
            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_core_options_ui_api_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_core_translation_api_TranslatorInterface::_ => tubepress_core_translation_api_TranslatorInterface::_
        );
    }
}