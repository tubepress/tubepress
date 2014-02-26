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
 */
interface tubepress_spi_theme_ThemeInterface extends tubepress_spi_addon_Contributable
{
    const _ = 'tubepress_spi_theme_ThemeInterface';

    /**
     * @return string[] An array, which may be empty but never null, of relative paths
     *                  (from the theme root) of scripts for this theme.
     */
    function getScripts();

    /**
     * @return string[] An array, which may be empty but never null, of relative paths
     *                  (from the theme root) of stylesheets for this theme.
     */
    function getStyles();

    /**
     * @return string The name of this theme's parent. May be null.
     */
    function getParentThemeName();

    /**
     * @return string The absolute path, with trailing slash, of this theme on the filesystem.
     */
    function getRootFilesystemPath();
}