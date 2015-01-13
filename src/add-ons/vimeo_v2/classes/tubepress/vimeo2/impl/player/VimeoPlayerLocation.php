<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Plays videos with jqmodal.
 */
class tubepress_vimeo2_impl_player_VimeoPlayerLocation implements tubepress_app_api_player_PlayerLocationInterface
{
    /**
     * @return string The name of this playerLocation. Never empty or null. All alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'vimeo';
    }

    /**
     * @return string The human-readable name of this player location.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'from the video\'s original Vimeo page';                 //>(translatable)<
    }

    /**
     * Get the data required to populate the invoking HTML anchor.
     *
     * @param tubepress_app_api_media_MediaItem $mediaItem
     *
     * @return array An associative array where the keys are HTML <a> attribute names and the values are
     *               the corresponding attribute values. May be empty nut never null.
     *
     * @api
     * @since 4.0.0
     */
    public function getAttributesForInvocationAnchor(tubepress_app_api_media_MediaItem $mediaItem)
    {
        return array(
            'target' => '_blank',
            'rel'    => 'external nofollow',
            'href'   => sprintf('https://vimeo.com/%d', $mediaItem->getId())
        );
    }

    /**
     * @return string The template name that this player location uses when it is loaded
     *                statically on a gallery page, or null if not required on static page load.
     *
     * @api
     * @since 4.0.0
     */
    public function getStaticTemplateName()
    {
        return null;
    }

    /**
     * @return string The template name that this player location uses when it is loaded
     *                dynamically via Ajax, or null if not used via Ajax.
     *
     * @api
     * @since 4.0.0
     */
    public function getAjaxTemplateName()
    {
        return null;
    }
}