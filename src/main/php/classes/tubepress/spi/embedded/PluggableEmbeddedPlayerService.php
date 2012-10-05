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
