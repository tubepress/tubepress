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
 *
 */
class tubepress_player_ioc_PlayerExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
        $this->_registerListeners($containerBuilder);
        $this->_registerDefaultPlayers($containerBuilder);
        $this->_registerTemplatePaths($containerBuilder);
    }

    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_player_impl_listeners_PlayerAjaxListener',
            'tubepress_player_impl_listeners_PlayerAjaxListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_CollectorInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_ResponseCodeInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
             'event'     => tubepress_app_api_event_Events::HTTP_AJAX . '.playerHtml',
             'priority'  => 100000,
             'method'   => 'onAjax',
        ));

        $containerBuilder->register(
            'tubepress_player_impl_listeners_PlayerListener',
            'tubepress_player_impl_listeners_PlayerListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => 'tubepress_app_api_player_PlayerLocationInterface',
            'method' => 'setPlayerLocations'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::MEDIA_PAGE_NEW,
            'priority' => 92000,
            'method'   => 'onNewMediaPage'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::PLAYER_LOCATION,
            'priority' => 100000,
            'method'   => 'onAcceptableValues'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_SELECT . '.gallery/player/static',
            'priority' => 100000,
            'method'   => 'onStaticPlayerTemplateSelection'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_SELECT . '.gallery/player/ajax',
            'priority' => 100000,
            'method'   => 'onAjaxPlayerTemplateSelection'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
            'priority' => 94000,
            'method'   => 'onGalleryTemplatePreRender'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::GALLERY_INIT_JS,
            'priority' => 96000,
            'method'   => 'onGalleryInitJs'));

        $containerBuilder->register(
            'tubepress_player_impl_listeners_SoloPlayerListener',
            'tubepress_player_impl_listeners_SoloPlayerListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::HTML_GENERATION,
            'priority' => 98000,
            'method'   => 'onHtmlGeneration'
        ));
    }

    private function _registerDefaultPlayers(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_player_impl_JsPlayerLocation__jqmodal',
            'tubepress_player_impl_JsPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_JQMODAL)
         ->addArgument('with jqModal')                                          //>(translatable)<)
         ->addArgument('gallery/players/jqmodal/static')
         ->addArgument('gallery/players/jqmodal/ajax')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_JsPlayerLocation__normal',
            'tubepress_player_impl_JsPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_NORMAL)
         ->addArgument('normally (at the top of your gallery)')                 //>(translatable)<
         ->addArgument('gallery/players/normal/static')
         ->addArgument('gallery/players/normal/ajax')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_JsPlayerLocation__popup',
            'tubepress_player_impl_JsPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_POPUP)
         ->addArgument('in a popup window')                 //>(translatable)<
         ->addArgument('gallery/players/popup/static')
         ->addArgument('gallery/players/popup/ajax')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_JsPlayerLocation__shadowbox',
            'tubepress_player_impl_JsPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_SHADOWBOX)
         ->addArgument('with Shadowbox')                 //>(translatable)<
         ->addArgument('gallery/players/shadowbox/static')
         ->addArgument('gallery/players/shadowbox/ajax')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_SoloOrStaticPlayerLocation__solo',
            'tubepress_player_impl_SoloOrStaticPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_SOLO)
         ->addArgument('in a new window on its own')                 //>(translatable)<
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_SoloOrStaticPlayerLocation__static',
            'tubepress_player_impl_SoloOrStaticPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_STATIC)
         ->addArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
         ->addArgument('gallery/players/static/static')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');
    }

    private function _registerTemplatePaths(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_template_BasePathProvider__player',
            'tubepress_api_template_BasePathProvider'
        )->addArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/player/templates',
        ))->addTag('tubepress_lib_api_template_PathProviderInterface');
    }
}