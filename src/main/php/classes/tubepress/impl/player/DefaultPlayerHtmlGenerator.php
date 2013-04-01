<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
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
     * Get's the HTML for the TubePress "player"
     *
     * @param tubepress_api_video_Video $vid The video to display in the player.
     *
     * @return string The HTML for this player with the given video.
     */
    public final function getHtml(tubepress_api_video_Video $vid)
    {
        $executionContextService   = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $themeHandler              = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $requestedPlayerLocation   = $executionContextService->get(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $playerLocation            = null;
        $registeredPlayerLocations = tubepress_impl_patterns_sl_ServiceLocator::getPlayerLocations();

        foreach ($registeredPlayerLocations as $registeredPlayerLocation) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($registeredPlayerLocation->getName() === $requestedPlayerLocation) {

                $playerLocation = $registeredPlayerLocation;

                break;
            }
        }

        if ($playerLocation === null) {

            return null;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $template = $playerLocation->getTemplate($themeHandler);

        $eventDispatcherService = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        /*
         * Run filters for the player template construction.
         */
        /** @noinspection PhpUndefinedMethodInspection */
        $playerTemplateEvent = new tubepress_api_event_TubePressEvent(

            $template, array(

                'video'      => $vid,
                'playerName' => $playerLocation->getName())
        );

        $eventDispatcherService->dispatch(

            tubepress_api_const_event_CoreEventNames::PLAYER_TEMPLATE_CONSTRUCTION,
            $playerTemplateEvent
        );

        /*
         * Run filters for the player HTML construction.
         */
        $html            = $playerTemplateEvent->getSubject()->toString();

        /** @noinspection PhpUndefinedMethodInspection */
        $playerHtmlEvent = new tubepress_api_event_TubePressEvent($html, array(

            'video'        => $vid,
            'playerName'   => $playerLocation->getName()
        ));

        $eventDispatcherService->dispatch(

            tubepress_api_const_event_CoreEventNames::PLAYER_HTML_CONSTRUCTION,
            $playerHtmlEvent
        );

        /*
         * Run filters for the HTML construction.
         */
        $html      = $playerHtmlEvent->getSubject();
        $htmlEvent = new tubepress_api_event_TubePressEvent($html);
        $eventDispatcherService->dispatch(

            tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION,
            $htmlEvent
        );

        $html = $htmlEvent->getSubject();

        return $html;
    }
}
