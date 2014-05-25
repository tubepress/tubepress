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
interface tubepress_core_api_player_PlayerLocationInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_api_player_PlayerLocationInterface';

    /**
     * @return string[] The paths for the template factory.
     *
     * @api
     * @since 4.0.0
     */
    function getPathsForTemplateFactory();

    /**
     * @return string The name of this playerLocation. Never empty or null. All alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @param tubepress_core_api_environment_EnvironmentInterface $environment
     *
     * @return tubepress_core_api_url_UrlInterface Gets the URL to this player location's JS init script.
     *
     * @api
     * @since 4.0.0
     */
    function getPlayerJsUrl(tubepress_core_api_environment_EnvironmentInterface $environment);

    /**
     * @return boolean True if this player location produces HTML, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function producesHtml();

    /**
     * @return string The human-readable name of this player location.
     *
     * @api
     * @since 4.0.0
     */
    function getUntranslatedFriendlyName();
}