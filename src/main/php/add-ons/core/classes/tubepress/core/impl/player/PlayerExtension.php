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
class tubepress_core_impl_player_PlayerExtension implements tubepress_api_ioc_ContainerExtensionInterface
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

            tubepress_core_api_player_PlayerHtmlInterface::_,
            'tubepress_core_impl_player_PlayerHtml'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_));

        $containerBuilder->register(

            'tubepress_core_impl_player_locations_BasePlayerLocation__normal',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->addArgument('normal')
         ->addArgument('normally (at the top of your gallery)')                 //>(translatable)<)
         ->addArgument(array('players/normal.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/normal.tpl.php'))
         ->addArgument('src/main/web/players/normal/normal.js')
         ->addArgument(true)
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
             'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
             'method'   => 'onSelectPlayerLocation',
             'priority' => 10000
         ));

        $containerBuilder->register(

            'tubepress_core_impl_player_locations_BasePlayerLocation__shadowbox',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->addArgument('shadowbox')
         ->addArgument('with Shadowbox')                 //>(translatable)<)
         ->addArgument(array('players/shadowbox.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/shadowbox.tpl.php'))
         ->addArgument('src/main/web/players/shadowbox/shadowbox.js')
         ->addArgument(true)
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9900
        ));

        $containerBuilder->register(

            'tubepress_core_impl_player_locations_BasePlayerLocation__popup',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->addArgument('popup')
         ->addArgument('in a popup window')                 //>(translatable)<)
         ->addArgument(array('players/popup.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/popup.tpl.php'))
         ->addArgument('src/main/web/players/popup/popup.js')
         ->addArgument(true)
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9800
         ));

        $containerBuilder->register(

            'tubepress_core_impl_player_locations_BasePlayerLocation__solo',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->addArgument('solo')
         ->addArgument('in a new window on its own')                 //>(translatable)<)
         ->addArgument(array())
         ->addArgument('src/main/web/players/solo/solo.js')
         ->addArgument(false)
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9700
         ));

        $containerBuilder->register(

            'tubepress_core_impl_player_locations_BasePlayerLocation__static',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->addArgument('static')
         ->addArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<)
         ->addArgument(array('players/static.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/static.tpl.php'))
         ->addArgument('src/main/web/players/static/static.js')
         ->addArgument(true)
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9600
         ));

        $containerBuilder->register(

            'tubepress_core_impl_player_locations_BasePlayerLocation__jqmodal',
            'tubepress_core_impl_player_locations_BasePlayerLocation'
        )->addArgument('jqmodal')
         ->addArgument('with jqModal')                 //>(translatable)<)
         ->addArgument(array('players/jqmodal.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/jqmodal.tpl.php'))
         ->addArgument('src/main/web/players/jqmodal/jqmodal.js')
         ->addArgument(true)
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9500
         ));
    }
}