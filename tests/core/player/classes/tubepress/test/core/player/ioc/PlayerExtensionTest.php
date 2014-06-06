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
 * @covers tubepress_core_player_ioc_PlayerExtension
 */
class tubepress_test_core_player_ioc_PlayerExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_player_ioc_PlayerExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_player_ioc_PlayerExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_core_player_impl_listeners_html_SoloPlayerListener',
            'tubepress_core_player_impl_listeners_html_SoloPlayerListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_core_html_single_impl_listeners_html_SingleVideoListener'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
                'method'   => 'onHtmlGeneration',
                'priority' => 7000
            ));

        $this->expectRegistration(
            'tubepress_core_player_impl_listeners_options_AcceptableValues',
            'tubepress_core_player_impl_listeners_options_AcceptableValues'
        )->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION,
                'method'   => 'onPlayerLocation',
                'priority' => 30000,
            ))->withTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_core_player_api_PlayerLocationInterface::_,
                'method' => 'setPlayerLocations'
            ));

        $this->expectRegistration(
            'tubepress_core_player_impl_listeners_template_PlayerLocationCoreVariables',
            'tubepress_core_player_impl_listeners_template_PlayerLocationCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_embedded_api_EmbeddedHtmlInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_TEMPLATE,
                'method'   => 'onPlayerTemplate',
                'priority' => 10000
            ));

        $this->expectRegistration(
            tubepress_core_player_api_PlayerHtmlInterface::_,
            'tubepress_core_player_impl_PlayerHtml'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));

        $this->expectRegistration(

            'tubepress_core_player_impl_BasePlayerLocation__normal',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->withArgument('normal')
            ->withArgument('normally (at the top of your gallery)')                 //>(translatable)<)
            ->withArgument(array('players/normal.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/normal.tpl.php'))
            ->withArgument('src/core/player/web/players/normal/normal.js')
            ->withArgument(true)
            ->withTag(tubepress_core_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'tubepress_core_player_impl_BasePlayerLocation__shadowbox',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->withArgument('shadowbox')
            ->withArgument('with Shadowbox')                 //>(translatable)<)
            ->withArgument(array('players/shadowbox.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/shadowbox.tpl.php'))
            ->withArgument('src/core/player/web/players/shadowbox/shadowbox.js')
            ->withArgument(true)
            ->withTag(tubepress_core_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9900
            ));

        $this->expectRegistration(

            'tubepress_core_player_impl_BasePlayerLocation__popup',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->withArgument('popup')
            ->withArgument('in a popup window')                 //>(translatable)<)
            ->withArgument(array('players/popup.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/popup.tpl.php'))
            ->withArgument('src/core/player/web/players/popup/popup.js')
            ->withArgument(true)
            ->withTag(tubepress_core_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9800
            ));

        $this->expectRegistration(

            'tubepress_core_player_impl_BasePlayerLocation__solo',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->withArgument('solo')
            ->withArgument('in a new window on its own')                 //>(translatable)<)
            ->withArgument(array())
            ->withArgument('src/core/player/web/players/solo/solo.js')
            ->withArgument(false)
            ->withTag(tubepress_core_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9700
            ));

        $this->expectRegistration(

            'tubepress_core_player_impl_BasePlayerLocation__static',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->withArgument('static')
            ->withArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<)
            ->withArgument(array('players/static.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/static.tpl.php'))
            ->withArgument('src/core/player/web/players/static/static.js')
            ->withArgument(true)
            ->withTag(tubepress_core_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9600
            ));

        $this->expectRegistration(

            'tubepress_core_player_impl_BasePlayerLocation__jqmodal',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->withArgument('jqmodal')
            ->withArgument('with jqModal')                 //>(translatable)<)
            ->withArgument(array('players/jqmodal.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/jqmodal.tpl.php'))
            ->withArgument('src/core/player/web/players/jqmodal/jqmodal.js')
            ->withArgument(true)
            ->withTag(tubepress_core_player_api_PlayerLocationInterface::_)
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9500
            ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_player', array(

            'defaultValues' => array(
                tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION => 'normal',
            ),

            'labels' => array(
                tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION => 'Play each video',      //>(translatable)<
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            tubepress_core_environment_api_EnvironmentInterface::_ => tubepress_core_environment_api_EnvironmentInterface::_,
            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            'tubepress_core_html_single_impl_listeners_html_SingleVideoListener' => 'tubepress_core_html_single_impl_listeners_html_SingleVideoListener',
            tubepress_core_http_api_RequestParametersInterface::_ => tubepress_core_http_api_RequestParametersInterface::_,
            tubepress_core_embedded_api_EmbeddedHtmlInterface::_ => tubepress_core_embedded_api_EmbeddedHtmlInterface::_
        );
    }
}
