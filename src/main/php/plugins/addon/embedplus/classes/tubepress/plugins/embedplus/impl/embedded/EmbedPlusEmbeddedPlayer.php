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
 * Plays videos with JW FLV Player.
 */
class tubepress_plugins_embedplus_impl_embedded_EmbedPlusPluggableEmbeddedPlayer implements tubepress_spi_embedded_PluggableEmbeddedPlayer
{
    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'embedplus';
    }

    /**
     * @return string The path, relative to TubePress's install root, of the template for this embedded player.
     */
    public final function getRelativePathToTemplate()
    {
        return TUBEPRESS_ROOT . DIRECTORY_SEPARATOR . 'src/main/php/plugins/addon/embedplus/resources/templates/embedded.tpl.php';
    }

    /**
     * @param string $videoId The video ID to play.
     *
     * @return ehough_curly_Url The URL of the data for this video.
     */
    public final function getDataUrlForVideo($videoId)
    {
        return new ehough_curly_Url(sprintf('http://www.youtube.com/embed/%s', $videoId));
    }

    /**
     * @return string The name of the video provider whose videos this player handles.
     */
    public final function getHandledProviderName()
    {
        return 'youtube';
    }
}
