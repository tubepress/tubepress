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
class tubepress_vimeo_impl_player_VimeoPlayerLocation implements tubepress_core_player_api_PlayerLocationInterface
{
    /**
     * @return string[] The paths for the template factory.
     *
     * @api
     * @since 4.0.0
     */
    public function getPathsForTemplateFactory()
    {
        return null;
    }

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
     * @param tubepress_core_environment_api_EnvironmentInterface $environment
     *
     * @return tubepress_core_url_api_UrlInterface Gets the URL to this player location's JS init script.
     *
     * @api
     * @since 4.0.0
     */
    public function getPlayerJsUrl(tubepress_core_environment_api_EnvironmentInterface $environment)
    {
        $sysUrl = $environment->getBaseUrl()->getClone();

        $sysUrl->addPath('core/player/web/players/vimeo/vimeo.js');

        return $sysUrl;
    }

    /**
     * @return boolean True if this player location produces HTML, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function producesHtml()
    {
        return false;
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
     * @return bool True if this player location should show HTML when the gallery is initially loaded, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function displaysHtmlOnInitialGalleryLoad()
    {
        return false;
    }
}