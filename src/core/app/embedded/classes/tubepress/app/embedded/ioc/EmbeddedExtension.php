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
class tubepress_app_embedded_ioc_EmbeddedExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{
    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_platform_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_platform_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_app_embedded_api_EmbeddedHtmlInterface::_,
            'tubepress_app_embedded_impl_EmbeddedHtml'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
            'method' => 'setMediaProviders'))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_embedded_api_EmbeddedProviderInterface::_,
            'method' => 'setEmbeddedProviders'));

        $containerBuilder->register(
            'tubepress_app_embedded_impl_listeners_js_JsOptionsListener',
            'tubepress_app_embedded_impl_listeners_js_JsOptionsListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_embedded_api_EmbeddedHtmlInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_GALLERY_INIT_JS,
            'method'   => 'onGalleryInitJs',
            'priority' => 9900,
         ));

        $containerBuilder->register(

            'tubepress_app_embedded_impl_listeners_template_Core',
            'tubepress_app_embedded_impl_listeners_template_Core'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_embedded_api_Constants::EVENT_TEMPLATE_EMBEDDED,
            'method'   => 'onEmbeddedTemplate',
            'priority' => 10100
        ));

        $containerBuilder->register(

            'tubepress_app_embedded_impl_listeners_options_AcceptableValues',
            'tubepress_app_embedded_impl_listeners_options_AcceptableValues'
        )->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL,
            'method'   => 'onAcceptableValues',
            'priority' => 30000,
        ))->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_embedded_api_EmbeddedProviderInterface::_,
            'method' => 'setEmbeddedProviders'))
          ->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
            'method' => 'setMediaProviders'
        ));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_embedded', array(

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

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_embedded', array(

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
                $containerBuilder->register(
                    'embedded_field_' . $fieldIndex++,
                    'tubepress_app_options_ui_api_FieldInterface'
                )->setFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
                 ->setFactoryMethod('newInstance')
                 ->addArgument($fieldId)
                 ->addArgument($type);
            }
        }
        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('embedded_field_' . $x);
        }

        $containerBuilder->register(
            'player_category',
            'tubepress_app_options_ui_api_ElementInterface'
        )->setFactoryService(tubepress_app_options_ui_api_ElementBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_app_embedded_api_Constants::OPTIONS_UI_CATEGORY_EMBEDDED)
         ->addArgument('Player');   //>(translatable)<

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

        $containerBuilder->register(
            'tubepress_app_embedded_impl_options_ui_FieldProvider',
            'tubepress_app_embedded_impl_options_ui_FieldProvider'
        )->addArgument(array(new tubepress_platform_api_ioc_Reference('player_category')))
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }
}