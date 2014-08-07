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
 * @covers tubepress_vimeo2_ioc_VimeoExtension
 */
class tubepress_test_vimeo2_ioc_VimeoExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{

    /**
     * @return tubepress_platform_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_vimeo2_ioc_VimeoExtension();
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
            'tubepress_vimeo2_impl_embedded_VimeoEmbeddedProvider',
            'tubepress_vimeo2_impl_embedded_VimeoEmbeddedProvider'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->withTag('tubepress_app_api_embedded_EmbeddedProviderInterface')
            ->withTag('tubepress_lib_api_template_PathProviderInterface');
    }

    private function _expectListeners()
    {
        $this->expectRegistration(
            'tubepress_vimeo2_impl_listeners_http_OauthListener',
            'tubepress_vimeo2_impl_listeners_http_OauthListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_oauth_v1_ClientInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_lib_api_http_Events::EVENT_HTTP_REQUEST,
                'method'   => 'onRequest',
                'priority' => 9000
            ));

        $this->expectRegistration(
            'tubepress_vimeo2_impl_listeners_media_HttpItemListener',
            'tubepress_vimeo2_impl_listeners_media_HttpItemListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_AttributeFormatterInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::MEDIA_ITEM_HTTP_NEW,
                'method'   => 'onHttpItem',
                'priority' => 40000
            ));

        $this->expectRegistration(
            'tubepress_app_api_listeners_options_TrimmingListener.' . tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR,
            'tubepress_app_api_listeners_options_TrimmingListener'
        )->withArgument('#')
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::OPTION_SET . '.' . tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR,
                'method'   => 'onOption',
                'priority' => 9500,
            ));

        $validators = array(
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_HEXCOLOR => array(
                tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_ALBUM_VALUE,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_CHANNEL_VALUE,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_CREDITED_VALUE,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_GROUP_VALUE,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_LIKES_VALUE,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE,
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
            'tubepress_vimeo2_impl_media_FeedHandler',
            'tubepress_vimeo2_impl_media_FeedHandler'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_));


        $this->expectRegistration(
            'tubepress_vimeo2_impl_media_MediaProvider',
            'tubepress_vimeo2_impl_media_MediaProvider'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_HttpCollectorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('tubepress_vimeo2_impl_media_FeedHandler'))
            ->withTag(tubepress_app_api_media_MediaProviderInterface::__);
    }

    private function _expectOptionsUi()
    {
        $fieldIndex = 0;
        $this->expectRegistration(
            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_app_api_options_ui_FieldInterface'
        )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY)
            ->withArgument('text')
            ->withArgument(array('size' => 40));

        $this->expectRegistration(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_app_api_options_ui_FieldInterface'
        )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET)
            ->withArgument('text')
            ->withArgument(array('size' => 40));

        $gallerySourceMap = array(

            array(tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_ALBUM_VALUE),

            array(tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_CHANNEL_VALUE),

            array(tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_SEARCH_VALUE),

            array(tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE),

            array(tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE),

            array(tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CREDITED,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_CREDITED_VALUE),

            array(tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_LIKES_VALUE),

            array(tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
                tubepress_vimeo2_api_Constants::OPTION_VIMEO_GROUP_VALUE),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $subFieldId = 'vimeo_options_field_' . $fieldIndex++;

            $this->expectRegistration(

                $subFieldId,
                'tubepress_app_api_options_ui_FieldInterface'
            )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[1])
                ->withArgument('text');

            $this->expectRegistration(

                'vimeo_options_field_' . $fieldIndex++,
                'tubepress_app_api_options_ui_FieldInterface'
            )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[0])
                ->withArgument('gallerySourceRadio')
                ->withArgument(array(
                    'additionalField' => new tubepress_platform_api_ioc_Reference($subFieldId)
                ));
        }

        $this->expectRegistration(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_app_api_options_ui_FieldInterface'
        )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR)
            ->withArgument('spectrum');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('vimeo_options_field_' . $x);
        }

        $this->expectRegistration(

            'tubepress_vimeo2_impl_options_ui_FieldProvider',
            'tubepress_vimeo2_impl_options_ui_FieldProvider'
        )->withArgument($fieldReferences)
            ->withArgument(array(

                tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE => array(

                    tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
                    tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
                    tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
                    tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
                    tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
                    tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CREDITED,
                    tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
                    tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
                ),

                tubepress_app_api_options_ui_CategoryNames::EMBEDDED => array(

                    tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR,
                ),

                tubepress_app_api_options_ui_CategoryNames::FEED => array(

                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY,
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET,
                ),
            ))
            ->withTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }

    private function _expectOptions()
    {
        $this->expectRegistration(
            'tubepress_app_api_options_Reference__vimeo',
            'tubepress_app_api_options_Reference'
        )->withTag(tubepress_app_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR           => '999999',
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY              => null,
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET           => null,
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_ALBUM_VALUE      => '140484',
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE => 'royksopp',
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_CREDITED_VALUE   => 'patricklawler',
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_GROUP_VALUE      => 'hdxs',
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_LIKES_VALUE      => 'coiffier',
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_SEARCH_VALUE     => 'glacier national park',
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE => 'AvantGardeDiaries',
                    tubepress_vimeo2_api_Constants::OPTION_LIKES                  => false,
                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR => 'Main color', //>(translatable)<

                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY    => 'Vimeo API "Consumer Key"',    //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => 'Vimeo API "Consumer Secret"', //>(translatable)<

                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_ALBUM_VALUE      => 'Videos from this Vimeo album',       //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE => 'Videos this Vimeo user appears in',  //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_CHANNEL_VALUE    => 'Videos in this Vimeo channel',       //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_CREDITED_VALUE   => 'Videos credited to this Vimeo user (either appears in or uploaded by)',  //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_GROUP_VALUE      => 'Videos from this Vimeo group',       //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_LIKES_VALUE      => 'Videos this Vimeo user likes',       //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_SEARCH_VALUE     => 'Vimeo search for',                   //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE => 'Videos uploaded by this Vimeo user', //>(translatable)<

                    tubepress_vimeo2_api_Constants::OPTION_LIKES => 'Number of "likes"',  //>(translatable)<
                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(

                    tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR => sprintf('Default is %s', "999999"), //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY    => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
                    tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
                ),
            ));
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockField = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $mockfieldBuilder = $this->mock(tubepress_app_api_options_ui_FieldBuilderInterface::_);
        $mockfieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(

            tubepress_app_api_options_ContextInterface::_ => tubepress_app_api_options_ContextInterface::_,
            tubepress_platform_api_url_UrlFactoryInterface::_ => tubepress_platform_api_url_UrlFactoryInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_ => tubepress_lib_api_template_TemplatingInterface::_,
            tubepress_platform_api_util_LangUtilsInterface::_ => tubepress_platform_api_util_LangUtilsInterface::_,
            tubepress_lib_api_http_oauth_v1_ClientInterface::_ => tubepress_lib_api_http_oauth_v1_ClientInterface::_,
            tubepress_lib_api_util_TimeUtilsInterface::_ => tubepress_lib_api_util_TimeUtilsInterface::_,
            tubepress_platform_api_log_LoggerInterface::_ => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_lib_api_event_EventDispatcherInterface::_ => tubepress_lib_api_event_EventDispatcherInterface::_,
            tubepress_app_api_options_ui_FieldBuilderInterface::_ => $mockfieldBuilder,
            tubepress_lib_api_translation_TranslatorInterface::_ => tubepress_lib_api_translation_TranslatorInterface::_,
            tubepress_app_api_options_ReferenceInterface::_ => tubepress_app_api_options_ReferenceInterface::_,
            tubepress_app_api_media_HttpCollectorInterface::_ => tubepress_app_api_media_HttpCollectorInterface::_,
            tubepress_app_api_media_AttributeFormatterInterface::_ => tubepress_app_api_media_AttributeFormatterInterface::_
        );
    }
}