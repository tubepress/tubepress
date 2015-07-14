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
 * @covers tubepress_player_ioc_PlayerExtension
 */
class tubepress_test_player_ioc_PlayerExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_player_ioc_PlayerExtension
     */
    protected function buildSut()
    {
        return  new tubepress_player_ioc_PlayerExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerDefaultPlayers();
        $this->_registerTemplatePaths();
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_player_impl_listeners_PlayerAjaxListener',
            'tubepress_player_impl_listeners_PlayerAjaxListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_CollectorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_ResponseCodeInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'     => tubepress_app_api_event_Events::HTTP_AJAX . '.playerHtml',
                'priority'  => 100000,
                'method'   => 'onAjax',
            ));

        $this->expectRegistration(
            'tubepress_player_impl_listeners_PlayerListener',
            'tubepress_player_impl_listeners_PlayerListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => 'tubepress_app_api_player_PlayerLocationInterface',
                'method' => 'setPlayerLocations'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::MEDIA_PAGE_NEW,
                'priority' => 92000,
                'method'   => 'onNewMediaPage'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::PLAYER_LOCATION,
                'priority' => 100000,
                'method'   => 'onAcceptableValues'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_SELECT . '.gallery/player/static',
                'priority' => 100000,
                'method'   => 'onStaticPlayerTemplateSelection'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_SELECT . '.gallery/player/ajax',
                'priority' => 100000,
                'method'   => 'onAjaxPlayerTemplateSelection'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
                'priority' => 94000,
                'method'   => 'onGalleryTemplatePreRender'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::GALLERY_INIT_JS,
                'priority' => 96000,
                'method'   => 'onGalleryInitJs'));

        $this->expectRegistration(
            'tubepress_player_impl_listeners_SoloPlayerListener',
            'tubepress_player_impl_listeners_SoloPlayerListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::HTML_GENERATION,
                'priority' => 98000,
                'method'   => 'onHtmlGeneration'
            ));
    }

    private function _registerDefaultPlayers()
    {
        $this->expectRegistration(
            'tubepress_player_impl_JsPlayerLocation__jqmodal',
            'tubepress_player_impl_JsPlayerLocation'
        )->withArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_JQMODAL)
            ->withArgument('with jqModal')                                          //>(translatable)<)
            ->withArgument('gallery/players/jqmodal/static')
            ->withArgument('gallery/players/jqmodal/ajax')
            ->withTag('tubepress_app_api_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_JsPlayerLocation__normal',
            'tubepress_player_impl_JsPlayerLocation'
        )->withArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_NORMAL)
            ->withArgument('normally (at the top of your gallery)')                 //>(translatable)<
            ->withArgument('gallery/players/normal/static')
            ->withArgument('gallery/players/normal/ajax')
            ->withTag('tubepress_app_api_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_JsPlayerLocation__popup',
            'tubepress_player_impl_JsPlayerLocation'
        )->withArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_POPUP)
            ->withArgument('in a popup window')                 //>(translatable)<
            ->withArgument('gallery/players/popup/static')
            ->withArgument('gallery/players/popup/ajax')
            ->withTag('tubepress_app_api_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_JsPlayerLocation__shadowbox',
            'tubepress_player_impl_JsPlayerLocation'
        )->withArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_SHADOWBOX)
            ->withArgument('with Shadowbox')                 //>(translatable)<
            ->withArgument('gallery/players/shadowbox/static')
            ->withArgument('gallery/players/shadowbox/ajax')
            ->withTag('tubepress_app_api_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_SoloOrStaticPlayerLocation__solo',
            'tubepress_player_impl_SoloOrStaticPlayerLocation'
        )->withArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_SOLO)
            ->withArgument('in a new window on its own')                 //>(translatable)<
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->withTag('tubepress_app_api_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_SoloOrStaticPlayerLocation__static',
            'tubepress_player_impl_SoloOrStaticPlayerLocation'
        )->withArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_STATIC)
            ->withArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->withArgument('gallery/players/static/static')
            ->withTag('tubepress_app_api_player_PlayerLocationInterface');
    }

    private function _registerTemplatePaths()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__player',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/player/templates',
        ))->withTag('tubepress_lib_api_template_PathProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockCurrentUrl = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $mockCurrentUrl->shouldReceive('removeSchemeAndAuthority');

        $mockUrlFactory = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $mockUrlFactory->shouldReceive('fromCurrent')->atLeast(1)->andReturn($mockCurrentUrl);

        return array(
            tubepress_platform_api_log_LoggerInterface::_        => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_app_api_options_ContextInterface::_        => tubepress_app_api_options_ContextInterface::_,
            tubepress_app_api_media_CollectorInterface::_        => tubepress_app_api_media_CollectorInterface::_,
            tubepress_lib_api_http_RequestParametersInterface::_ => tubepress_lib_api_http_RequestParametersInterface::_,
            tubepress_lib_api_http_ResponseCodeInterface::_      => tubepress_lib_api_http_ResponseCodeInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_    => tubepress_lib_api_template_TemplatingInterface::_,
            tubepress_platform_api_url_UrlFactoryInterface::_    => $mockUrlFactory,
        );
    }
}
