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
 * @covers tubepress_core_impl_player_PlayerExtension
 */
class tubepress_test_core_impl_player_PlayerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_impl_player_PlayerExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_impl_player_PlayerExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_core_api_player_PlayerHtmlInterface::_,
            'tubepress_core_impl_player_PlayerHtml'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_));

        $this->expectRegistration(

            'tubepress_core_impl_player_locations_BasePlayerLocation__normal',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->withArgument('normal')
            ->withArgument('normally (at the top of your gallery)')                 //>(translatable)<)
            ->withArgument(array('players/normal.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/normal.tpl.php'))
            ->withArgument('src/main/web/players/normal/normal.js')
            ->withArgument(true)
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'tubepress_core_impl_player_locations_BasePlayerLocation__shadowbox',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->withArgument('shadowbox')
            ->withArgument('with Shadowbox')                 //>(translatable)<)
            ->withArgument(array('players/shadowbox.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/shadowbox.tpl.php'))
            ->withArgument('src/main/web/players/shadowbox/shadowbox.js')
            ->withArgument(true)
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9900
            ));

        $this->expectRegistration(

            'tubepress_core_impl_player_locations_BasePlayerLocation__popup',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->withArgument('popup')
            ->withArgument('in a popup window')                 //>(translatable)<)
            ->withArgument(array('players/popup.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/popup.tpl.php'))
            ->withArgument('src/main/web/players/popup/popup.js')
            ->withArgument(true)
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9800
            ));

        $this->expectRegistration(

            'tubepress_core_impl_player_locations_BasePlayerLocation__solo',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->withArgument('solo')
            ->withArgument('in a new window on its own')                 //>(translatable)<)
            ->withArgument(array())
            ->withArgument('src/main/web/players/solo/solo.js')
            ->withArgument(false)
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9700
            ));

        $this->expectRegistration(

            'tubepress_core_impl_player_locations_BasePlayerLocation__static',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->withArgument('static')
            ->withArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<)
            ->withArgument(array('players/static.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/static.tpl.php'))
            ->withArgument('src/main/web/players/static/static.js')
            ->withArgument(true)
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9600
            ));

        $this->expectRegistration(

            'tubepress_core_impl_player_locations_BasePlayerLocation__jqmodal',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->withArgument('jqmodal')
            ->withArgument('with jqModal')                 //>(translatable)<)
            ->withArgument(array('players/jqmodal.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/jqmodal.tpl.php'))
            ->withArgument('src/main/web/players/jqmodal/jqmodal.js')
            ->withArgument(true)
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
                'method'   => 'onSelectPlayerLocation',
                'priority' => 9500
            ));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_api_player_PlayerHtmlInterface::_ => 'tubepress_core_impl_player_PlayerHtml',
            'tubepress_core_impl_player_locations_BasePlayerLocation__normal' => 'tubepress_core_impl_player_locations_BasePlayerLocation',
            'tubepress_core_impl_player_locations_BasePlayerLocation__shadowbox' => 'tubepress_core_impl_player_locations_BasePlayerLocation',
            'tubepress_core_impl_player_locations_BasePlayerLocation__solo' => 'tubepress_core_impl_player_locations_BasePlayerLocation',
            'tubepress_core_impl_player_locations_BasePlayerLocation__popup' => 'tubepress_core_impl_player_locations_BasePlayerLocation',
            'tubepress_core_impl_player_locations_BasePlayerLocation__jqmodal' => 'tubepress_core_impl_player_locations_BasePlayerLocation',
            'tubepress_core_impl_player_locations_BasePlayerLocation__static' => 'tubepress_core_impl_player_locations_BasePlayerLocation',
        );
    }

    protected function getExpectedExternalServicesMap()
    {

        return array(

            tubepress_core_api_options_ContextInterface::_ => tubepress_core_api_options_ContextInterface::_,
            tubepress_core_api_event_EventDispatcherInterface::_ => tubepress_core_api_event_EventDispatcherInterface::_,
            tubepress_core_api_template_TemplateFactoryInterface::_ => tubepress_core_api_template_TemplateFactoryInterface::_,
            tubepress_core_api_environment_EnvironmentInterface::_ => tubepress_core_api_environment_EnvironmentInterface::_
        );
    }
}
