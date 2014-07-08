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
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_player_api_PlayerLocationInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_player_api_PlayerLocationInterface';

    /**
     * @return string[] The paths for the template factory.
     *
     * @api
     * @since 4.0.0
     */
    function getTemplatePathsForStaticContent();

    /**
     * @return string[] The paths for the template factory.
     *
     * @api
     * @since 4.0.0
     */
    function getTemplatePathsForAjaxContent();

    /**
     * @return string The name of this playerLocation. Never empty or null. All alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return string The human-readable name of this player location.
     *
     * @api
     * @since 4.0.0
     */
    function getUntranslatedDisplayName();

    /**
     * @param tubepress_app_media_item_api_MediaItem $mediaItem
     *
     * @return array An an associative array of attribute names to values that should be included in any
     *               HTML anchors to invoke playback of this media item. e.g. array('href' => 'http://foo.bar/video/id')
     *               will end up like <a href="http://foo.bar/video/id" ...>
     *
     * @api
     * @since 4.0.0
     */
    function getInvocationAnchorAttributeArray(tubepress_app_media_item_api_MediaItem $mediaItem);
}