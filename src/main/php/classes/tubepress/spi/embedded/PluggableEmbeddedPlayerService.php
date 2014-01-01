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
 * An embedded video player.
 */
interface tubepress_spi_embedded_PluggableEmbeddedPlayerService
{
    const _ = 'tubepress_spi_embedded_PluggableEmbeddedPlayerService';

    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     */
    function getName();

    /**
     * @return string The friendly name of this embedded player service.
     */
    function getFriendlyName();

    /**
     * @param tubepress_spi_theme_ThemeHandler $themeHandler The theme handler.
     *
     * @return ehough_contemplate_api_Template The template for this embedded player.
     */
    function getTemplate(tubepress_spi_theme_ThemeHandler $themeHandler);

    /**
     * @param string $videoId The video ID to play.
     *
     * @return ehough_curly_Url The URL of the data for this video.
     */
    function getDataUrlForVideo($videoId);

    /**
     * @return string The name of the video provider whose videos this player handles.
     */
    function getHandledProviderName();
}