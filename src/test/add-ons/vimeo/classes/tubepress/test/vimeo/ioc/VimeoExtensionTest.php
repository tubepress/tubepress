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
 * @covers tubepress_vimeo_impl_ioc_VimeoExtension
 */
class tubepress_test_vimeo_impl_ioc_VimeoExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_vimeo_ioc_VimeoExtension();
    }


    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider',
            'tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withTag(tubepress_core_embedded_api_EmbeddedProviderInterface::_);

        $this->expectRegistration(

            'tubepress_vimeo_impl_listeners_http_VimeoOauthRequestListener',
            'tubepress_vimeo_impl_listeners_http_VimeoOauthRequestListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_oauth_v1_ClientInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(

                'event' => tubepress_core_http_api_Constants::EVENT_HTTP_REQUEST,
                'method' => 'onRequest',
                'priority' => 9000
            ));

        $this->expectRegistration(

            'tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
            'tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_util_api_TimeUtilsInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event' => tubepress_core_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
                'method' => 'onVideoConstruction',
                'priority' => 10000
            ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_TRIMMER, array(
            'priority'    => 9500,
            'charlist'    => '#',
            'ltrim'       => true,
            'optionNames' => array(
                tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR
            )
        ));

        $this->expectRegistration(

            'tubepress_vimeo_impl_provider_VimeoVideoProvider',
            'tubepress_vimeo_impl_provider_VimeoVideoProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_provider_api_ItemSorterInterface::_))
            ->withTag(tubepress_core_provider_api_HttpProviderInterface::_);

        $fieldIndex = 0;
        $this->expectRegistration(
            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_vimeo_api_Constants::OPTION_VIMEO_KEY)
            ->withArgument('text')
            ->withArgument(array('size' => 40));

        $this->expectRegistration(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_vimeo_api_Constants::OPTION_VIMEO_SECRET)
            ->withArgument('text')
            ->withArgument(array('size' => 40));

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

            $this->expectRegistration(

                $subFieldId,
                'tubepress_core_options_ui_api_FieldInterface'
            )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[1])
                ->withArgument('text');

            $this->expectRegistration(

                'vimeo_options_field_' . $fieldIndex++,
                'tubepress_core_options_ui_api_FieldInterface'
            )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[0])
                ->withArgument('gallerySourceRadio')
                ->withArgument(array(
                    'additionalField' => new tubepress_api_ioc_Reference($subFieldId)
                ));
        }

        $this->expectRegistration(

            'vimeo_options_field_' . $fieldIndex++,
            'tubepress_core_options_ui_api_FieldInterface'
        )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR)
            ->withArgument('spectrum');

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

            $this->expectRegistration(
                'vimeo_category_' . $categoryIndex++,
                'tubepress_core_options_ui_api_ElementInterface'
            )->withFactoryService(tubepress_core_options_ui_api_ElementBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($categoryName)
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_));
        }
        $categoryReferences = array();
        for ($x = 0; $x , $x < count($categoryIndex); $x++) {
            $categoryReferences[] = new tubepress_api_ioc_Reference('vimeo_category_' . $x);
        }

        $this->expectRegistration(

            'tubepress_vimeo_impl_options_ui_VimeoFieldProvider',
            'tubepress_vimeo_impl_options_ui_VimeoFieldProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument($fieldReferences)
            ->withArgument($categoryReferences)
            ->withTag('tubepress_core_options_ui_api_FieldProviderInterface');

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_vimeo', array(

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

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_vimeo', array(

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

    protected function getExpectedExternalServicesMap()
    {
        $mockField = $this->mock('tubepress_core_options_ui_api_FieldInterface');
        $mockfieldBuilder = $this->mock(tubepress_core_options_ui_api_FieldBuilderInterface::_);
        $mockfieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockElementBuilder = $this->mock(tubepress_core_options_ui_api_ElementBuilderInterface::_);
        $mockElement = $this->mock('tubepress_core_options_ui_api_ElementInterface');
        $mockElementBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockElement);

        return array(

            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_url_api_UrlFactoryInterface::_ => tubepress_core_url_api_UrlFactoryInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            tubepress_api_util_LangUtilsInterface::_ => tubepress_api_util_LangUtilsInterface::_,
            tubepress_core_http_api_oauth_v1_ClientInterface::_ => tubepress_core_http_api_oauth_v1_ClientInterface::_,
            tubepress_core_util_api_TimeUtilsInterface::_ => tubepress_core_util_api_TimeUtilsInterface::_,
            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_core_options_ui_api_FieldBuilderInterface::_ => $mockfieldBuilder,
            tubepress_core_translation_api_TranslatorInterface::_ => tubepress_core_translation_api_TranslatorInterface::_,
            tubepress_core_provider_api_ItemSorterInterface::_ => tubepress_core_provider_api_ItemSorterInterface::_,
            tubepress_core_options_ui_api_ElementBuilderInterface::_ => $mockElementBuilder,
        );
    }
}