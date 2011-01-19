<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
interface org_tubepress_api_theme_ThemeHandler
{
    /**
     * Gets an instance of a template appropriate for the current theme.
     *
     * @param string $pathToTemplate The relative path (from the root of the theme directory) to the template.
     *
     * @return org_tubepress_api_template_Template The template instance.
     */
    function getTemplateInstance($pathToTemplate);

    function getCssPath($currentTheme, $relative = false);

    function calculateCurrentThemeName();
}
