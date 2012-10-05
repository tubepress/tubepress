<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
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
        $executionContextService    = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $themeHandler               = tubepress_impl_patterns_ioc_KernelServiceLocator::getThemeHandler();
        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        $requestedPlayerLocation   = $executionContextService->get(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $playerLocation            = null;
        $registeredPlayerLocations = $serviceCollectionsRegistry->getAllServicesOfType(tubepress_spi_player_PlayerLocation::_);

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

        $eventDispatcherService = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

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
