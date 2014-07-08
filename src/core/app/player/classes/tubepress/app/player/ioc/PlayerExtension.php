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
class tubepress_app_player_ioc_PlayerExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
            'tubepress_app_player_impl_http_PlayerAjaxCommand',
            'tubepress_app_player_impl_http_PlayerAjaxCommand'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_player_api_PlayerHtmlInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_media_provider_api_CollectorInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_http_api_ResponseCodeInterface::_))
         ->addTag(tubepress_app_http_api_AjaxCommandInterface::_);

        $containerBuilder->register(
            'tubepress_app_player_impl_listeners_html_SoloPlayerListener',
            'tubepress_app_player_impl_listeners_html_SoloPlayerListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_html_api_Constants::EVENT_PRIMARY_HTML,
            'method'   => 'onHtmlGeneration',
            'priority' => 9000
        ));

        $containerBuilder->register(
            'tubepress_app_player_impl_listeners_js_GalleryJsListener',
            'tubepress_app_player_impl_listeners_js_GalleryJsListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_player_api_PlayerHtmlInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_GALLERY_INIT_JS,
            'method'   => 'onGalleryInitJs',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_app_player_impl_listeners_options_AcceptableValues',
            'tubepress_app_player_impl_listeners_options_AcceptableValues'
        )->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION,
            'method'   => 'onPlayerLocation',
            'priority' => 30000,
        ))->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_player_api_PlayerLocationInterface::_,
            'method' => 'setPlayerLocations'
        ));

        $containerBuilder->register(
            'tubepress_app_player_impl_listeners_template_GalleryTemplateListener',
            'tubepress_app_player_impl_listeners_template_GalleryTemplateListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_player_api_PlayerHtmlInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY,
            'method'   => 'onGalleryTemplate',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_app_player_impl_listeners_template_PlayerTemplateListener',
            'tubepress_app_player_impl_listeners_template_PlayerTemplateListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_embedded_api_EmbeddedHtmlInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_TEMPLATE,
            'method'   => 'onPlayerTemplate',
            'priority' => 10000
        ));

        $containerBuilder->register(
            tubepress_app_player_api_PlayerHtmlInterface::_,
            'tubepress_app_player_impl_PlayerHtml'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag' => tubepress_app_player_api_PlayerLocationInterface::_,
            'method' => 'setPlayerLocations'
        ));

        $containerBuilder->register(

            'tubepress_app_player_impl_BasePlayerLocation__normal',
            'tubepress_app_player_impl_BasePlayerLocation'
        )->addArgument('normal')
         ->addArgument('normally (at the top of your gallery)')                 //>(translatable)<)
         ->addArgument(array('players/normal/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/normal/static.tpl.php'))
         ->addArgument(array('players/normal/ajax.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/normal/ajax.tpl.php'))
         ->addTag(tubepress_app_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
             'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
             'method'   => 'onSelectPlayerLocation',
             'priority' => 10000
         ));

        $containerBuilder->register(

            'tubepress_app_player_impl_BasePlayerLocation__shadowbox',
            'tubepress_app_player_impl_BasePlayerLocation'
        )->addArgument('shadowbox')
         ->addArgument('with Shadowbox')                 //>(translatable)<)
         ->addArgument(array('players/shadowbox/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/shadowbox/static.tpl.php'))
         ->addArgument(array('players/shadowbox/ajax.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/shadowbox/ajax.tpl.php'))
         ->addTag(tubepress_app_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9900
        ));

        $containerBuilder->register(

            'tubepress_app_player_impl_BasePlayerLocation__popup',
            'tubepress_app_player_impl_BasePlayerLocation'
        )->addArgument('popup')
         ->addArgument('in a popup window')                 //>(translatable)<)
         ->addArgument(array('players/popup/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/popup/static.tpl.php'))
         ->addArgument(array('players/popup/ajax.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/popup/ajax.tpl.php'))
         ->addTag(tubepress_app_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9800
         ));

        $containerBuilder->register(

            'tubepress_app_player_impl_SamePagePlayerLocation',
            'tubepress_app_player_impl_SamePagePlayerLocation'
        )->addArgument('solo')
         ->addArgument('in a new window on its own')                 //>(translatable)<))
         ->addArgument(array())
         ->addArgument(array())
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_util_api_UrlUtilsInterface::_))
         ->addTag(tubepress_app_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9700
         ));

        $containerBuilder->register(

            'tubepress_app_player_impl_SamePagePlayerLocation',
            'tubepress_app_player_impl_SamePagePlayerLocation'
        )->addArgument('static')
         ->addArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<)
         ->addArgument(array('players/static/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/static/static.tpl.php'))
         ->addArgument(array())
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_util_api_UrlUtilsInterface::_))
         ->addTag(tubepress_app_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9600
         ));

        $containerBuilder->register(

            'tubepress_app_player_impl_BasePlayerLocation__jqmodal',
            'tubepress_app_player_impl_BasePlayerLocation'
        )->addArgument('jqmodal')
         ->addArgument('with jqModal')                 //>(translatable)<)
         ->addArgument(array('players/jqmodal/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/jqmodal/static.tpl.php'))
         ->addArgument(array('players/jqmodal/ajax.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/jqmodal/ajax.tpl.php'))
         ->addTag(tubepress_app_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9500
         ));

        $containerBuilder->register(

            'tubepress_app_player_impl_listeners_media_item_InvokingAnchorListener',
            'tubepress_app_player_impl_listeners_media_item_InvokingAnchorListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_player_api_PlayerHtmlInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event' => tubepress_app_media_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
            'method' => 'onNewMediaItem',
            'priority' => 10000
        ));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_player', array(

            'defaultValues' => array(
                tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION => 'normal',
            ),

            'labels' => array(
                tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION => 'Play each video',      //>(translatable)<
            )
        ));
    }
}