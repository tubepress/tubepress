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
class tubepress_addons_core_impl_player_ShadowboxPluggablePlayerLocationService implements tubepress_spi_player_PluggablePlayerLocationService
{
    /**
     * @param tubepress_spi_theme_ThemeHandlerInterface $themeHandler The theme handler.
     *
     * @return ehough_contemplate_api_Template The player's template.
     */
    public final function getTemplate(tubepress_spi_theme_ThemeHandlerInterface $themeHandler)
    {
        return $themeHandler->getTemplateInstance('players/shadowbox.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default');
    }

    /**
     * @return string The name of this playerLocation. Never empty or null. All alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'shadowbox';
    }

    /**
     * @return string Gets the relative path to this player location's JS init script.
     */
    public final function getPlayerJsUrl()
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $sysUrl              = $environmentDetector->getBaseUrl();

        return "$sysUrl/src/main/web/players/shadowbox/shadowbox.js";
    }

    /**
     * @return boolean True if this player location produces HTML, false otherwise.
     */
    public final function producesHtml()
    {
        return true;
    }

    /**
     * @return string The human-readable name of this player location.
     */
    public final function getFriendlyName()
    {
        return 'with Shadowbox';                                        //>(translatable)<
    }
}