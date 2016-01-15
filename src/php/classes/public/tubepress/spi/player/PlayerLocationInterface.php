<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
interface tubepress_spi_player_PlayerLocationInterface
{
    /**
     * @return string The name of this player location.
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return string The display name of this player location.
     *
     * @api
     * @since 4.0.0
     */
    function getUntranslatedDisplayName();

    /**
     * @return string The template name that this player location uses when it is loaded
     *                statically on a gallery page, or null if not required on static page load.
     *
     * @api
     * @since 4.0.0
     */
    function getStaticTemplateName();

    /**
     * @return string The template name that this player location uses when it is loaded
     *                dynamically via Ajax, or null if not used via Ajax.
     *
     * @api
     * @since 4.0.0
     */
    function getAjaxTemplateName();

    /**
     * Get the data required to populate the invoking HTML anchor.
     *
     * @param tubepress_api_media_MediaItem $mediaItem
     *
     * @return array An associative array where the keys are HTML <a> attribute names and the values are
     *               the corresponding attribute values. May be empty nut never null.
     *
     * @api
     * @since 4.0.0
     */
    function getAttributesForInvocationAnchor(tubepress_api_media_MediaItem $mediaItem);
}