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
 * Generates the HTML for a TubePress player location.
 */
interface tubepress_spi_player_PlayerHtmlGenerator
{
    const _ = 'tubepress_spi_player_PlayerHtmlGenerator';

    /**
     * Get's the HTML for the TubePress "player"
     *
     * @param tubepress_api_video_Video $vid The video to display in the player.
     *
     * @throws Exception If something goes wrong.
     *
     * @return string The HTML for this player with the given video.
     */
    function getHtml(tubepress_api_video_Video $vid);
}
