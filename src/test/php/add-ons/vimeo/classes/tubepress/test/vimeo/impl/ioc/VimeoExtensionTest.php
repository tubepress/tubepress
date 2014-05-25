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
class tubepress_test_vimeo_impl_ioc_VimeoExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_vimeo_impl_ioc_VimeoExtension();
    }


    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider',
            'tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withTag(tubepress_core_api_embedded_EmbeddedProviderInterface::_);

        $this->expectRegistration(

            'tubepress_vimeo_impl_listeners_http_VimeoOauthRequestListener',
            'tubepress_vimeo_impl_listeners_http_VimeoOauthRequestListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_oauth_v1_ClientInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(

                'event' => tubepress_core_api_const_event_EventNames::HTTP_REQUEST,
                'method' => 'onRequest',
                'priority' => 9000
            ));

        $this->expectRegistration(

            'tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
            'tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
        ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_util_TimeUtilsInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event' => tubepress_core_api_const_event_EventNames::VIDEO_CONSTRUCTION,
                'method' => 'onVideoConstruction',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'vimeo_color_sanitizer',
            'stdclass'

        )->withTag(tubepress_core_api_const_ioc_Tags::LTRIM_SUBJECT_LISTENER, array(
                'event' => tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_vimeo_api_const_options_Names::PLAYER_COLOR,
                'charlist'   => '#',
                'priority' => 9500
            ));

        $this->expectRegistration(

            'tubepress_vimeo_impl_options_VimeoOptionProvider',
            'tubepress_vimeo_impl_options_VimeoOptionProvider'
        )->withTag(tubepress_core_api_options_EasyProviderInterface::_);

        $this->expectRegistration(

            'tubepress_vimeo_impl_provider_VimeoVideoProvider',
            'tubepress_vimeo_impl_provider_VimeoVideoProvider'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_core_api_provider_EasyHttpProviderInterface::_);

        $fields = array();

        $fields[] = $this->expectRegistration(

            'vimeo_options_field_' . tubepress_vimeo_api_const_options_Names::VIMEO_KEY,
            'tubepress_core_api_options_ui_FieldInterface'
        )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_vimeo_api_const_options_Names::VIMEO_KEY)
            ->withArgument('text')
            ->withArgument(array('size' => 40));

        $fields[] = $this->expectRegistration(

            'vimeo_options_field_' . tubepress_vimeo_api_const_options_Names::VIMEO_SECRET,
            'tubepress_core_api_options_ui_FieldInterface'
        )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_vimeo_api_const_options_Names::VIMEO_SECRET)
            ->withArgument('text')
            ->withArgument(array('size' => 40));

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

            $this->expectRegistration(

                $subFieldId,
                'tubepress_core_api_options_ui_FieldInterface'
            )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[1])
                ->withArgument('text');

            $this->expectRegistration(

                'vimeo_options_field_' . $gallerySourceFieldArray[0],
                'tubepress_core_api_options_ui_FieldInterface'
            )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
                ->withFactoryMethod('newInstance')
                ->withArgument($gallerySourceFieldArray[0])
                ->withArgument('gallerySourceRadio')
                ->withArgument(array(
                    'additionalField' => new tubepress_api_ioc_Reference($subFieldId)
                ));
        }

        $this->expectRegistration(

            'vimeo_options_field_' . tubepress_vimeo_api_const_options_Names::PLAYER_COLOR,
            'tubepress_core_api_options_ui_FieldInterface'
        )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_vimeo_api_const_options_Names::PLAYER_COLOR)
            ->withArgument('spectrum');
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            'tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider' => 'tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockField = $this->mock('tubepress_core_api_options_ui_FieldInterface');
        $mockfieldBuilder = $this->mock(tubepress_core_api_options_ui_FieldBuilderInterface::_);
        $mockfieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(

            tubepress_core_api_options_ContextInterface::_ => tubepress_core_api_options_ContextInterface::_,
            tubepress_core_api_url_UrlFactoryInterface::_ => tubepress_core_api_url_UrlFactoryInterface::_,
            tubepress_core_api_template_TemplateFactoryInterface::_ => tubepress_core_api_template_TemplateFactoryInterface::_,
            tubepress_api_util_LangUtilsInterface::_ => tubepress_api_util_LangUtilsInterface::_,
            tubepress_core_api_http_oauth_v1_ClientInterface::_ => tubepress_core_api_http_oauth_v1_ClientInterface::_,
            tubepress_core_api_util_TimeUtilsInterface::_ => tubepress_core_api_util_TimeUtilsInterface::_,
            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_core_api_event_EventDispatcherInterface::_ => tubepress_core_api_event_EventDispatcherInterface::_,
            tubepress_core_api_options_ui_FieldBuilderInterface::_ => $mockfieldBuilder
        );
    }
}