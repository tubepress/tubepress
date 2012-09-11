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
    private $_executionContext;

    private $_themeHandler;

    private $_providerCalculator;

    private $_eventDispatcher;

    public function __construct(

        tubepress_spi_context_ExecutionContext    $executionContext,
        tubepress_spi_theme_ThemeHandler          $themeHandler,
        tubepress_spi_provider_ProviderCalculator $providerCalculator,
        ehough_tickertape_api_IEventDispatcher    $eventDispatcher)
    {
        $this->_executionContext   = $executionContext;
        $this->_themeHandler       = $themeHandler;
        $this->_providerCalculator = $providerCalculator;
        $this->_eventDispatcher    = $eventDispatcher;
    }

    /**
     * Get's the HTML for the TubePress "player"
     *
     * @param tubepress_api_video_Video $vid The video to display in the player.
     *
     * @return string The HTML for this player with the given video.
     */
    public final function getHtml(tubepress_api_video_Video $vid)
    {
        $playerName   = $this->_executionContext->get(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $template     = $this->_themeHandler->getTemplateInstance("players/$playerName.tpl.php");
        $providerName = $this->_providerCalculator->calculateProviderOfVideoId($vid->getId());

        /*
         * Run filters for the player template construction.
         */
        $playerTemplateEvent = new tubepress_api_event_PlayerTemplateConstruction($template, $vid, $providerName, $playerName);
        $this->_eventDispatcher->dispatch(

            tubepress_api_event_PlayerTemplateConstruction::EVENT_NAME,
            $playerTemplateEvent
        );

        /*
         * Run filters for the player HTML construction.
         */
        $html            = $playerTemplateEvent->template->toString();
        $playerHtmlEvent = new tubepress_api_event_PlayerHtmlConstruction($html, $vid, $providerName, $playerName);
        $this->_eventDispatcher->dispatch(

            tubepress_api_event_PlayerHtmlConstruction::EVENT_NAME,
            $playerHtmlEvent
        );

        /*
         * Run filters for the HTML construction.
         */
        $html      = $playerHtmlEvent->playerHtml;
        $htmlEvent = new tubepress_api_event_HtmlConstruction($html);
        $this->_eventDispatcher->dispatch(

            tubepress_api_event_HtmlConstruction::EVENT_NAME,
            $htmlEvent
        );

        $html = $htmlEvent->html;

        return $html;
    }
}
