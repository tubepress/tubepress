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
 * @covers tubepress_core_embedded_ioc_EmbeddedExtension
 */
class tubepress_test_core_embedded_ioc_EmbeddedExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_core_embedded_ioc_EmbeddedExtension
     */
    protected function buildSut()
    {
        return new tubepress_core_embedded_ioc_EmbeddedExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_core_embedded_api_EmbeddedHtmlInterface::_,
            'tubepress_core_embedded_impl_EmbeddedHtml'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_core_media_provider_api_MediaProviderInterface::_,
                'method' => 'setMediaProviders'))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_core_embedded_api_EmbeddedProviderInterface::_,
                'method' => 'setEmbeddedProviders'));

        $this->expectRegistration(

            'tubepress_core_embedded_impl_listeners_template_EmbeddedCoreVariables',
            'tubepress_core_embedded_impl_listeners_template_EmbeddedCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_environment_api_EnvironmentInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_embedded_api_Constants::EVENT_TEMPLATE_EMBEDDED,
                'method'   => 'onEmbeddedTemplate',
                'priority' => 10100
            ));

        $this->expectRegistration(

            'tubepress_core_embedded_impl_listeners_options_AcceptableValues',
            'tubepress_core_embedded_impl_listeners_options_AcceptableValues'
        )->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_core_embedded_api_Constants::OPTION_PLAYER_IMPL,
                'method'   => 'onAcceptableValues',
                'priority' => 30000,
            ))->withTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_core_embedded_api_EmbeddedProviderInterface::_,
                'method' => 'setEmbeddedProviders'))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_core_media_provider_api_MediaProviderInterface::_,
                'method' => 'setMediaProviders'
            ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE, array(

            'defaultValues' => array(

                tubepress_core_embedded_api_Constants::OPTION_AUTOPLAY        => false,
                tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT => 390,
                tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH  => 640,
                tubepress_core_embedded_api_Constants::OPTION_LAZYPLAY        => true,
                tubepress_core_embedded_api_Constants::OPTION_LOOP            => false,
                tubepress_core_embedded_api_Constants::OPTION_PLAYER_IMPL     => tubepress_core_embedded_api_Constants::EMBEDDED_IMPL_PROVIDER_BASED,
                tubepress_core_embedded_api_Constants::OPTION_SHOW_INFO       => false,
            ),

            'labels' => array(

                tubepress_core_embedded_api_Constants::OPTION_AUTOPLAY        => 'Auto-play all videos',                               //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT => 'Max height (px)',                                    //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH  => 'Max width (px)',                                     //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_LAZYPLAY        => '"Lazy" play videos',                                 //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_LOOP            => 'Loop',                                               //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_PLAYER_IMPL     => 'Implementation',
                tubepress_core_embedded_api_Constants::OPTION_SHOW_INFO       => 'Show title and rating before video starts',          //>(translatable)<

            ),

            'descriptions' => array(

                tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT => sprintf('Default is %s.', 390), //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH  => sprintf('Default is %s.', 640), //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_LAZYPLAY        => 'Auto-play each video after thumbnail click.', //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_LOOP            => 'Continue playing the video until the user stops it.', //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_PLAYER_IMPL     => 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).', //>(translatable)<
                tubepress_core_embedded_api_Constants::OPTION_SHOW_INFO       => false,
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION, array(

            'priority' => 30000,
            'map'      => array(
                'positiveInteger' => array(
                    tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT,
                    tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH,
                )
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->once()->andReturn(true);
        return array(

            tubepress_api_log_LoggerInterface::_                    => $logger,
            tubepress_core_options_api_ContextInterface::_          => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_    => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_core_url_api_UrlFactoryInterface::_           => tubepress_core_url_api_UrlFactoryInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            tubepress_core_environment_api_EnvironmentInterface::_ => tubepress_core_environment_api_EnvironmentInterface::_,
        );
    }
}