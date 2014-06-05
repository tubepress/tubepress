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
 * TubePress theme.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_theme_api_ThemeInterface extends tubepress_api_contrib_ContributableInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_theme_api_ThemeInterface';

    /**
     * @return string[] An array, which may be empty but never null, of relative paths
     *                  (from the theme root) of scripts for this theme.
     *
     * @api
     * @since 4.0.0
     */
    function getScripts();

    /**
     * @return string[] An array, which may be empty but never null, of relative paths
     *                  (from the theme root) of stylesheets for this theme.
     *
     * @api
     * @since 4.0.0
     */
    function getStyles();

    /**
     * @return string The name of this theme's parent. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getParentThemeName();

    /**
     * @return string The absolute path, with trailing slash, of this theme on the filesystem.
     *
     * @api
     * @since 4.0.0
     */
    function getRootFilesystemPath();

    /**
     * @return bool True if this is a system theme, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function isSystemTheme();
}