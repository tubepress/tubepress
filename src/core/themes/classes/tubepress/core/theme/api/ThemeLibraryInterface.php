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
 * TubePress theme handler.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_theme_api_ThemeLibraryInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_theme_api_ThemeLibraryInterface';

    /**
     * @return string The current theme name.
     *
     * @api
     * @since 4.0.0
     */
    function getCurrentThemeName();

    /**
     * @param string      $relativePath The relative path to the template.
     * @param string|null $themeName    The theme name, or null to use the current theme.
     *
     * @return string The absolute path to the given template, or null if not found in the theme hierarchy.
     *
     * @throws InvalidArgumentException If no such theme.
     *
     * @api
     * @since 4.0.0
     */
    function getAbsolutePathToTemplate($relativePath, $themeName = null);

    /**
     * @return string[] An associative array of strings, which may be empty but never null, of all known theme
     *                  names to their untranslated titles.
     *
     * @api
     * @since 4.0.0
     */
    function getMapOfAllThemeNamesToTitles();

    /**
     * @param string|null $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return tubepress_core_url_api_UrlInterface[] URLs of CSS stylesheets required for the current theme. May be empty but never null.
     *
     * @throws InvalidArgumentException If no such theme.
     *
     * @api
     * @since 4.0.0
     */
    function getStylesUrls($themeName = null);

    /**
     * @param string|null $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return tubepress_core_url_api_UrlInterface[] URLs of JS scripts required for the current theme. May be empty but never null.
     *
     * @throws InvalidArgumentException If no such theme.
     *
     * @api
     * @since 4.0.0
     */
    function getScriptsUrls($themeName = null);

    /**
     * @param string|null $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return tubepress_core_url_api_UrlInterface[] URLs of screenshots for the current theme. May be empty but never null.
     *
     * @throws InvalidArgumentException If no such theme.
     *
     * @api
     * @since 4.0.0
     */
    function getScreenshots($themeName = null);
}