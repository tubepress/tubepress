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
     * @return string Gets the URL to this player location's JS init script.
     */
    function getPlayerJsUrl();

    /**
     * @return boolean True if this player location produces HTML, false otherwise.
     */
    function producesHtml();

    /**
     * @return string The human-readable name of this player location.
     */
    function getFriendlyName();
}