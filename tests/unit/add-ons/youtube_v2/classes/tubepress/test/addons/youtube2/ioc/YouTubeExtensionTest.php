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
 * @covers tubepress_youtube2_ioc_YouTubeExtension<extended>
 */
class tubepress_test_youtube2_ioc_YouTubeExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_youtube2_ioc_YouTubeExtension();
    }

    protected function prepareForLoad()
    {
        $this->_expectEmbedded();
        $this->_expectListeners();
        $this->_expectMediaProvider();
        $this->_expectOptions();
        $this->_expectOptionsUi();
    }

    private function _expectEmbedded()
    {
        $this->expectRegistration(
            'tubepress_youtube2_impl_embedded_YouTubeEmbeddedProvider',
            'tubepress_youtube2_impl_embedded_YouTubeEmbeddedProvider'

        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->withTag('tubepress_app_api_embedded_EmbeddedProviderInterface')
            ->withTag('tubepress_lib_api_template_PathProviderInterface');
    }

    private function _expectListeners()
    {
        $this->expectRegistration(
            'tubepress_youtube2_impl_listeners_media_HttpItemListener',
            'tubepress_youtube2_impl_listeners_media_HttpItemListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_AttributeFormatterInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_util_TimeUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::MEDIA_ITEM_HTTP_NEW,
                'method'   => 'onHttpItem',
                'priority' => 30000
            ));

        $this->expectRegistration(
            'tubepress_youtube2_impl_listeners_options_PlaylistIdListener',
            'tubepress_youtube2_impl_listeners_options_PlaylistIdListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::OPTION_SET . '.' . tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE,
                'method'   => 'onPreValidationOptionSet',
                'priority' => 40000
            ));

        $fixedValues = array(
            tubepress_youtube2_api_Constants::OPTION_AUTOHIDE => array(
                tubepress_youtube2_api_Constants::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS => 'Fade progress bar only',               //>(translatable)<
                tubepress_youtube2_api_Constants::AUTOHIDE_HIDE_BOTH              => 'Fade progress bar and video controls', //>(translatable)<
                tubepress_youtube2_api_Constants::AUTOHIDE_SHOW_BOTH              => 'Disable fading - always show both'     //>(translatable)<
            ),
            tubepress_youtube2_api_Constants::OPTION_SHOW_CONTROLS => array(
                tubepress_youtube2_api_Constants::CONTROLS_SHOW_IMMEDIATE_FLASH => 'Show controls - load Flash player immediately',          //>(translatable)<
                tubepress_youtube2_api_Constants::CONTROLS_SHOW_DELAYED_FLASH   => 'Show controls - load Flash player when playback begins', //>(translatable)<
                tubepress_youtube2_api_Constants::CONTROLS_HIDE                 => 'Hide controls',                                          //>(translatable)<
            ),
            tubepress_youtube2_api_Constants::OPTION_THEME => array(
                tubepress_youtube2_api_Constants::PLAYER_THEME_DARK  => 'Dark',     //>(translatable)<
                tubepress_youtube2_api_Constants::PLAYER_THEME_LIGHT => 'Light'    //>(translatable)<
            ),
            tubepress_youtube2_api_Constants::OPTION_FILTER => array(
                tubepress_youtube2_api_Constants::SAFESEARCH_NONE     => 'none',     //>(translatable)<
                tubepress_youtube2_api_Constants::SAFESEARCH_MODERATE => 'moderate', //>(translatable)<
                tubepress_youtube2_api_Constants::SAFESEARCH_STRICT   => 'strict',   //>(translatable)<
            ),
            tubepress_youtube2_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE => array(
                tubepress_youtube2_api_Constants::TIMEFRAME_ALL_TIME   => 'all time',        //>(translatable)<
                tubepress_youtube2_api_Constants::TIMEFRAME_TODAY      => 'today',           //>(translatable)<
            )
        );

        foreach ($fixedValues as $optionName => $values) {

            $this->expectRegistration(
                "fixed_values.$optionName",
                'tubepress_app_api_listeners_options_FixedValuesListener'
            )->withArgument($values)
                ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
                    'priority' => 30000,
                    'method'   => 'onAcceptableValues'
                ));
        }

        $validators = array(
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN => array(
                tubepress_youtube2_api_Constants::OPTION_DEV_KEY,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE,
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_YOUTUBE_VIDEO_ID => array(
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_RELATED_VALUE
            )
        );

        foreach ($validators as $type => $optionNames) {
            foreach ($optionNames as $optionName) {

                $this->expectRegistration(
                    "regex_validation.$optionName",
                    'tubepress_app_api_listeners_options_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_));
            }
        }
    }

    private function _expectMediaProvider()
    {
        $this->expectRegistration(
            'tubepress_youtube2_impl_media_FeedHandler',
            'tubepress_youtube2_impl_media_FeedHandler'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_));

        $this->expectRegistration(
            'tubepress_youtube2_impl_media_MediaProvider',
            'tubepress_youtube2_impl_media_MediaProvider'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_HttpCollectorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('tubepress_youtube2_impl_media_FeedHandler'))
            ->withTag(tubepress_app_api_media_MediaProviderInterface::__);
    }

    private function _expectOptions()
    {
        $this->expectRegistration(
            'tubepress_app_api_options_Reference__youtube',
            'tubepress_app_api_options_Reference'
        )->withTag(tubepress_app_api_options_ReferenceInterface::_)
         ->withArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_youtube2_api_Constants::OPTION_AUTOHIDE                   => tubepress_youtube2_api_Constants::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS,
                    tubepress_youtube2_api_Constants::OPTION_CLOSED_CAPTIONS            => false,
                    tubepress_youtube2_api_Constants::OPTION_DISABLE_KEYBOARD           => false,
                    tubepress_youtube2_api_Constants::OPTION_FULLSCREEN                 => true,
                    tubepress_youtube2_api_Constants::OPTION_MODEST_BRANDING            => true,
                    tubepress_youtube2_api_Constants::OPTION_SHOW_ANNOTATIONS           => false,
                    tubepress_youtube2_api_Constants::OPTION_SHOW_CONTROLS              => tubepress_youtube2_api_Constants::CONTROLS_SHOW_IMMEDIATE_FLASH,
                    tubepress_youtube2_api_Constants::OPTION_SHOW_RELATED               => true,
                    tubepress_youtube2_api_Constants::OPTION_THEME                      => tubepress_youtube2_api_Constants::PLAYER_THEME_DARK,
                    tubepress_youtube2_api_Constants::OPTION_DEV_KEY                    => 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg',
                    tubepress_youtube2_api_Constants::OPTION_EMBEDDABLE_ONLY            => true,
                    tubepress_youtube2_api_Constants::OPTION_FILTER                     => tubepress_youtube2_api_Constants::SAFESEARCH_NONE,
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE => tubepress_youtube2_api_Constants::TIMEFRAME_TODAY,
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_RELATED_VALUE      => 'P9M__yYbsZ4',
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE     => 'F679CB240DD4C112',
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE    => 'techcrunch',
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_TAG_VALUE          => 'iphone ios',
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE         => 'apple',
                    tubepress_youtube2_api_Constants::OPTION_RATING                     => false,
                    tubepress_youtube2_api_Constants::OPTION_RATINGS                    => false,
                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_youtube2_api_Constants::OPTION_AUTOHIDE                   => 'Fade progress bar and video controls', //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_CLOSED_CAPTIONS            => 'Show closed captions by default',       //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_DISABLE_KEYBOARD           => 'Disable keyboard controls',            //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_FULLSCREEN                 => 'Allow fullscreen playback.',           //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_MODEST_BRANDING            => '"Modest" branding',                    //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_SHOW_ANNOTATIONS           => 'Show video annotations by default',    //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_SHOW_CONTROLS              => 'Show or hide video controls',          //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_SHOW_RELATED               => 'Show related videos',                  //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_THEME                      => 'YouTube player theme',                 //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_DEV_KEY                    => 'YouTube API Developer Key',            //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_EMBEDDABLE_ONLY            => 'Only retrieve embeddable videos',      //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_FILTER                     => 'Filter "racy" content',                //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE => 'Most-viewed YouTube videos from',      //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_RELATED_VALUE      => 'Videos related to this YouTube video', //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE     => 'This YouTube playlist',                //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE    => 'This YouTube user\'s "favorites"',     //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_TAG_VALUE          => 'YouTube search for',                   //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE         => 'Videos from this YouTube user',        //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_RATING                     => 'Average rating',                       //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_RATINGS                    => 'Number of ratings',                    //>(translatable)<
                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_youtube2_api_Constants::OPTION_AUTOHIDE               => 'After video playback begins, choose which elements (if any) of the embedded video player to automatically hide.', //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_MODEST_BRANDING        => 'Hide the YouTube logo from the control area.',                    //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_SHOW_RELATED           => 'Toggles the display of related videos after a video finishes.',   //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_DEV_KEY                => sprintf('YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="%s" target="_blank">here</a>. Don\'t change this unless you know what you\'re doing.', //>(translatable)<
                        'http://code.google.com/apis/youtube/dashboard/'),
                    tubepress_youtube2_api_Constants::OPTION_EMBEDDABLE_ONLY        => 'Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.', //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_FILTER                 => 'Don\'t show videos that may not be suitable for minors.',         //>(translatable)<
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE => sprintf('The URL to any YouTube playlist (e.g. <a href="%s" target="_blank">%s</a>) or just the playlist identifier (e.g. %s).',  //>(translatable)<
                        'http://youtube.com/playlist?list=48A83AD3506C9D36', 'http://youtube.com/playlist?list=48A83AD3506C9D36', '48A83AD3506C9D36'),
                    tubepress_youtube2_api_Constants::OPTION_YOUTUBE_TAG_VALUE      => 'YouTube limits this to 1,000 results.',                           //>(translatable)<
                )))
        ->withArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_PRO_ONLY => array(

                    tubepress_youtube2_api_Constants::OPTION_CLOSED_CAPTIONS,
                    tubepress_youtube2_api_Constants::OPTION_DISABLE_KEYBOARD,
                    tubepress_youtube2_api_Constants::OPTION_SHOW_ANNOTATIONS,
                    tubepress_youtube2_api_Constants::OPTION_SHOW_CONTROLS,
                    tubepress_youtube2_api_Constants::OPTION_THEME
                )
            ));
    }

    private function _expectOptionsUi()
    {
        $fieldIndex = 0;

        $this->expectRegistration(

            'youtube_options_field_' . $fieldIndex++,
            'tubepress_app_api_options_ui_FieldInterface'
        )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_youtube2_api_Constants::OPTION_DEV_KEY)
            ->withArgument('text')
            ->withArgument(array('size' => 40));

        $gallerySourceMap = array(

            array(
                tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_SEARCH,
                'text',
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_TAG_VALUE),

            array(tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_USER,
                'text',
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_USER_VALUE),

            array(tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_PLAYLIST,
                'text',
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_PLAYLIST_VALUE),

            array(tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES,
                'text',
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_FAVORITES_VALUE),

            array(tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
                'dropdown',
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE),

            array(tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_RELATED,
                'text',
                tubepress_youtube2_api_Constants::OPTION_YOUTUBE_RELATED_VALUE),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $this->expectRegistration(

                'youtube_options_subfield_' . $fieldIndex,
                'tubepress_app_api_options_ui_FieldInterface'
            )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[2])
                ->withArgument($gallerySourceFieldArray[1]);

            $this->expectRegistration(

                'youtube_options_field_' . $fieldIndex,
                'tubepress_app_api_options_ui_FieldInterface'
            )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[0])
                ->withArgument('gallerySourceRadio')
                ->withArgument(array('additionalField' => new tubepress_platform_api_ioc_Reference('youtube_options_subfield_' . $fieldIndex++)));
        }

        $fieldMap = array(

            tubepress_youtube2_api_Constants::OPTION_AUTOHIDE         => 'dropdown',
            tubepress_youtube2_api_Constants::OPTION_CLOSED_CAPTIONS  => 'bool',
            tubepress_youtube2_api_Constants::OPTION_DISABLE_KEYBOARD => 'bool',
            tubepress_youtube2_api_Constants::OPTION_FULLSCREEN       => 'bool',
            tubepress_youtube2_api_Constants::OPTION_MODEST_BRANDING  => 'bool',
            tubepress_youtube2_api_Constants::OPTION_SHOW_ANNOTATIONS => 'bool',
            tubepress_youtube2_api_Constants::OPTION_SHOW_RELATED     => 'bool',
            tubepress_youtube2_api_Constants::OPTION_THEME            => 'dropdown',
            tubepress_youtube2_api_Constants::OPTION_SHOW_CONTROLS    => 'dropdown',

            //Feed fields
            tubepress_youtube2_api_Constants::OPTION_FILTER               => 'dropdown',
            tubepress_youtube2_api_Constants::OPTION_EMBEDDABLE_ONLY      => 'bool',
        );

        foreach ($fieldMap as $id => $class) {

            $this->expectRegistration(

                'youtube_options_field_' . $fieldIndex++,
                'tubepress_app_api_options_ui_FieldInterface'
            )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($id)
                ->withArgument($class);
        }

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('youtube_options_field_' . $x);
        }

        $this->expectRegistration(

            'tubepress_youtube2_impl_options_ui_FieldProvider',
            'tubepress_youtube2_impl_options_ui_FieldProvider'

        )->withArgument($fieldReferences)
            ->withTag('tubepress_app_api_options_ui_FieldProviderInterface');

    }

    protected function getExpectedExternalServicesMap()
    {
        $mockField = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $mockFieldBuilder = $this->mock(tubepress_app_api_options_ui_FieldBuilderInterface::_);
        $mockFieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(

            tubepress_app_api_options_ContextInterface::_         => tubepress_app_api_options_ContextInterface::_,
            tubepress_platform_api_util_LangUtilsInterface::_     => tubepress_platform_api_util_LangUtilsInterface::_,
            tubepress_platform_api_util_StringUtilsInterface::_   => tubepress_platform_api_util_StringUtilsInterface::_,
            tubepress_platform_api_url_UrlFactoryInterface::_     => tubepress_platform_api_url_UrlFactoryInterface::_,
            tubepress_lib_api_util_TimeUtilsInterface::_          => tubepress_lib_api_util_TimeUtilsInterface::_,
            tubepress_platform_api_log_LoggerInterface::_         => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_lib_api_event_EventDispatcherInterface::_   => tubepress_lib_api_event_EventDispatcherInterface::_,
            tubepress_app_api_options_ui_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_lib_api_translation_TranslatorInterface::_  => tubepress_lib_api_translation_TranslatorInterface::_,
            tubepress_app_api_media_AttributeFormatterInterface::_ => tubepress_app_api_media_AttributeFormatterInterface::_,
            tubepress_app_api_media_HttpCollectorInterface::_ => tubepress_app_api_media_HttpCollectorInterface::_,
            tubepress_app_api_options_ReferenceInterface::_ => tubepress_app_api_options_ReferenceInterface::_
        );
    }
}