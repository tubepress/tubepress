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
 */
interface tubepress_spi_theme_ThemeHandlerInterface
{
    const _ = 'tubepress_spi_theme_ThemeHandlerInterface';

    /**
     * Gets an instance of a template appropriate for the current theme.
     *
     * @param string $pathToTemplate    The relative path (from the root of the user's theme directory,
     *                                  or the fallback directory) to the template.
     * @param string $fallBackDirectory The absolute path to a directory where this template, defined by the relative
     *                                  path, can be found. You should make sure that the template will *always* exist
     *                                  here.
     * @param string|null $themeName    The name of the theme to query, or null for the currently stored theme.
     *
     * @throws RuntimeException If the template could not be found.
     *
     * @return ehough_contemplate_api_Template The template instance.
     */
    function getTemplateInstance($pathToTemplate, $fallBackDirectory, $themeName = null);

    /**
     * @return string[] An associative array of strings, which may be empty but never null, of all known theme
     *                  names to their untranslated titles.
     */
    function getMapOfAllThemeNamesToTitles();

    /**
     * @param string|null $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return string[] URLs of CSS stylesheets required for the current theme. May be empty but never null.
     */
    function getStyles($themeName = null);

    /**
     * @param string|null $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return string[] URLs of JS scripts required for the current theme. May be empty but never null.
     */
    function getScripts($themeName = null);

    /**
     * @param string|null $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return string[] URLs of screenshots for the current theme. May be empty but never null.
     */
    function getScreenshots($themeName = null);
}