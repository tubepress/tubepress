<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_dailymotion_ioc_DailymotionExtension
 */
class tubepress_test_dailymotion_ioc_DailymotionExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{

    /**
     * @return tubepress_spi_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_dailymotion_ioc_DailymotionExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerApiServices();
        $this->_registerEmbedded();
        $this->_registerListeners();
        $this->_registerMediaProvider();
        $this->_registerPlayer();
        $this->_registerOptionTransformers();
        $this->_registerOptions();
    }

    private function _registerApiServices()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_dmapi_ApiUtility',
            'tubepress_dailymotion_impl_dmapi_ApiUtility'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_HttpClientInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_array_ArrayReaderInterface::_));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_dmapi_LanguageLocaleSupplier.languages',
            'tubepress_dailymotion_impl_dmapi_LanguageLocaleSupplier'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_dmapi_ApiUtility'))
            ->withArgument('https://api.dailymotion.com/languages')
            ->withArgument('code')
            ->withArgument(array('native_name', 'name'));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_dmapi_LanguageLocaleSupplier.locales',
            'tubepress_dailymotion_impl_dmapi_LanguageLocaleSupplier'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_dmapi_ApiUtility'))
            ->withArgument('https://api.dailymotion.com/locales')
            ->withArgument('locale')
            ->withArgument(array('locally_localized_language'));
    }

    private function _registerEmbedded()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_embedded_DailymotionEmbeddedProvider',
            'tubepress_dailymotion_impl_embedded_DailymotionEmbeddedProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withTag('tubepress_spi_embedded_EmbeddedProviderInterface')
            ->withTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_media_HttpItemListener',
            'tubepress_dailymotion_impl_listeners_media_HttpItemListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_media_AttributeFormatterInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_array_ArrayReaderInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::MEDIA_ITEM_HTTP_NEW . '.dailymotion',
                'method'   => 'onHttpItem',
                'priority' => 100000
            ));

        $fixedValues = array(
            tubepress_dailymotion_api_Constants::OPTION_PLAYER_QUALITY=> array(
                tubepress_dailymotion_api_Constants::PLAYER_QUALITY_AUTO => 'Auto',   //>(translatable)<
                tubepress_dailymotion_api_Constants::PLAYER_QUALITY_2160 => '2160p',
                tubepress_dailymotion_api_Constants::PLAYER_QUALITY_1440 => '1440p',
                tubepress_dailymotion_api_Constants::PLAYER_QUALITY_1080 => '1080p',
                tubepress_dailymotion_api_Constants::PLAYER_QUALITY_720  => '720p',
                tubepress_dailymotion_api_Constants::PLAYER_QUALITY_480  => '480p',
                tubepress_dailymotion_api_Constants::PLAYER_QUALITY_380  => '380p',
                tubepress_dailymotion_api_Constants::PLAYER_QUALITY_240  => '240p',
            ),
            tubepress_dailymotion_api_Constants::OPTION_PLAYER_THEME => array(
                tubepress_dailymotion_api_Constants::PLAYER_THEME_LIGHT => 'Light', //>(translatable)<
                tubepress_dailymotion_api_Constants::PLAYER_THEME_DARK  => 'Dark',  //>(translatable)<
            ),
            tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER => array(
                tubepress_dailymotion_api_Constants::FILTER_LIVE_ALL           => 'All videos',             //>(translatable)<
                tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_ONLY     => 'Live only',              //>(translatable)<
                tubepress_dailymotion_api_Constants::FILTER_LIVE_NON_LIVE      => 'Non-live only',          //>(translatable)<
                tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_ON       => 'On-air live only',       //>(translatable)<
                tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_OFF      => 'Off-air live only',      //>(translatable)<
                tubepress_dailymotion_api_Constants::FILTER_LIVE_LIVE_UPCOMING => 'Upcoming live only',     //>(translatable)<
            ),
            tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER => array(
                tubepress_dailymotion_api_Constants::FILTER_PREMIUM_ALL              => 'All videos',                //>(translatable)<
                tubepress_dailymotion_api_Constants::FILTER_PREMIUM_PREMIUM_ONLY     => 'Paid content only',         //>(translatable)<
                tubepress_dailymotion_api_Constants::FILTER_PREMIUM_NON_PREMIUM_ONLY => 'Free content only',         //>(translatable)<
            ),
            tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER => array(
                tubepress_dailymotion_api_Constants::FILTER_PARTNER_ALL          => 'All videos',                   //>(translatable)<
                tubepress_dailymotion_api_Constants::FILTER_PARTNER_PARTNER_ONLY => 'Partner videos only',          //>(translatable)<
                tubepress_dailymotion_api_Constants::FILTER_PARTNER_ALL          => 'User-generated videos only',   //>(translatable)<
            ),
            tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO => array(
                tubepress_dailymotion_api_Constants::THUMB_RATIO_ORIGINAL   => 'Original',   //>(translatable)<
                tubepress_dailymotion_api_Constants::THUMB_RATIO_WIDESCREEN => 'Widescreen', //>(translatable)<
                tubepress_dailymotion_api_Constants::THUMB_RATIO_SQUARE     => 'Square',     //>(translatable)<
            ),
            tubepress_dailymotion_api_Constants::OPTION_THUMB_SIZE => array(
                tubepress_dailymotion_api_Constants::THUMB_SIZE_MAX => 'Maximum',                  //>(translatable)<
                tubepress_dailymotion_api_Constants::THUMB_SIZE_720 => sprintf('%d pixels', 720),  //>(translatable)<
                tubepress_dailymotion_api_Constants::THUMB_SIZE_480 => sprintf('%d pixels', 480),  //>(translatable)<
                tubepress_dailymotion_api_Constants::THUMB_SIZE_360 => sprintf('%d pixels', 360),  //>(translatable)<
                tubepress_dailymotion_api_Constants::THUMB_SIZE_240 => sprintf('%d pixels', 240),  //>(translatable)<
                tubepress_dailymotion_api_Constants::THUMB_SIZE_180 => sprintf('%d pixels', 180),  //>(translatable)<
                tubepress_dailymotion_api_Constants::THUMB_SIZE_120 => sprintf('%d pixels', 120),  //>(translatable)<
                tubepress_dailymotion_api_Constants::THUMB_SIZE_60  => sprintf('%d pixels', 60),   //>(translatable)<
            )
        );

        foreach ($fixedValues as $optionName => $values) {

            $this->expectRegistration(
                "fixed_values.$optionName",
                'tubepress_api_options_listeners_FixedValuesListener'
            )->withArgument($values)
                ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
                    'priority' => 100000,
                    'method'   => 'onAcceptableValues'
                ));
        }

        $languageLocaleMap = array(
            tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED => 'languages',
            tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE            => 'locales',
        );

        foreach ($languageLocaleMap as $optionName => $suffix) {

            $this->expectRegistration(
                "fixed_values.$optionName",
                'tubepress_dailymotion_impl_listeners_options_LanguageLocaleListener'
            )->withArgument(new tubepress_api_ioc_Reference("tubepress_dailymotion_impl_dmapi_LanguageLocaleSupplier.$suffix"))
                ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
                    'priority' => 100000,
                    'method'   => 'onAcceptableValues'
                ));
        }

        $validators = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN,
                tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_STRING_HEXCOLOR => array(
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_COLOR,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_DOM_ELEMENT_ID_OR_NAME => array(
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_ID,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_DOMAIN => array(
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_ORIGIN_DOMAIN,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_TWO_DIGIT_COUNTRY_CODE => array(
                tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY,
            )
        );

        foreach ($validators as $type => $optionNames) {
            foreach ($optionNames as $optionName) {

                $this->expectRegistration(
                    "regex_validation.$optionName",
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                    ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                        'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                        'priority' => 100000,
                        'method'   => 'onOption',
                    ));
            }
        }

        $strlenValidators = array(
            tubepress_dailymotion_api_Constants::OPTION_SEARCH_VALUE,
            tubepress_dailymotion_api_Constants::OPTION_TAG_VALUE,
            tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH,
            tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS,
            tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG,
            tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE,
            tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE,
        );

        foreach ($strlenValidators as $optionName) {

            $this->expectRegistration(
                "strlen_validation.$optionName",
                'tubepress_api_options_listeners_PatternValidatingListener'
            )->withArgument('/^.{0,150}$/')
                ->withArgument('"%s" cannot be longer than 150 characters')
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                    'priority' => 100000,
                    'method'   => 'onOptionValidation',
                ));
        }

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_TransformListener__dmUser',
            'tubepress_dailymotion_impl_listeners_options_TransformListener'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_listeners_options_transform_UserTransformer'))
            ->withArgument('Invalid Dailymotion user ID')
            ->withArgument(false)
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_dailymotion_api_Constants::OPTION_FAVORITES_VALUE,
                'method'   => 'onOption',
                'priority' => 100000,
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_dailymotion_api_Constants::OPTION_FEATURED_VALUE,
                'method'   => 'onOption',
                'priority' => 100000,
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_dailymotion_api_Constants::OPTION_USER_VALUE,
                'method'   => 'onOption',
                'priority' => 100000,
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_dailymotion_api_Constants::OPTION_SUBSCRIPTIONS_VALUE,
                'method'   => 'onOption',
                'priority' => 100000,
            ));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_TransformListener__video',
            'tubepress_dailymotion_impl_listeners_options_TransformListener'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_listeners_options_transform_VideoIdTransformer'))
            ->withArgument('Invalid Dailymotion video ID')
            ->withArgument(false)
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_dailymotion_api_Constants::OPTION_RELATED_VALUE,
                'method'   => 'onOption',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_TransformListener__playlist',
            'tubepress_dailymotion_impl_listeners_options_TransformListener'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_listeners_options_transform_VideoIdTransformer'))
            ->withArgument('Invalid Dailymotion playlist ID')
            ->withArgument(false)
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_dailymotion_api_Constants::OPTION_PLAYLIST_VALUE,
                'method'   => 'onOption',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_TransformListener__videos',
            'tubepress_dailymotion_impl_listeners_options_TransformListener'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer__videos'))
            ->withArgument('Invalid Dailymotion video ID(s)')
            ->withArgument(false)
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_dailymotion_api_Constants::OPTION_LIST_VALUE,
                'method'   => 'onOption',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_TransformListener__languages',
            'tubepress_dailymotion_impl_listeners_options_TransformListener'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer__languages'))
            ->withArgument('')
            ->withArgument(true)
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED,
                'method'   => 'onOption',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_TransformListener__users',
            'tubepress_dailymotion_impl_listeners_options_TransformListener'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer__users'))
            ->withArgument('')
            ->withArgument(true)
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER,
                'method'   => 'onOption',
                'priority' => 100000
            ));
    }

    private function _registerMediaProvider()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_media_FeedHandler',
            'tubepress_dailymotion_impl_media_FeedHandler'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_array_ArrayReaderInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_dmapi_ApiUtility'));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_media_MediaProvider',
            'tubepress_dailymotion_impl_media_MediaProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_media_HttpCollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_media_FeedHandler'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_spi_media_MediaProviderInterface::__);
    }

    private function _registerOptionTransformers()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_transform_LanguageTransformer',
            'tubepress_dailymotion_impl_listeners_options_transform_LanguageTransformer'
        );

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_transform_PlaylistTransformer',
            'tubepress_dailymotion_impl_listeners_options_transform_PlaylistTransformer'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_transform_UserTransformer',
            'tubepress_dailymotion_impl_listeners_options_transform_UserTransformer'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_transform_VideoIdTransformer',
            'tubepress_dailymotion_impl_listeners_options_transform_VideoIdTransformer'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer__videos',
            'tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_listeners_options_transform_VideoIdTransformer'));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer__languages',
            'tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_listeners_options_transform_LanguageTransformer'));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer__users',
            'tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_listeners_options_transform_UserTransformer'));
    }

    private function _registerOptions()
    {
        $valueMap = array(

            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(

                /**
                 * Player options.
                 */
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_COLOR          => 'ffcc33',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_QUALITY        => tubepress_dailymotion_api_Constants::PLAYER_QUALITY_AUTO,
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_SHOW_CONTROLS  => true,
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_SHOW_ENDSCREEN => true,
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_SHOW_LOGO      => false,
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_SHOW_SHARING   => false,
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_THEME          => tubepress_dailymotion_api_Constants::PLAYER_THEME_DARK,
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_ORIGIN_DOMAIN  => null,
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_ID             => null,

                /**
                 * Source values.
                 */
                tubepress_dailymotion_api_Constants::OPTION_FAVORITES_VALUE     => null,
                tubepress_dailymotion_api_Constants::OPTION_FEATURED_VALUE      => null,
                tubepress_dailymotion_api_Constants::OPTION_LIST_VALUE          => null,
                tubepress_dailymotion_api_Constants::OPTION_PLAYLIST_VALUE      => null,
                tubepress_dailymotion_api_Constants::OPTION_RELATED_VALUE       => null,
                tubepress_dailymotion_api_Constants::OPTION_SEARCH_VALUE        => null,
                tubepress_dailymotion_api_Constants::OPTION_SUBSCRIPTIONS_VALUE => null,
                tubepress_dailymotion_api_Constants::OPTION_TAG_VALUE           => null,
                tubepress_dailymotion_api_Constants::OPTION_USER_VALUE          => null,

                /**
                 * Global params.
                 */
                tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER => true,
                tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE        => null,

                /**
                 * Feed options.
                 */
                tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => null,
                tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => null,
                tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => null,
                tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => false,
                tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => null,
                tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => null,
                tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => false,
                tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => tubepress_dailymotion_api_Constants::FILTER_LIVE_ALL,
                tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PREMIUM_ALL,
                tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => tubepress_dailymotion_api_Constants::FILTER_PARTNER_ALL,
                tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 0,
                tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 0,
                tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => null,
                tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => null,
                tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => null,
                tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => null,

                /**
                 * Thumbnail options.
                 */
                tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO => tubepress_dailymotion_api_Constants::THUMB_RATIO_ORIGINAL,
                tubepress_dailymotion_api_Constants::OPTION_THUMB_SIZE   => tubepress_dailymotion_api_Constants::THUMB_SIZE_240,
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(

                /**
                 * Player options.
                 */
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_COLOR          => 'Highlight color of controls',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_QUALITY        => 'Preferred playback quality',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_SHOW_CONTROLS  => 'Show player controls',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_SHOW_ENDSCREEN => 'Show end-screen',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_SHOW_LOGO      => 'Show Dailymotion logo',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_SHOW_SHARING   => 'Enable sharing controls',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_THEME          => 'Player theme',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_ORIGIN_DOMAIN  => 'Origin domain',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_ID             => 'Player identifier',

                /**
                 * Source values.
                 */
                tubepress_dailymotion_api_Constants::OPTION_FAVORITES_VALUE     => null,
                tubepress_dailymotion_api_Constants::OPTION_FEATURED_VALUE      => null,
                tubepress_dailymotion_api_Constants::OPTION_LIST_VALUE          => null,
                tubepress_dailymotion_api_Constants::OPTION_PLAYLIST_VALUE      => null,
                tubepress_dailymotion_api_Constants::OPTION_RELATED_VALUE       => null,
                tubepress_dailymotion_api_Constants::OPTION_SEARCH_VALUE        => null,
                tubepress_dailymotion_api_Constants::OPTION_SUBSCRIPTIONS_VALUE => null,
                tubepress_dailymotion_api_Constants::OPTION_TAG_VALUE           => null,
                tubepress_dailymotion_api_Constants::OPTION_USER_VALUE          => null,

                /**
                 * Global params.
                 */
                tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER => 'Filter out explicit videos',
                tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE        => 'Preferred localization',

                /**
                 * Feed options.
                 */
                tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'Limit to country',
                tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'Limit to detected language',
                tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'Limit to declared languages',
                tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => 'Featured videos only',
                tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Limit to genre',
                tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Exclude genre',
                tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => 'High-definition videos only',
                tubepress_dailymotion_api_Constants::OPTION_FEED_LIVE_FILTER        => 'Broadcast status',
                tubepress_dailymotion_api_Constants::OPTION_FEED_PREMIUM_FILTER     => 'Premium filter',
                tubepress_dailymotion_api_Constants::OPTION_FEED_PARTNER_FILTER     => 'Partner filter',
                tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 'Maximum duration in minutes',
                tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 'Minimum duration in minutes',
                tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'Limit to "strong" tags',
                tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'Limit to tags',
                tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'Limit to owners',
                tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'Limit to full-text search',

                /**
                 * Thumbnail options.
                 */
                tubepress_dailymotion_api_Constants::OPTION_THUMBS_RATIO => 'Preferred thumbnail shape',
                tubepress_dailymotion_api_Constants::OPTION_THUMB_SIZE   => 'Preferred thumbnail size'
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(

                /**
                 * Player options.
                 */
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_QUALITY        => 'Suggest a default playback quality. Set to "auto" for best results.',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_SHOW_ENDSCREEN => 'Show related videos after playback finishes.',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_ORIGIN_DOMAIN  => 'Most users should leave this blank. You may set a domain for the page hosting the video player, which may be useful in rare situations.',
                tubepress_dailymotion_api_Constants::OPTION_PLAYER_ID             => 'Most users should leave this blank. You may set a unique identifier for the player on the page, which may be useful for custom JavaScript programming.',

                /**
                 * Source values.
                 */
                tubepress_dailymotion_api_Constants::OPTION_FAVORITES_VALUE     => null,
                tubepress_dailymotion_api_Constants::OPTION_FEATURED_VALUE      => null,
                tubepress_dailymotion_api_Constants::OPTION_LIST_VALUE          => null,
                tubepress_dailymotion_api_Constants::OPTION_PLAYLIST_VALUE      => null,
                tubepress_dailymotion_api_Constants::OPTION_RELATED_VALUE       => null,
                tubepress_dailymotion_api_Constants::OPTION_SEARCH_VALUE        => null,
                tubepress_dailymotion_api_Constants::OPTION_SUBSCRIPTIONS_VALUE => null,
                tubepress_dailymotion_api_Constants::OPTION_TAG_VALUE           => null,
                tubepress_dailymotion_api_Constants::OPTION_USER_VALUE          => null,

                /**
                 * Global params.
                 */
                tubepress_dailymotion_api_Constants::OPTION_FEED_FAMILY_FILTER => 'Enable Dailymotion\'s "family filter" which attempts to exclude videos with adult content.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_LOCALE        => 'Enter a <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">two-digit language code</a> (e.g. <code>it</code>, <code>fr</code>, etc), optionally followed by an underscore and a <a href="https://en.wikipedia.org/wiki/ISO_3166-1" target="_blank">two-digit country code</a> (e.g. <code>fr_FR</code>, <code>en_US</code>, etc.) to narrow video results to a specified language or region.',

                /**
                 * Feed options.
                 */
                tubepress_dailymotion_api_Constants::OPTION_FEED_COUNTRY            => 'Enter a <a href="https://en.wikipedia.org/wiki/ISO_3166-1" target="_blank">two-digit country code</a> to only include videos declared to be from the specific country. e.g. <code>FR</code> for France or <code>IT</code> for Italy.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGE_DETECTED  => 'Dailymotion attempts to guess the language spoken in every video. Enter <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">two-digit language code</a> to include only videos with the detected language. i.e. <code>ja</code> for Japanese or <code>pl</code> for Polish.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_LANGUAGES_DECLARED => 'Enter a comma-separated list of <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">two-digit language codes</a> to include only videos declared (by video owner) to be in the specified languages. e.g. <code>ru, be, uk</code> for Russian, Belarusian, and Ukranian or <code>sv</code> for Swedish.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_FEATURED_ONLY      => 'Only include videos featured by Dailymotion.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_GENRE              => 'Only include videos with the specified genre.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_NO_GENRE           => 'Exclude videos with the specified genre.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_HD_ONLY            => 'Only include videos with a vertical resolution of 720p or higher.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_SHORTER_THAN       => 'Only include videos shorter than or equal to the given number of minutes.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_LONGER_THAN        => 'Only include videos longer than or equal to the given number of minutes.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS_STRONG        => 'Enter a comma-separated list of terms to only include videos tagged with those exact terms. e.g. <code>Weddings</code> or <code>Wedding Planning, Wedding Venue</code>.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_TAGS               => 'Enter a comma-separated list of terms to only include videos with tags that contain the given terms. e.g. <code>wedding</code> or <code>marriage, party, celebration</code>.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_OWNERS_FILTER      => 'Enter a comma-separated list of Dailymotion screennames to include videos only from those users.',
                tubepress_dailymotion_api_Constants::OPTION_FEED_SEARCH             => 'Only include videos that match the given search query.',

                /**
                 *
                 * Thumbnail options.
                 */
                tubepress_dailymotion_api_Constants::OPTION_THUMB_SIZE   => 'Choose the height of thumbnails that are served from Dailymotion. Higher resolutions will generally look better but will negatively impact your site\'s load time. For best results, choose the value closest to (but not less than) your actual thumbnail height (configured above).',
            ),
        );

        $this->expectRegistration(
            'tubepress_api_options_Reference__dailymotion',
            'tubepress_api_options_Reference'
        )->withArgument($valueMap)
            ->withArgument(array())
            ->withTag(tubepress_api_options_ReferenceInterface::_);
    }

    private function _registerPlayer()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_player_DailymotionPlayerLocation',
            'tubepress_dailymotion_impl_player_DailymotionPlayerLocation'
        )->withTag('tubepress_spi_player_PlayerLocationInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockLogger      = $this->mock(tubepress_api_log_LoggerInterface::_);
        $mockBaseUrl     = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockEnvironment = $this->mock(tubepress_api_environment_EnvironmentInterface::_);

        $mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $mockEnvironment->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('getClone')->once()->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('addPath')->once()->with('src/add-ons/provider-dailymotion/web/images/icons/dailymotion-icon-34w_x_34h.png')->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('mock-base-url');

        $mockUrlFactory = new tubepress_url_impl_puzzle_UrlFactory();

        return array(
            tubepress_api_options_ContextInterface::_          => tubepress_api_options_ContextInterface::_,
            tubepress_api_util_LangUtilsInterface::_           => tubepress_api_util_LangUtilsInterface::_,
            tubepress_api_url_UrlFactoryInterface::_           => $mockUrlFactory,
            tubepress_api_log_LoggerInterface::_               => $mockLogger,
            tubepress_api_http_HttpClientInterface::_          => tubepress_api_http_HttpClientInterface::_,
            tubepress_api_array_ArrayReaderInterface::_        => tubepress_api_array_ArrayReaderInterface::_,
            tubepress_api_media_HttpCollectorInterface::_      => tubepress_api_media_HttpCollectorInterface::_,
            tubepress_api_environment_EnvironmentInterface::_  => $mockEnvironment,
            tubepress_api_util_StringUtilsInterface::_         => tubepress_api_util_StringUtilsInterface::_,
            tubepress_api_media_AttributeFormatterInterface::_ => tubepress_api_media_AttributeFormatterInterface::_,
            tubepress_api_options_ReferenceInterface::_        => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_translation_TranslatorInterface::_   => tubepress_api_translation_TranslatorInterface::_,
        );
    }
}