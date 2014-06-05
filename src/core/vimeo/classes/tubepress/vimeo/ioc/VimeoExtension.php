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
class tubepress_vimeo_ioc_VimeoExtension implements tubepress_api_ioc_ContainerExtensionInterface
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

            'tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider',
            'tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addTag(tubepress_core_embedded_api_EmbeddedProviderInterface::_);

        $containerBuilder->register(

            'tubepress_vimeo_impl_listeners_http_VimeoOauthRequestListener',
            'tubepress_vimeo_impl_listeners_http_VimeoOauthRequestListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_oauth_v1_ClientInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(

            'event' => tubepress_core_http_api_Constants::EVENT_HTTP_REQUEST,
            'method' => 'onRequest',
            'priority' => 9000
        ));

        $containerBuilder->register(

            'tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
            'tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event' => tubepress_core_media_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
            'method' => 'onVideoConstruction',
            'priority' => 10000
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_TRIMMER . '_vimeo', array(
            'priority'    => 9500,
            'charlist'    => '#',
            'ltrim'       => true,
            'optionNames' => array(
                tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR
            )
        ));

        $containerBuilder->register(

            'tubepress_vimeo_impl_provider_VimeoVideoProvider',
            'tubepress_vimeo_impl_provider_VimeoVideoProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_media_provider_api_ItemSorterInterface::_))
         ->addTag('tubepress_core_media_provider_api_HttpProviderInterface');

        $fieldIndex = 0;
        $containerBuilder->register(
            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY)
         ->addArgument('text')
         ->addArgument(array('size' => 40));

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET)
         ->addArgument('text')
         ->addArgument(array('size' => 40));

        $gallerySourceMap = array(

            array(tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_ALBUM_VALUE),

            array(tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_CHANNEL_VALUE),

            array(tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_SEARCH_VALUE),

            array(tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE),

            array(tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE),

            array(tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_CREDITED,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_CREDITED_VALUE),

            array(tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_LIKES_VALUE),

            array(tubepress_vimeo_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_GROUP_VALUE),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $subFieldId = 'vimeo_options_field_' . $fieldIndex++;

            $containerBuilder->register(

                $subFieldId,
                'tubepress_core_options_ui_api_FieldInterface'
            )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[1])
             ->addArgument('text');

            $containerBuilder->register(

                'vimeo_options_field_' . $fieldIndex++,
                'tubepress_core_options_ui_api_FieldInterface'
            )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[0])
             ->addArgument('gallerySourceRadio')
             ->addArgument(array(
                'additionalField' => new tubepress_api_ioc_Reference($subFieldId)
             ));
        }

        $containerBuilder->register(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR)
         ->addArgument('spectrum');

        $fieldReferences = array();
        for ($x = 0; $x < count($fieldIndex); $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('vimeo_options_field_' . $x);
        }

        $categoryIndex = 0;
        $categories = array(
            tubepress_core_options_ui_api_Constants::CATEGORY_NAME_GALLERYSOURCE,
            tubepress_core_options_ui_api_Constants::CATEGORY_NAME_FEED,
            tubepress_core_options_ui_api_Constants::CATEGORY_NAME_PLAYER,
        );
        foreach ($categories as $categoryName) {

            $containerBuilder->register(
                'vimeo_category_' . $categoryIndex++,
                'tubepress_core_options_ui_api_ElementInterface'
            )->setFactoryService(tubepress_core_options_ui_api_ElementBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($categoryName)
             ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_));
        }
        $categoryReferences = array();
        for ($x = 0; $x , $x < count($categoryIndex); $x++) {
            $categoryReferences[] = new tubepress_api_ioc_Reference('vimeo_category_' . $x);
        }

        $containerBuilder->register(

            'tubepress_vimeo_impl_options_ui_VimeoFieldProvider',
            'tubepress_vimeo_impl_options_ui_VimeoFieldProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
         ->addArgument($fieldReferences)
         ->addArgument($categoryReferences)
         ->addTag('tubepress_core_options_ui_api_FieldProviderInterface');

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_vimeo', array(

            'defaultValues' => array(
                tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR           => '999999',
                tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY              => null,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET           => null,
                tubepress_vimeo_api_Constants::OPTION_VIMEO_ALBUM_VALUE      => '140484',
                tubepress_vimeo_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE => 'royksopp',
                tubepress_vimeo_api_Constants::OPTION_VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
                tubepress_vimeo_api_Constants::OPTION_VIMEO_CREDITED_VALUE   => 'patricklawler',
                tubepress_vimeo_api_Constants::OPTION_VIMEO_GROUP_VALUE      => 'hdxs',
                tubepress_vimeo_api_Constants::OPTION_VIMEO_LIKES_VALUE      => 'coiffier',
                tubepress_vimeo_api_Constants::OPTION_VIMEO_SEARCH_VALUE     => 'glacier national park',
                tubepress_vimeo_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE => 'AvantGardeDiaries',
                tubepress_vimeo_api_Constants::OPTION_LIKES                  => false,
            ),

            'descriptions' => array(
                tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR => sprintf('Default is %s', "999999"), //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY    => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
            ),

            'labels' => array(
                tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR => 'Main color', //>(translatable)<

                tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY    => 'Vimeo API "Consumer Key"',    //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET => 'Vimeo API "Consumer Secret"', //>(translatable)<

                tubepress_vimeo_api_Constants::OPTION_VIMEO_ALBUM_VALUE      => 'Videos from this Vimeo album',       //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE => 'Videos this Vimeo user appears in',  //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_CHANNEL_VALUE    => 'Videos in this Vimeo channel',       //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_CREDITED_VALUE   => 'Videos credited to this Vimeo user (either appears in or uploaded by)',  //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_GROUP_VALUE      => 'Videos from this Vimeo group',       //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_LIKES_VALUE      => 'Videos this Vimeo user likes',       //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_SEARCH_VALUE     => 'Vimeo search for',                   //>(translatable)<
                tubepress_vimeo_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE => 'Videos uploaded by this Vimeo user', //>(translatable)<

                tubepress_vimeo_api_Constants::OPTION_LIKES => 'Number of "likes"',  //>(translatable)<
            )
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_vimeo', array(

            'priority' => 4000,
            'map' => array(

                'hexColor' => array(
                    tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR
                ),
                'oneOrMoreWordChars' => array(
                    tubepress_vimeo_api_Constants::OPTION_VIMEO_ALBUM_VALUE,
                    tubepress_vimeo_api_Constants::OPTION_VIMEO_APPEARS_IN_VALUE,
                    tubepress_vimeo_api_Constants::OPTION_VIMEO_CHANNEL_VALUE,
                    tubepress_vimeo_api_Constants::OPTION_VIMEO_CREDITED_VALUE,
                    tubepress_vimeo_api_Constants::OPTION_VIMEO_GROUP_VALUE,
                    tubepress_vimeo_api_Constants::OPTION_VIMEO_LIKES_VALUE,
                    tubepress_vimeo_api_Constants::OPTION_VIMEO_UPLOADEDBY_VALUE,
                )
            )
        ));
    }
}