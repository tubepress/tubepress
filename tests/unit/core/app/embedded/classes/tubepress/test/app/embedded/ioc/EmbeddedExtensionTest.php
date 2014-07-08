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
 * @covers tubepress_app_embedded_ioc_EmbeddedExtension
 */
class tubepress_test_app_embedded_ioc_EmbeddedExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_app_embedded_ioc_EmbeddedExtension
     */
    protected function buildSut()
    {
        return new tubepress_app_embedded_ioc_EmbeddedExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_app_embedded_api_EmbeddedHtmlInterface::_,
            'tubepress_app_embedded_impl_EmbeddedHtml'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
                'method' => 'setMediaProviders'))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_embedded_api_EmbeddedProviderInterface::_,
                'method' => 'setEmbeddedProviders'));

        $this->expectRegistration(
            'tubepress_app_embedded_impl_listeners_js_JsOptionsListener',
            'tubepress_app_embedded_impl_listeners_js_JsOptionsListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_GALLERY_INIT_JS,
                'method'   => 'onGalleryInitJs',
                'priority' => 9900,
            ));
        
        $this->expectRegistration(

            'tubepress_app_embedded_impl_listeners_template_Core',
            'tubepress_app_embedded_impl_listeners_template_Core'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_embedded_api_Constants::EVENT_TEMPLATE_EMBEDDED,
                'method'   => 'onEmbeddedTemplate',
                'priority' => 10100
            ));

        $this->expectRegistration(

            'tubepress_app_embedded_impl_listeners_options_AcceptableValues',
            'tubepress_app_embedded_impl_listeners_options_AcceptableValues'
        )->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL,
                'method'   => 'onAcceptableValues',
                'priority' => 30000,
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_embedded_api_EmbeddedProviderInterface::_,
                'method' => 'setEmbeddedProviders'))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
                'method' => 'setMediaProviders'
            ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_embedded', array(

            'defaultValues' => array(

                tubepress_app_embedded_api_Constants::OPTION_AUTOPLAY        => false,
                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT => 390,
                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH  => 640,
                tubepress_app_embedded_api_Constants::OPTION_LAZYPLAY        => true,
                tubepress_app_embedded_api_Constants::OPTION_LOOP            => false,
                tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL     => tubepress_app_embedded_api_Constants::EMBEDDED_IMPL_PROVIDER_BASED,
                tubepress_app_embedded_api_Constants::OPTION_SHOW_INFO       => false,
            ),

            'labels' => array(

                tubepress_app_embedded_api_Constants::OPTION_AUTOPLAY        => 'Auto-play all videos',                               //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT => 'Max height (px)',                                    //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH  => 'Max width (px)',                                     //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_LAZYPLAY        => '"Lazy" play videos',                                 //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_LOOP            => 'Loop',                                               //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL     => 'Implementation',                                     //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_SHOW_INFO       => 'Show title and rating before video starts',          //>(translatable)<

            ),

            'descriptions' => array(

                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT => sprintf('Default is %s.', 390), //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH  => sprintf('Default is %s.', 640), //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_LAZYPLAY        => 'Auto-play each video after thumbnail click.', //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_LOOP            => 'Continue playing the video until the user stops it.', //>(translatable)<
                tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL     => 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).', //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_embedded', array(

            'priority' => 30000,
            'map'      => array(
                'positiveInteger' => array(
                    tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT,
                    tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH,
                )
            )
        ));

        $fieldIndex = 0;
        $fieldsMap = array(
            'dropdown' => array(
                tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION,
                tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL
            ),
            'text' => array(
                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT,
                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH
            ),
            'boolean' => array(

                tubepress_app_embedded_api_Constants::OPTION_LAZYPLAY,
                tubepress_app_embedded_api_Constants::OPTION_SHOW_INFO,
                tubepress_app_embedded_api_Constants::OPTION_AUTOPLAY,
                tubepress_app_embedded_api_Constants::OPTION_LOOP,
            )
        );
        foreach ($fieldsMap as $type => $fieldIds) {
            foreach ($fieldIds as $fieldId) {
                $this->expectRegistration(
                    'embedded_field_' . $fieldIndex++,
                    'tubepress_app_options_ui_api_FieldInterface'
                )->withFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($fieldId)
                    ->withArgument($type);
            }
        }
        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('embedded_field_' . $x);
        }

        $this->expectRegistration(
            'player_category',
            'tubepress_app_options_ui_api_ElementInterface'
        )->withFactoryService(tubepress_app_options_ui_api_ElementBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_app_embedded_api_Constants::OPTIONS_UI_CATEGORY_EMBEDDED)
            ->withArgument('Player');

        $fieldMap = array(
            tubepress_app_embedded_api_Constants::OPTIONS_UI_CATEGORY_EMBEDDED => array(
                tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION,
                tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL,
                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT,
                tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH,
                tubepress_app_embedded_api_Constants::OPTION_LAZYPLAY,
                tubepress_app_embedded_api_Constants::OPTION_SHOW_INFO,
                tubepress_app_embedded_api_Constants::OPTION_AUTOPLAY,
                tubepress_app_embedded_api_Constants::OPTION_LOOP,
            )
        );

        $this->expectRegistration(

            'tubepress_app_embedded_impl_options_ui_FieldProvider',
            'tubepress_app_embedded_impl_options_ui_FieldProvider'
        )->withArgument(array(new tubepress_platform_api_ioc_Reference('player_category')))
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->once()->andReturn(true);

        $mockFieldBuilder = $this->mock(tubepress_app_options_ui_api_FieldBuilderInterface::_);
        $mockField        = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $mockFieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $element = $this->mock('tubepress_app_options_ui_api_ElementInterface');
        $elementBuilder = $this->mock(tubepress_app_options_ui_api_ElementBuilderInterface::_);
        $elementBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($element);

        return array(

            tubepress_platform_api_log_LoggerInterface::_                    => $logger,
            tubepress_app_options_api_ContextInterface::_          => tubepress_app_options_api_ContextInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_    => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_lib_url_api_UrlFactoryInterface::_           => tubepress_lib_url_api_UrlFactoryInterface::_,
            tubepress_lib_template_api_TemplateFactoryInterface::_ => tubepress_lib_template_api_TemplateFactoryInterface::_,
            tubepress_app_environment_api_EnvironmentInterface::_ => tubepress_app_environment_api_EnvironmentInterface::_,
            tubepress_app_options_ui_api_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_app_options_ui_api_ElementBuilderInterface::_ => $elementBuilder
        );
    }
}