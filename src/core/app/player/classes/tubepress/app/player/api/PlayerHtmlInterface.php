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
interface tubepress_app_player_api_PlayerHtmlInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_player_api_PlayerHtmlInterface';

    /**
     * Get's the player HTML for the given media item. This HTML will be loaded
     * into the DOM on page load.
     *
     * @param tubepress_app_media_item_api_MediaItem $mediaItem The item to display in the player.
     *
     * @return string The HTML for this player with the given item. May be empty if this player doesn't need
     *                any HTML loaded on the page load.
     *
     * @api
     * @since 4.0.0
     */
    function getStaticHtml(tubepress_app_media_item_api_MediaItem $mediaItem);

    /**
     * Get's the Ajax HTML for the TubePress "player"
     *
     * @param tubepress_app_media_item_api_MediaItem $mediaItem The item to display in the player.
     *
     * @return string The HTML for this player with the given item. May be empty if this player doesn't support
     *                Ajax.
     *
     * @api
     * @since 4.0.0
     */
    function getAjaxHtml(tubepress_app_media_item_api_MediaItem $mediaItem);

    /**
     * @return tubepress_app_player_api_PlayerLocationInterface
     *
     * @api
     * @since 4.0.0
     */
    function getActivePlayerLocation();
}