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
class tubepress_vimeo_impl_ioc_VimeoExtension implements tubepress_api_ioc_ContainerExtensionInterface
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

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addTag(tubepress_core_api_embedded_EmbeddedProviderInterface::_);

        $containerBuilder->register(

            'tubepress_vimeo_impl_listeners_http_VimeoOauthRequestListener',
            'tubepress_vimeo_impl_listeners_http_VimeoOauthRequestListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_oauth_v1_ClientInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(

            'event' => tubepress_core_api_const_event_EventNames::HTTP_REQUEST,
            'method' => 'onRequest',
            'priority' => 9000
        ));

        $containerBuilder->register(

            'tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
            'tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_util_TimeUtilsInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_CONSTRUCTION,
            'method' => 'onVideoConstruction',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'vimeo_color_sanitizer',
            'stdclass'

        )->addTag(tubepress_core_api_const_ioc_Tags::LTRIM_SUBJECT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_vimeo_api_const_options_Names::PLAYER_COLOR,
            'charlist'   => '#',
            'priority' => 9500
        ));

        $containerBuilder->register(

            'tubepress_vimeo_impl_options_VimeoOptionProvider',
            'tubepress_vimeo_impl_options_VimeoOptionProvider'
        )->addTag(tubepress_core_api_options_EasyProviderInterface::_);

        $containerBuilder->register(

            'tubepress_vimeo_impl_provider_VimeoVideoProvider',
            'tubepress_vimeo_impl_provider_VimeoVideoProvider'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_core_api_provider_EasyHttpProviderInterface::_);

        $fields = array();

        $fields[] = $containerBuilder->register(

            'vimeo_options_field_' . tubepress_vimeo_api_const_options_Names::VIMEO_KEY,
            'tubepress_core_api_options_ui_FieldInterface'
        )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo_api_const_options_Names::VIMEO_KEY)
         ->addArgument('text')
         ->addArgument(array('size' => 40));

        $fields[] = $containerBuilder->register(

            'vimeo_options_field_' . tubepress_vimeo_api_const_options_Names::VIMEO_SECRET,
            'tubepress_core_api_options_ui_FieldInterface'
        )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo_api_const_options_Names::VIMEO_SECRET)
         ->addArgument('text')
         ->addArgument(array('size' => 40));

        $gallerySourceMap = array(

            array(
                tubepress_vimeo_api_const_options_Values::VIMEO_ALBUM,
                tubepress_vimeo_api_const_options_Names::VIMEO_ALBUM_VALUE),

            array(tubepress_vimeo_api_const_options_Values::VIMEO_CHANNEL,
                tubepress_vimeo_api_const_options_Names::VIMEO_CHANNEL_VALUE),

            array(tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH,
                tubepress_vimeo_api_const_options_Names::VIMEO_SEARCH_VALUE),

            array(tubepress_vimeo_api_const_options_Values::VIMEO_UPLOADEDBY,
                tubepress_vimeo_api_const_options_Names::VIMEO_UPLOADEDBY_VALUE),

            array(tubepress_vimeo_api_const_options_Values::VIMEO_APPEARS_IN,
                tubepress_vimeo_api_const_options_Names::VIMEO_APPEARS_IN_VALUE),

            array(tubepress_vimeo_api_const_options_Values::VIMEO_CREDITED,
                tubepress_vimeo_api_const_options_Names::VIMEO_CREDITED_VALUE),

            array(tubepress_vimeo_api_const_options_Values::VIMEO_LIKES,
                tubepress_vimeo_api_const_options_Names::VIMEO_LIKES_VALUE),

            array(tubepress_vimeo_api_const_options_Values::VIMEO_GROUP,
                tubepress_vimeo_api_const_options_Names::VIMEO_GROUP_VALUE),
        );

        foreach ($gallerySourceMap as $gallerySourceFieldArray) {

            $subFieldId = 'vimeo_options_subfield' . $gallerySourceFieldArray[1];

            $containerBuilder->register(

                $subFieldId,
                'tubepress_core_api_options_ui_FieldInterface'
            )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[1])
             ->addArgument('text');

            $containerBuilder->register(

                'vimeo_options_field_' . $gallerySourceFieldArray[0],
                'tubepress_core_api_options_ui_FieldInterface'
            )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
             ->setFactoryMethod('newInstance')
             ->addArgument($gallerySourceFieldArray[0])
             ->addArgument('gallerySourceRadio')
             ->addArgument(array(
                'additionalField' => new tubepress_api_ioc_Reference($subFieldId)
             ));
        }

        $containerBuilder->register(

            'vimeo_options_field_' . tubepress_vimeo_api_const_options_Names::PLAYER_COLOR,
            'tubepress_core_api_options_ui_FieldInterface'
        )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_vimeo_api_const_options_Names::PLAYER_COLOR)
         ->addArgument('spectrum');
    }
}