<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_vimeo2_ioc_VimeoExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{
    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder A tubepress_platform_api_ioc_ContainerBuilderInterface instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerEmbedded($containerBuilder);
        $this->_registerListeners($containerBuilder);
        $this->_registerMediaProvider($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
        $this->_registerPlayer($containerBuilder);
    }

    private function _registerEmbedded(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_vimeo2_impl_embedded_VimeoEmbeddedProvider',
            'tubepress_vimeo2_impl_embedded_VimeoEmbeddedProvider'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
         ->addTag('tubepress_app_api_embedded_EmbeddedProviderInterface')
         ->addTag('tubepress_lib_api_template_PathProviderInterface');
    }

    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_vimeo2_impl_listeners_http_OauthListener',
            'tubepress_vimeo2_impl_listeners_http_OauthListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_oauth_v1_ClientInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_lib_api_http_Events::EVENT_HTTP_REQUEST,
            'method'   => 'onRequest',
            'priority' => 98000
        ));

        $containerBuilder->register(
            'tubepress_vimeo2_impl_listeners_media_HttpItemListener',
            'tubepress_vimeo2_impl_listeners_media_HttpItemListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_AttributeFormatterInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::MEDIA_ITEM_HTTP_NEW . '.vimeo_v2',
            'method'   => 'onHttpItem',
            'priority' => 100000
        ));

        $containerBuilder->register(
            'tubepress_app_api_listeners_options_TrimmingListener.' . tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR,
            'tubepress_app_api_listeners_options_TrimmingListener'
        )->addArgument('#')
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::OPTION_SET . '.' . tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR,
            'method'   => 'onOption',
            'priority' => 100000,
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

                $containerBuilder->register(
                    "regex_validation.$optionName",
                    'tubepress_app_api_listeners_options_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_));
            }
        }

        $containerBuilder->register(
            'tubepress_vimeo2_impl_listeners_options_VimeoOptionsListener',
            'tubepress_vimeo2_impl_listeners_options_VimeoOptionsListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
            ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::OPTION_SET . '.' . tubepress_vimeo2_api_Constants::OPTION_VIMEO_ALBUM_VALUE,
                'method'   => 'onAlbumValue',
                'priority' => 100000))
            ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'   => tubepress_app_api_event_Events::OPTION_SET . '.' . tubepress_vimeo2_api_Constants::OPTION_VIMEO_GROUP_VALUE,
                'method'  => 'onGroupValue',
                'priority' => 100000))
            ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'   => tubepress_app_api_event_Events::OPTION_SET . '.' . tubepress_vimeo2_api_Constants::OPTION_VIMEO_CHANNEL_VALUE,
                'method'  => 'onChannelValue',
                'priority' => 100000));
    }

    private function _registerMediaProvider(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_vimeo2_impl_media_FeedHandler',
            'tubepress_vimeo2_impl_media_FeedHandler'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_));


        $containerBuilder->register(
            'tubepress_vimeo2_impl_media_MediaProvider',
            'tubepress_vimeo2_impl_media_MediaProvider'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_HttpCollectorInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_vimeo2_impl_media_FeedHandler'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_app_api_media_MediaProviderInterface::__);
    }

    private function _registerOptionsUi(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldIndex = 0;
        $containerBuilder->register(
            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_app_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY)
         ->addArgument('text')
         ->addArgument(array('size' => 40));

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_app_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET)
         ->addArgument('text')
         ->addArgument(array('size' => 40));

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

            $containerBuilder->register(

                'vimeo_options_subfield_' . $fieldIndex,
                'tubepress_app_api_options_ui_FieldInterface'
            )->setFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[1])
             ->addArgument('multiSourceText');

            $containerBuilder->register(

                'vimeo_options_field_' . $fieldIndex,
                'tubepress_app_api_options_ui_FieldInterface'
            )->setFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[0])
             ->addArgument('gallerySourceRadio')
             ->addArgument(array(
                'additionalField' => new tubepress_platform_api_ioc_Reference('vimeo_options_subfield_' . $fieldIndex++)
             ));
        }

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_app_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR)
         ->addArgument('spectrum');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('vimeo_options_field_' . $x);
        }

        $containerBuilder->register(

            'tubepress_vimeo2_impl_options_ui_FieldProvider',
            'tubepress_vimeo2_impl_options_ui_FieldProvider'
        )->addArgument($fieldReferences)
         ->addArgument(array(

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
         ->addTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }

    private function _registerOptions(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_api_options_Reference__vimeo',
            'tubepress_app_api_options_Reference'
        )->addTag(tubepress_app_api_options_ReferenceInterface::_)
         ->addArgument(array(

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

    private function _registerPlayer(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_vimeo2_impl_player_VimeoPlayerLocation',
            'tubepress_vimeo2_impl_player_VimeoPlayerLocation'
        )->addTag('tubepress_app_api_player_PlayerLocationInterface');
    }
}