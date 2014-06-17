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
 * @covers tubepress_youtube_ioc_YouTubeExtension<extended>
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
                'event' => tubepress_core_media_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
                'method' => 'onVideoConstruction',
                'priority' => 40000
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
            ->withTag('tubepress_core_media_provider_api_HttpProviderInterface');

        $fieldIndex = 0;

        $this->expectRegistration(

            'youtube_options_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_youtube_api_Constants::OPTION_DEV_KEY)
            ->withArgument('text')
            ->withArgument(array('size' => 40));

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

        $this->expectParameter(tubepress_core_media_item_api_Constants::IOC_PARAM_EASY_ATTRIBUTE_FORMATTER . '_youtube', array(

            'priority'     => 30000,
            'providerName' => 'youtube',
            'map'          => array(

                array(
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_COUNT,
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_COUNT,
                    'number',
                    0,
                ),
                array(
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT,
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT,
                    'number',
                    0,
                ),
                array(
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_AVERAGE,
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_AVERAGE,
                    'number',
                    2,
                ),
                array(
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION,
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION,
                    'truncateString',
                    tubepress_core_media_item_api_Constants::OPTION_DESC_LIMIT
                ),
                array(
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_SECONDS,
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_FORMATTED,
                    'durationFromSeconds',
                ),
                array(
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME,
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
                    'dateFromUnixTime'
                ),
                array(
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_KEYWORD_ARRAY,
                    tubepress_core_media_item_api_Constants::ATTRIBUTE_KEYWORDS_FORMATTED,
                    'implodeArray',
                    ', ',
                )
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_youtube', array(

            'defaultValues' => array(
                tubepress_youtube_api_Constants::OPTION_AUTOHIDE                   => tubepress_youtube_api_Constants::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS,
                tubepress_youtube_api_Constants::OPTION_CLOSED_CAPTIONS            => false,
                tubepress_youtube_api_Constants::OPTION_DISABLE_KEYBOARD           => false,
                tubepress_youtube_api_Constants::OPTION_FULLSCREEN                 => true,
                tubepress_youtube_api_Constants::OPTION_MODEST_BRANDING            => true,
                tubepress_youtube_api_Constants::OPTION_SHOW_ANNOTATIONS           => false,
                tubepress_youtube_api_Constants::OPTION_SHOW_CONTROLS              => tubepress_youtube_api_Constants::CONTROLS_SHOW_IMMEDIATE_FLASH,
                tubepress_youtube_api_Constants::OPTION_SHOW_RELATED               => true,
                tubepress_youtube_api_Constants::OPTION_THEME                      => tubepress_youtube_api_Constants::PLAYER_THEME_DARK,
                tubepress_youtube_api_Constants::OPTION_DEV_KEY                    => 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg',
                tubepress_youtube_api_Constants::OPTION_EMBEDDABLE_ONLY            => true,
                tubepress_youtube_api_Constants::OPTION_FILTER                     => tubepress_youtube_api_Constants::SAFESEARCH_NONE,
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE => tubepress_youtube_api_Constants::TIMEFRAME_TODAY,
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_RELATED_VALUE      => 'P9M__yYbsZ4',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE     => 'F679CB240DD4C112',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE    => 'techcrunch',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_TAG_VALUE          => 'iphone ios',
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE         => 'apple',
                tubepress_youtube_api_Constants::OPTION_RATING                     => false,
                tubepress_youtube_api_Constants::OPTION_RATINGS                    => false,
            ),

            'labels' => array(
                tubepress_youtube_api_Constants::OPTION_AUTOHIDE                   => 'Fade progress bar and video controls', //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_CLOSED_CAPTIONS            => 'Show closed captions by default',       //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_DISABLE_KEYBOARD           => 'Disable keyboard controls',            //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_FULLSCREEN                 => 'Allow fullscreen playback.',           //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_MODEST_BRANDING            => '"Modest" branding',                    //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_SHOW_ANNOTATIONS           => 'Show video annotations by default',    //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_SHOW_CONTROLS              => 'Show or hide video controls',          //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_SHOW_RELATED               => 'Show related videos',                  //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_THEME                      => 'YouTube player theme',                 //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_DEV_KEY                    => 'YouTube API Developer Key',            //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_EMBEDDABLE_ONLY            => 'Only retrieve embeddable videos',      //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_FILTER                     => 'Filter "racy" content',                //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE => 'Most-viewed YouTube videos from',      //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_RELATED_VALUE      => 'Videos related to this YouTube video', //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE     => 'This YouTube playlist',                //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE    => 'This YouTube user\'s "favorites"',     //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_TAG_VALUE          => 'YouTube search for',                   //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE         => 'Videos from this YouTube user',        //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_RATING                     => 'Average rating',                       //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_RATINGS                    => 'Number of ratings',                    //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_youtube_api_Constants::OPTION_AUTOHIDE               => 'After video playback begins, choose which elements (if any) of the embedded video player to automatically hide.', //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_MODEST_BRANDING        => 'Hide the YouTube logo from the control area.',                    //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_SHOW_RELATED           => 'Toggles the display of related videos after a video finishes.',   //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_DEV_KEY                => 'YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="%s" target="_blank">here</a>. Don\'t change this unless you know what you\'re doing.', //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_EMBEDDABLE_ONLY        => 'Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.', //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_FILTER                 => 'Don\'t show videos that may not be suitable for minors.',         //>(translatable)<
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => sprintf('The URL to any YouTube playlist (e.g. <a href="%s" target="_blank">%s</a>) or just the playlist identifier (e.g. %s).',  //>(translatable)<
                    'http://youtube.com/playlist?list=48A83AD3506C9D36', 'http://youtube.com/playlist?list=48A83AD3506C9D36', '48A83AD3506C9D36'),
                tubepress_youtube_api_Constants::OPTION_YOUTUBE_TAG_VALUE      => 'YouTube limits this to 1,000 results.',                           //>(translatable)<
            ),

            'proOptionNames' => array(

                tubepress_youtube_api_Constants::OPTION_CLOSED_CAPTIONS,
                tubepress_youtube_api_Constants::OPTION_DISABLE_KEYBOARD,
                tubepress_youtube_api_Constants::OPTION_SHOW_ANNOTATIONS,
                tubepress_youtube_api_Constants::OPTION_SHOW_CONTROLS,
                tubepress_youtube_api_Constants::OPTION_THEME
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES . '_' . tubepress_youtube_api_Constants::OPTION_AUTOHIDE, array(

            'optionName' => tubepress_youtube_api_Constants::OPTION_AUTOHIDE,
            'priority'   => 30000,
            'values'     => array(

                tubepress_youtube_api_Constants::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS => 'Fade progress bar only',               //>(translatable)<
                tubepress_youtube_api_Constants::AUTOHIDE_HIDE_BOTH              => 'Fade progress bar and video controls', //>(translatable)<
                tubepress_youtube_api_Constants::AUTOHIDE_SHOW_BOTH              => 'Disable fading - always show both'     //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES . '_' . tubepress_youtube_api_Constants::OPTION_SHOW_CONTROLS, array(

            'optionName' => tubepress_youtube_api_Constants::OPTION_SHOW_CONTROLS,
            'priority'   => 30000,
            'values'     => array(

                tubepress_youtube_api_Constants::CONTROLS_SHOW_IMMEDIATE_FLASH => 'Show controls - load Flash player immediately',          //>(translatable)<
                tubepress_youtube_api_Constants::CONTROLS_SHOW_DELAYED_FLASH   => 'Show controls - load Flash player when playback begins', //>(translatable)<
                tubepress_youtube_api_Constants::CONTROLS_HIDE                 => 'Hide controls',                                          //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES . '_' . tubepress_youtube_api_Constants::OPTION_THEME, array(

            'optionName' => tubepress_youtube_api_Constants::OPTION_THEME,
            'priority'   => 30000,
            'values'     => array(

                tubepress_youtube_api_Constants::PLAYER_THEME_DARK  => 'Dark',     //>(translatable)<
                tubepress_youtube_api_Constants::PLAYER_THEME_LIGHT => 'Light'    //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES . '_' . tubepress_youtube_api_Constants::OPTION_FILTER, array(

            'optionName' => tubepress_youtube_api_Constants::OPTION_FILTER,
            'priority'   => 30000,
            'values'     => array(

                tubepress_youtube_api_Constants::SAFESEARCH_NONE     => 'none',     //>(translatable)<
                tubepress_youtube_api_Constants::SAFESEARCH_MODERATE => 'moderate', //>(translatable)<
                tubepress_youtube_api_Constants::SAFESEARCH_STRICT   => 'strict',   //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES . '_' . tubepress_youtube_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE, array(

            'optionName' => tubepress_youtube_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE,
            'priority'   => 30000,
            'values'     => array(

                tubepress_youtube_api_Constants::TIMEFRAME_ALL_TIME   => 'all time',        //>(translatable)<
                tubepress_youtube_api_Constants::TIMEFRAME_TODAY      => 'today',           //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_youtube', array(

            'priority' => 30000,
            'map'      => array(
                'oneOrMoreWordCharsPlusHyphen' => array(
                    tubepress_youtube_api_Constants::OPTION_DEV_KEY,
                    tubepress_youtube_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE,
                    tubepress_youtube_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE,
                    tubepress_youtube_api_Constants::OPTION_YOUTUBE_USER_VALUE
                ),
                'youTubeVideoId' => array(
                    tubepress_youtube_api_Constants::OPTION_YOUTUBE_RELATED_VALUE
                )
            )
        ));

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