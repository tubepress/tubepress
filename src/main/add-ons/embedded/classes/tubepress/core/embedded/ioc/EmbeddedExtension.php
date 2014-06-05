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
 *
 */
class tubepress_core_embedded_ioc_EmbeddedExtension implements tubepress_api_ioc_ContainerExtensionInterface
{
    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 3.2.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_core_embedded_api_EmbeddedHtmlInterface::_,
            'tubepress_core_embedded_impl_EmbeddedHtml'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_media_provider_api_MediaProviderInterface::_,
            'method' => 'setMediaProviders'))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_embedded_api_EmbeddedProviderInterface::_,
            'method' => 'setEmbeddedProviders'));

        $containerBuilder->register(

            'tubepress_core_embedded_impl_listeners_template_EmbeddedCoreVariables',
            'tubepress_core_embedded_impl_listeners_template_EmbeddedCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_environment_api_EnvironmentInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_embedded_api_Constants::EVENT_TEMPLATE_EMBEDDED,
            'method'   => 'onEmbeddedTemplate',
            'priority' => 10100
        ));

        $containerBuilder->register(

            'tubepress_core_embedded_impl_listeners_options_AcceptableValues',
            'tubepress_core_embedded_impl_listeners_options_AcceptableValues'
        )->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_core_embedded_api_Constants::OPTION_PLAYER_IMPL,
            'method'   => 'onAcceptableValues',
            'priority' => 30000,
        ))->addTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_embedded_api_EmbeddedProviderInterface::_,
            'method' => 'setEmbeddedProviders'))
          ->addTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_media_provider_api_MediaProviderInterface::_,
            'method' => 'setMediaProviders'
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE, array(

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

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION, array(

            'priority' => 30000,
            'map'      => array(
                'positiveInteger' => array(
                    tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT,
                    tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH,
                )
            )
        ));
    }
}