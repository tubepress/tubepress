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
class tubepress_core_player_ioc_PlayerExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_core_player_impl_listeners_html_SoloPlayerListener',
            'tubepress_core_player_impl_listeners_html_SoloPlayerListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
            'method'   => 'onHtmlGeneration',
            'priority' => 9000
        ));

        $containerBuilder->register(
            'tubepress_core_player_impl_listeners_options_AcceptableValues',
            'tubepress_core_player_impl_listeners_options_AcceptableValues'
        )->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION,
            'method'   => 'onPlayerLocation',
            'priority' => 30000,
        ))->addTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_player_api_PlayerLocationInterface::_,
            'method' => 'setPlayerLocations'
        ));

        $containerBuilder->register(
            'tubepress_core_player_impl_listeners_template_PlayerLocationCoreVariables',
            'tubepress_core_player_impl_listeners_template_PlayerLocationCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_embedded_api_EmbeddedHtmlInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_TEMPLATE,
            'method'   => 'onPlayerTemplate',
            'priority' => 10000
        ));

        $containerBuilder->register(
            tubepress_core_player_api_PlayerHtmlInterface::_,
            'tubepress_core_player_impl_PlayerHtml'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));

        $containerBuilder->register(

            'tubepress_core_player_impl_BasePlayerLocation__normal',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->addArgument('normal')
         ->addArgument('normally (at the top of your gallery)')                 //>(translatable)<)
         ->addArgument(array('players/normal.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/normal.tpl.php'))
         ->addArgument('src/core/player/web/players/normal/normal.js')
         ->addArgument(true)
         ->addArgument(true)
         ->addTag(tubepress_core_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
             'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
             'method'   => 'onSelectPlayerLocation',
             'priority' => 10000
         ));

        $containerBuilder->register(

            'tubepress_core_player_impl_BasePlayerLocation__shadowbox',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->addArgument('shadowbox')
         ->addArgument('with Shadowbox')                 //>(translatable)<)
         ->addArgument(array('players/shadowbox.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/shadowbox.tpl.php'))
         ->addArgument('src/core/player/web/players/shadowbox/shadowbox.js')
         ->addArgument(true)
         ->addArgument(false)
         ->addTag(tubepress_core_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9900
        ));

        $containerBuilder->register(

            'tubepress_core_player_impl_BasePlayerLocation__popup',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->addArgument('popup')
         ->addArgument('in a popup window')                 //>(translatable)<)
         ->addArgument(array('players/popup.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/popup.tpl.php'))
         ->addArgument('src/core/player/web/players/popup/popup.js')
         ->addArgument(true)
         ->addArgument(false)
         ->addTag(tubepress_core_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9800
         ));

        $containerBuilder->register(

            'tubepress_core_player_impl_BasePlayerLocation__solo',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->addArgument('solo')
         ->addArgument('in a new window on its own')                 //>(translatable)<)
         ->addArgument(array())
         ->addArgument('src/core/player/web/players/solo/solo.js')
         ->addArgument(false)
         ->addArgument(false)
         ->addTag(tubepress_core_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9700
         ));

        $containerBuilder->register(

            'tubepress_core_player_impl_BasePlayerLocation__static',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->addArgument('static')
         ->addArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<)
         ->addArgument(array('players/static.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/static.tpl.php'))
         ->addArgument('src/core/player/web/players/static/static.js')
         ->addArgument(true)
         ->addArgument(true)
         ->addTag(tubepress_core_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9600
         ));

        $containerBuilder->register(

            'tubepress_core_player_impl_BasePlayerLocation__jqmodal',
            'tubepress_core_player_impl_BasePlayerLocation'
        )->addArgument('jqmodal')
         ->addArgument('with jqModal')                 //>(translatable)<)
         ->addArgument(array('players/jqmodal.tpl.php', TUBEPRESS_ROOT . '/src/core/themes/web/default/players/jqmodal.tpl.php'))
         ->addArgument('src/core/player/web/players/jqmodal/jqmodal.js')
         ->addArgument(true)
         ->addArgument(false)
         ->addTag(tubepress_core_player_api_PlayerLocationInterface::_)
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
            'method'   => 'onSelectPlayerLocation',
            'priority' => 9500
         ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_player', array(

            'defaultValues' => array(
                tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION => 'normal',
            ),

            'labels' => array(
                tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION => 'Play each video',      //>(translatable)<
            )
        ));
    }
}