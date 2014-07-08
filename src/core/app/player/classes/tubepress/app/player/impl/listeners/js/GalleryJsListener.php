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
class tubepress_app_player_impl_listeners_js_GalleryJsListener
{
    private static $_OPTIONS = 'options';
    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_app_player_api_PlayerHtmlInterface
     */
    private $_playerHtml;

    /**
     * @var tubepress_app_environment_api_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_app_options_api_ContextInterface         $context,
                                tubepress_app_player_api_PlayerHtmlInterface       $playerHtml,
                                tubepress_app_environment_api_EnvironmentInterface $environment)
    {
        $this->_context     = $context;
        $this->_playerHtml  = $playerHtml;
        $this->_environment = $environment;
    }

    public function onGalleryInitJs(tubepress_lib_event_api_EventInterface $event)
    {
        $args = $event->getSubject();

        $this->_ensureOutermostKeysExist($args);

        $player        = $this->_playerHtml->getActivePlayerLocation();
        $options       = $this->_getOptions($player);

        $args[self::$_OPTIONS] = array_merge($args[self::$_OPTIONS], $options);

        $event->setSubject($args);
    }

    private function _getOptions(tubepress_app_player_api_PlayerLocationInterface $player)
    {
        return array(

            tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION => $player->getName()
        );
    }

    private function _ensureOutermostKeysExist(array &$args)
    {
        foreach (array(self::$_OPTIONS) as $key) {

            if (!isset($args[$key]) || !is_array($args[$key])) {

                $args[$key] = array();
            }
        }
    }
}
