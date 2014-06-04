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
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_player_api_PlayerHtmlInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_player_api_PlayerHtmlInterface';

    /**
     * Get's the HTML for the TubePress "player"
     *
     * @param tubepress_core_provider_api_MediaItem $mediaItem The video to display in the player.
     *
     * @return string|null The HTML for this player with the given video, or null if not found.
     *
     * @api
     * @since 4.0.0
     */
    function getHtml(tubepress_core_provider_api_MediaItem $mediaItem);
}