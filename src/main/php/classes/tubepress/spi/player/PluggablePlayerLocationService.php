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
interface tubepress_spi_player_PluggablePlayerLocationService
{
    const _ = 'tubepress_spi_player_PluggablePlayerLocationService';

    /**
     * @param tubepress_spi_theme_ThemeHandler $themeHandler The theme handler.
     *
     * @return ehough_contemplate_api_Template The player's template.
     */
    function getTemplate(tubepress_spi_theme_ThemeHandler $themeHandler);

    /**
     * @return string The name of this playerLocation. Never empty or null. All alphanumerics and dashes.
     */
    function getName();

    /**
     * @return string Gets the relative path to this player location's JS init script.
     */
    function getRelativePlayerJsUrl();

    /**
     * @return boolean True if this player location produces HTML, false otherwise.
     */
    function producesHtml();

    /**
     * @return string The human-readable name of this player location.
     */
    function getFriendlyName();
}