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
interface tubepress_spi_theme_ThemeHandler
{
    const _ = 'tubepress_spi_theme_ThemeHandler';

    /**
     * Gets an instance of a template appropriate for the current theme.
     *
     * @param string $pathToTemplate    The relative path (from the root of the user's theme directory,
     *                                  or the fallback directory) to the template.
     * @param string $fallBackDirectory The absolute path to a directory where this template (defined by the relative
     *                                  path, can be found). You should make sure that the template will *always* exist
     *                                  here.
     *
     * @throws RuntimeException If the template could not be found.
     *
     * @return ehough_contemplate_api_Template The template instance.
     */
    function getTemplateInstance($pathToTemplate, $fallBackDirectory);

    /**
     * Returns the name of the current TubePress theme in use.
     *
     * @return string The current theme name, or 'default' if the default theme is in use or if there was a problem.
     */
    function calculateCurrentThemeName();
}
