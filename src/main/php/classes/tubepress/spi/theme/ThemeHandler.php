<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
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
