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
class tubepress_test_youtube_impl_ioc_YouTubeIocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_youtube_impl_ioc_YouTubeExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider',
            'tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withTag(tubepress_core_api_embedded_EmbeddedProviderInterface::_);

        $this->expectRegistration(

            'tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler',
            'tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE,
                'method'   => 'onPreValidationOptionSet',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener',
            'tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_util_TimeUtilsInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event' => tubepress_core_api_const_event_EventNames::VIDEO_CONSTRUCTION,
                'method' => 'onVideoConstruction',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'tubepress_youtube_impl_options_YouTubeOptionProvider',
            'tubepress_youtube_impl_options_YouTubeOptionProvider'
        )->withTag(tubepress_core_api_options_EasyProviderInterface::_);

        $this->expectRegistration(

            'tubepress_youtube_impl_player_YouTubePlayerLocation',
            'tubepress_youtube_impl_player_YouTubePlayerLocation'
        )->withTag(tubepress_core_api_player_PlayerLocationInterface::_);

        $this->expectRegistration(

            'tubepress_youtube_impl_provider_YouTubeVideoProvider',
            'tubepress_youtube_impl_provider_YouTubeVideoProvider'
        )
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_core_api_provider_EasyHttpProviderInterface::_);

        $fieldIndex = 0;

        $this->expectRegistration(

            'youtube_options_field_' . $fieldIndex++,
            'tubepress_core_api_options_ui_FieldInterface'
        )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_youtube_api_const_options_Names::DEV_KEY)
            ->withArgument('text')
            ->withArgument(array('size' => 120));

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

            $this->expectRegistration(

                'youtube_options_subfield_' . $fieldIndex,
                'tubepress_core_api_options_ui_FieldInterface'
            )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[2])
                ->withArgument($gallerySourceFieldArray[1]);

            $this->expectRegistration(

                'youtube_options_field_' . $fieldIndex,
                'tubepress_core_api_options_ui_FieldInterface'
            )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[0])
                ->withArgument('gallerySourceRadio')
                ->withArgument(array('additionalField' => new tubepress_api_ioc_Reference('youtube_options_subfield_' . $fieldIndex++)));
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

            $this->expectRegistration(

                'youtube_options_field_' . $fieldIndex++,
                'tubepress_core_api_options_ui_FieldInterface'
            )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
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

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
            ->withArgument($fieldReferences)
            ->withTag('tubepress_core_api_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockField = $this->mock('tubepress_core_api_options_ui_FieldInterface');
        $mockFieldBuilder = $this->mock(tubepress_core_api_options_ui_FieldBuilderInterface::_);
        $mockFieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(

            tubepress_core_api_options_ContextInterface::_ => tubepress_core_api_options_ContextInterface::_,
            tubepress_api_util_LangUtilsInterface::_ => tubepress_api_util_LangUtilsInterface::_,
            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_,
            tubepress_core_api_url_UrlFactoryInterface::_ => tubepress_core_api_url_UrlFactoryInterface::_,
            tubepress_core_api_util_TimeUtilsInterface::_ => tubepress_core_api_util_TimeUtilsInterface::_,
            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_core_api_event_EventDispatcherInterface::_ => tubepress_core_api_event_EventDispatcherInterface::_,
            tubepress_core_api_options_ui_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_core_api_translation_TranslatorInterface::_ => tubepress_core_api_translation_TranslatorInterface::_
        );
    }
}