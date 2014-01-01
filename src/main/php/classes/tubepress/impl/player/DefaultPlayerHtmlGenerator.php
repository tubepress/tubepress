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
 * A TubePress "player", such as lightWindow, GreyBox, popup window, etc
 */
class tubepress_impl_player_DefaultPlayerHtmlGenerator implements tubepress_spi_player_PlayerHtmlGenerator
{
    /**
     * @var array
     */
    private $_playerLocations = array();

    /**
     * Get's the HTML for the TubePress "player"
     *
     * @param tubepress_api_video_Video $vid The video to display in the player.
     *
     * @return string The HTML for this player with the given video.
     */
    public final function getHtml(tubepress_api_video_Video $vid)
    {
        $executionContextService  = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $themeHandler             = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $requestedPlayerLocation  = $executionContextService->get(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $playerLocation           = null;

        /**
         * @var $registeredPlayerLocation tubepress_spi_player_PluggablePlayerLocationService
         */
        foreach ($this->_playerLocations as $registeredPlayerLocation) {

            if ($registeredPlayerLocation->getName() === $requestedPlayerLocation) {

                $playerLocation = $registeredPlayerLocation;

                break;
            }
        }

        if ($playerLocation === null) {

            return null;
        }

        /**
         * @var $playerLocation tubepress_spi_player_PluggablePlayerLocationService
         */
        $template = $playerLocation->getTemplate($themeHandler);

        $eventDispatcherService = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        /*
         * Run filters for the player template construction.
         */
        $playerTemplateEvent = new tubepress_spi_event_EventBase(

            $template, array(

                'video'      => $vid,
                'playerName' => $playerLocation->getName())
        );

        $eventDispatcherService->dispatch(

            tubepress_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION,
            $playerTemplateEvent
        );

        /*
         * Run filters for the player HTML construction.
         */
        $html            = $playerTemplateEvent->getSubject()->toString();

        $playerHtmlEvent = new tubepress_spi_event_EventBase($html, array(

            'video'        => $vid,
            'playerName'   => $playerLocation->getName()
        ));

        $eventDispatcherService->dispatch(

            tubepress_api_const_event_EventNames::HTML_PLAYERLOCATION,
            $playerHtmlEvent
        );

        return $playerHtmlEvent->getSubject();
    }

    public function setPluggablePlayerLocations(array $playerLocations)
    {
        $this->_playerLocations = $playerLocations;
    }
}
