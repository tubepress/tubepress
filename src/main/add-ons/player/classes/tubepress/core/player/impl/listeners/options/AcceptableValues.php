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
class tubepress_core_player_impl_listeners_options_AcceptableValues
{
    /**
     * @var tubepress_core_player_api_PlayerLocationInterface[]
     */
    private $_playerLocations = array();

    public function onPlayerLocation(tubepress_core_event_api_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $playerLocations = $this->_getPlayerLocationValues();

        $event->setSubject(array_merge($current, $playerLocations));
    }

    private function _getPlayerLocationValues()
    {
        $toReturn = array();

        /**
         * @var $playerLocation tubepress_core_player_api_PlayerLocationInterface
         */
        foreach ($this->_playerLocations as $playerLocation) {

            $toReturn[$playerLocation->getName()] = $playerLocation->getUntranslatedFriendlyName();
        }

        asort($toReturn);

        return $toReturn;
    }

    public function setPlayerLocations(array $players)
    {
        $this->_playerLocations = $players;
    }
}
