<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_vimeo3_ioc_VimeoExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerEmbedded($containerBuilder);
        $this->_registerListeners($containerBuilder);
        $this->_registerMediaProvider($containerBuilder);
        $this->_registerOauthProvider($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
        $this->_registerPlayer($containerBuilder);
    }

    private function _registerEmbedded(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_vimeo3_impl_embedded_VimeoEmbeddedProvider',
            'tubepress_vimeo3_impl_embedded_VimeoEmbeddedProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addTag('tubepress_spi_embedded_EmbeddedProviderInterface')
         ->addTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_vimeo3_impl_listeners_media_HttpItemListener',
            'tubepress_vimeo3_impl_listeners_media_HttpItemListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_media_AttributeFormatterInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::MEDIA_ITEM_HTTP_NEW . '.vimeo_v3',
            'method'   => 'onHttpItem',
            'priority' => 100000,
        ));

        $containerBuilder->register(
            'tubepress_api_options_listeners_TrimmingListener.' . tubepress_vimeo3_api_Constants::OPTION_PLAYER_COLOR,
            'tubepress_api_options_listeners_TrimmingListener'
        )->addArgument('#')
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_vimeo3_api_Constants::OPTION_PLAYER_COLOR,
            'method'   => 'onOption',
            'priority' => 100000,
        ));

        $validators = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_STRING_HEXCOLOR => array(
                tubepress_vimeo3_api_Constants::OPTION_PLAYER_COLOR,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_ALBUM_VALUE,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_CHANNEL_VALUE,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_GROUP_VALUE,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_LIKES_VALUE,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE,
            ),
        );

        foreach ($validators as $type => $optionNames) {
            foreach ($optionNames as $optionName) {

                $containerBuilder->register(
                    "regex_validation.$optionName",
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_));
            }
        }

        $containerBuilder->register(
            'tubepress_vimeo3_impl_listeners_options_VimeoOptionsListener',
            'tubepress_vimeo3_impl_listeners_options_VimeoOptionsListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_vimeo3_api_Constants::OPTION_VIMEO_ALBUM_VALUE,
                'method'   => 'onAlbumValue',
                'priority' => 100000, ))
            ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_vimeo3_api_Constants::OPTION_VIMEO_GROUP_VALUE,
                'method'   => 'onGroupValue',
                'priority' => 100000, ))
            ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . '.' . tubepress_vimeo3_api_Constants::OPTION_VIMEO_CHANNEL_VALUE,
                'method'   => 'onChannelValue',
                'priority' => 100000, ));
    }

    private function _registerMediaProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_vimeo3_impl_media_FeedHandler',
            'tubepress_vimeo3_impl_media_FeedHandler'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_));

        $containerBuilder->register(
            'tubepress_vimeo3_impl_media_MediaProvider',
            'tubepress_vimeo3_impl_media_MediaProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_media_HttpCollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_vimeo3_impl_media_FeedHandler'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_spi_media_MediaProviderInterface::__);
    }

    private function _registerOauthProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_vimeo3_impl_oauth_VimeoOauth2Provider',
            'tubepress_vimeo3_impl_oauth_VimeoOauth2Provider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addTag(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
    }

    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldIndex = 0;

        $gallerySourceMap = array(

            array(tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_ALBUM_VALUE, ),

            array(tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_CHANNEL_VALUE, ),

            array(tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_SEARCH_VALUE, ),

            array(tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE, ),

            array(tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE, ),

            array(tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_LIKES_VALUE, ),

            array(tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_GROUP_VALUE, ),

            array(tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CATEGORY,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_CATEGORY_VALUE, ),

            array(tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_TAG,
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_TAG_VALUE, ),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $containerBuilder->register(

                'vimeo_options_subfield_' . $fieldIndex,
                'tubepress_api_options_ui_FieldInterface'
            )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[1])
             ->addArgument('multiSourceText');

            $containerBuilder->register(

                'vimeo_options_field_' . $fieldIndex,
                'tubepress_api_options_ui_FieldInterface'
            )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[0])
             ->addArgument('gallerySourceRadio')
             ->addArgument(array(
                'additionalField' => new tubepress_api_ioc_Reference('vimeo_options_subfield_' . $fieldIndex++),
             ));
        }

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo3_api_Constants::OPTION_PLAYER_COLOR)
         ->addArgument('spectrum');

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument('does-not-matter')
         ->addArgument('oauth2ClientInstructions')
         ->addArgument(array(
             'provider' => new tubepress_api_ioc_Reference('tubepress_vimeo3_impl_oauth_VimeoOauth2Provider'),
         ));

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument('does-not-matter')
         ->addArgument('oauth2ClientId')
         ->addArgument(array(
             'provider' => new tubepress_api_ioc_Reference('tubepress_vimeo3_impl_oauth_VimeoOauth2Provider'),
         ));

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument('does-not-matter')
         ->addArgument('oauth2ClientSecret')
         ->addArgument(array(
             'provider' => new tubepress_api_ioc_Reference('tubepress_vimeo3_impl_oauth_VimeoOauth2Provider'),
         ));

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
            ->setFactoryMethod('newInstance')
            ->addArgument('does-not-matter')
            ->addArgument('oauth2TokenManagement')
            ->addArgument(array(
                'provider' => new tubepress_api_ioc_Reference('tubepress_vimeo3_impl_oauth_VimeoOauth2Provider'),
            ));

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument('does-not-matter')
         ->addArgument('oauth2TokenSelection')
         ->addArgument(array(
            'provider' => new tubepress_api_ioc_Reference('tubepress_vimeo3_impl_oauth_VimeoOauth2Provider'),
        ));

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('vimeo_options_field_' . $x);
        }

        $containerBuilder->register(

            'tubepress_vimeo3_impl_options_ui_FieldProvider',
            'tubepress_vimeo3_impl_options_ui_FieldProvider'
        )->addArgument($fieldReferences)
         ->addArgument(array(

                tubepress_api_options_ui_CategoryNames::GALLERY_SOURCE => array(

                    tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
                    tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
                    tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
                    tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
                    tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
                    tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
                    tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
                    tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CATEGORY,
                    tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_TAG,
                ),

                tubepress_api_options_ui_CategoryNames::EMBEDDED => array(

                    tubepress_vimeo3_api_Constants::OPTION_PLAYER_COLOR,
                ),

                tubepress_api_options_ui_CategoryNames::FEED => array(

                    'clientInstructions_vimeoV3',
                    'clientId_vimeoV3',
                    'clientSecret_vimeoV3',
                    'tokenManagement_vimeoV3',
                    'tokenSelection_vimeoV3',
                ),
            ))
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__vimeo',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_vimeo3_api_Constants::OPTION_PLAYER_COLOR           => '999999',
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_ALBUM_VALUE      => '140484',
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE => 'royksopp',
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_GROUP_VALUE      => 'hdxs',
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_LIKES_VALUE      => 'coiffier',
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_SEARCH_VALUE     => 'glacier national park',
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE => 'AvantGardeDiaries',
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_CATEGORY_VALUE   => 'documentary',
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_TAG_VALUE        => 'weddings',
                tubepress_vimeo3_api_Constants::OPTION_LIKES                  => false,
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_vimeo3_api_Constants::OPTION_PLAYER_COLOR => 'Main color', //>(translatable)<

                tubepress_vimeo3_api_Constants::OPTION_VIMEO_ALBUM_VALUE      => 'Videos from this Vimeo album',       //>(translatable)<
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE => 'Videos this Vimeo user appears in',  //>(translatable)<
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_CHANNEL_VALUE    => 'Videos in this Vimeo channel',       //>(translatable)<
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_GROUP_VALUE      => 'Videos from this Vimeo group',       //>(translatable)<
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_LIKES_VALUE      => 'Videos this Vimeo user likes',       //>(translatable)<
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_SEARCH_VALUE     => 'Vimeo search for',                   //>(translatable)<
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE => 'Videos uploaded by this Vimeo user', //>(translatable)<
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_CATEGORY_VALUE   => 'Videos in this Vimeo category',      //>(translatable)<
                tubepress_vimeo3_api_Constants::OPTION_VIMEO_TAG_VALUE        => 'Videos tagged with',                 //>(translatable)<

                tubepress_vimeo3_api_Constants::OPTION_LIKES => 'Number of likes',  //>(translatable)<
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(

                tubepress_vimeo3_api_Constants::OPTION_PLAYER_COLOR => sprintf('Default is %s.', "999999"), //>(translatable)<
            ),
        ));
    }

    private function _registerPlayer(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_vimeo3_impl_player_VimeoPlayerLocation',
            'tubepress_vimeo3_impl_player_VimeoPlayerLocation'
        )->addTag('tubepress_spi_player_PlayerLocationInterface');
    }
}
