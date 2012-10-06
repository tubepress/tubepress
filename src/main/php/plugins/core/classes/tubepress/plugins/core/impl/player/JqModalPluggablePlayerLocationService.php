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
 * Plays videos with jqmodal.
 */
class tubepress_plugins_core_impl_player_JqModalPluggablePlayerLocationService implements tubepress_spi_player_PluggablePlayerLocationService
{
    /**
     * @param tubepress_spi_theme_ThemeHandler $themeHandler The theme handler.
     *
     * @return ehough_contemplate_api_Template The player's template.
     */
    public final function getTemplate(tubepress_spi_theme_ThemeHandler $themeHandler)
    {
        return $themeHandler->getTemplateInstance('players/jqmodal.tpl.php', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default');
    }

    /**
     * @return string The name of this playerLocation. Never empty or null. All alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'jqmodal';
    }
}
