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
 * @covers tubepress_app_player_ioc_PlayerExtension
 */
class tubepress_test_app_player_ioc_PlayerExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_app_player_ioc_PlayerExtension
     */
    protected function buildSut()
    {
        return  new tubepress_app_player_ioc_PlayerExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_app_player_impl_http_PlayerAjaxCommand',
            'tubepress_app_player_impl_http_PlayerAjaxCommand'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_player_api_PlayerHtmlInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_media_provider_api_CollectorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_http_api_ResponseCodeInterface::_))
            ->withTag(tubepress_app_http_api_AjaxCommandInterface::_);

        $this->expectRegistration(
            'tubepress_app_player_impl_listeners_html_SoloPlayerListener',
            'tubepress_app_player_impl_listeners_html_SoloPlayerListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_html_api_Constants::EVENT_PRIMARY_HTML,
                'method'   => 'onHtmlGeneration',
                'priority' => 9000
            ));

        $this->expectRegistration(
            'tubepress_app_player_impl_listeners_js_GalleryJsListener',
            'tubepress_app_player_impl_listeners_js_GalleryJsListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_player_api_PlayerHtmlInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_GALLERY_INIT_JS,
                'method'   => 'onGalleryInitJs',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_app_player_impl_listeners_options_AcceptableValues',
            'tubepress_app_player_impl_listeners_options_AcceptableValues'
        )->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION,
                'method'   => 'onPlayerLocation',
                'priority' => 30000,
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_player_api_PlayerLocationInterface::_,
                'method' => 'setPlayerLocations'
            ));

        $this->expectRegistration(
            'tubepress_app_player_impl_listeners_template_GalleryTemplateListener',
            'tubepress_app_player_impl_listeners_template_GalleryTemplateListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_player_api_PlayerHtmlInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY,
                'method'   => 'onGalleryTemplate',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_app_player_impl_listeners_template_PlayerTemplateListener',
            'tubepress_app_player_impl_listeners_template_PlayerTemplateListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_embedded_api_EmbeddedHtmlInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_TEMPLATE,
                'method'   => 'onPlayerTemplate',
                'priority' => 10000
            ));

        $this->expectRegistration(
            tubepress_app_player_api_PlayerHtmlInterface::_,
            'tubepress_app_player_impl_PlayerHtml'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag' => tubepress_app_player_api_PlayerLocationInterface::_,
                'method' => 'setPlayerLocations'
            ));

        $this->expectRegistration(

            'tubepress_app_player_impl_BasePlayerLocation__normal',
            'tubepress_app_player_impl_BasePlayerLocation'
        )->withArgument('normal')
            ->withArgument('normally (at the top of your gallery)')                 //>(translatable)<)
            ->withArgument(array('players/normal/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/normal/static.tpl.php'))
            ->withArgument(array('players/normal/ajax.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/normal/ajax.tpl.php'))
            ->withTag(tubepress_app_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'tubepress_app_player_impl_BasePlayerLocation__shadowbox',
            'tubepress_app_player_impl_BasePlayerLocation'
        )->withArgument('shadowbox')
            ->withArgument('with Shadowbox')                 //>(translatable)<)
            ->withArgument(array('players/shadowbox/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/shadowbox/static.tpl.php'))
            ->withArgument(array('players/shadowbox/ajax.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/shadowbox/ajax.tpl.php'))
            ->withTag(tubepress_app_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9900
            ));

        $this->expectRegistration(

            'tubepress_app_player_impl_BasePlayerLocation__popup',
            'tubepress_app_player_impl_BasePlayerLocation'
        )->withArgument('popup')
            ->withArgument('in a popup window')                 //>(translatable)<)
            ->withArgument(array('players/popup/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/popup/static.tpl.php'))
            ->withArgument(array('players/popup/ajax.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/popup/ajax.tpl.php'))
            ->withTag(tubepress_app_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9800
            ));

        $this->expectRegistration(

            'tubepress_app_player_impl_SamePagePlayerLocation',
            'tubepress_app_player_impl_SamePagePlayerLocation'
        )->withArgument('solo')
            ->withArgument('in a new window on its own')                 //>(translatable)<))
            ->withArgument(array())
            ->withArgument(array())
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_util_api_UrlUtilsInterface::_))
            ->withTag(tubepress_app_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9700
            ));

        $this->expectRegistration(

            'tubepress_app_player_impl_SamePagePlayerLocation',
            'tubepress_app_player_impl_SamePagePlayerLocation'
        )->withArgument('static')
            ->withArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<)
            ->withArgument(array('players/static/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/static/static.tpl.php'))
            ->withArgument(array())
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_util_api_UrlUtilsInterface::_))
            ->withTag(tubepress_app_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9600
            ));

        $this->expectRegistration(

            'tubepress_app_player_impl_BasePlayerLocation__jqmodal',
            'tubepress_app_player_impl_BasePlayerLocation'
        )->withArgument('jqmodal')
            ->withArgument('with jqModal')                 //>(translatable)<)
            ->withArgument(array('players/jqmodal/static.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/jqmodal/static.tpl.php'))
            ->withArgument(array('players/jqmodal/ajax.tpl.php', TUBEPRESS_ROOT . '/src/core/app/player/resources/templates/players/jqmodal/ajax.tpl.php'))
            ->withTag(tubepress_app_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9500
            ));

        $this->expectRegistration(

            'tubepress_app_player_impl_listeners_media_item_InvokingAnchorListener',
            'tubepress_app_player_impl_listeners_media_item_InvokingAnchorListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_player_api_PlayerHtmlInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event' => tubepress_app_media_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
                'method' => 'onNewMediaItem',
                'priority' => 10000
            ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_player', array(

            'defaultValues' => array(
                tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION => 'normal',
            ),

            'labels' => array(
                tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION => 'Play each video',      //>(translatable)<
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_app_options_api_ContextInterface::_ => tubepress_app_options_api_ContextInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_ => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_lib_template_api_TemplateFactoryInterface::_ => tubepress_lib_template_api_TemplateFactoryInterface::_,
            tubepress_app_environment_api_EnvironmentInterface::_ => tubepress_app_environment_api_EnvironmentInterface::_,
            tubepress_platform_api_log_LoggerInterface::_ => tubepress_platform_api_log_LoggerInterface::_,
            'tubepress_app_feature_single_impl_listeners_html_SingleVideoListener' => 'tubepress_app_feature_single_impl_listeners_html_SingleVideoListener',
            tubepress_app_http_api_RequestParametersInterface::_ => tubepress_app_http_api_RequestParametersInterface::_,
            tubepress_app_embedded_api_EmbeddedHtmlInterface::_ => tubepress_app_embedded_api_EmbeddedHtmlInterface::_,
            tubepress_app_media_provider_api_CollectorInterface::_ => tubepress_app_media_provider_api_CollectorInterface::_,
            tubepress_lib_http_api_ResponseCodeInterface::_ => tubepress_lib_http_api_ResponseCodeInterface::_,
            tubepress_lib_url_api_UrlFactoryInterface::_ => tubepress_lib_url_api_UrlFactoryInterface::_,
            tubepress_lib_util_api_UrlUtilsInterface::_ => tubepress_lib_util_api_UrlUtilsInterface::_
        );
    }
}
