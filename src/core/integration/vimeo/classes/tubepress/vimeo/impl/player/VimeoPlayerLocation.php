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
 * Plays videos with jqmodal.
 */
class tubepress_vimeo_impl_player_VimeoPlayerLocation implements tubepress_app_player_api_PlayerLocationInterface
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
     * @param tubepress_app_media_item_api_MediaItem $mediaItem
     *
     * @return array An an associative array of attribute names to values that should be included in any
     *               HTML anchors to invoke playback of this media item. e.g. array('href' => 'http://foo.bar/video/id')
     *               will end up like <a href="http://foo.bar/video/id" ...>
     *
     * @api
     * @since 4.0.0
     */
    public function getInvocationAnchorAttributeArray(tubepress_app_media_item_api_MediaItem $mediaItem)
    {
        return array(
            'target' => '_blank',
            'rel'    => 'external nofollow',
            'href'   => sprintf('https://vimeo.com/%d', $mediaItem->getId())
        );
    }

    /**
     * @return string[] The paths for the template factory.
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplatePathsForStaticContent()
    {
        return array();
    }

    /**
     * @return string[] The paths for the template factory.
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplatePathsForAjaxContent()
    {
        return array();
    }
}