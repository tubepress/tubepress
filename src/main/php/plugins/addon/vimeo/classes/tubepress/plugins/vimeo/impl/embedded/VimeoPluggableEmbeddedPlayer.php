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
 * Embedded player command for native Vimeo
 */
class tubepress_plugins_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayer implements tubepress_spi_embedded_PluggableEmbeddedPlayer
{
    private static $_URL_PARAM_AUTOPLAY  = 'autoplay';
    private static $_URL_PARAM_TITLE     = 'title';
    private static $_URL_PARAM_BYLINE    = 'byline';
    private static $_URL_PARAM_COLOR     = 'color';
    private static $_URL_PARAM_LOOP      = 'loop';
    private static $_URL_PARAM_PORTRAIT  = 'portrait';
    private static $_URL_PARAM_JS_API    = 'api';
    private static $_URL_PARAM_PLAYER_ID = 'player_id';

    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'vimeo';
    }

    /**
     * @param tubepress_spi_theme_ThemeHandler $themeHandler The theme handler.
     *
     * @return ehough_contemplate_api_Template The template for this embedded player.
     */
    public final function getTemplate(tubepress_spi_theme_ThemeHandler $themeHandler)
    {
        return $themeHandler->getTemplateInstance('embedded/vimeo.tpl.php', TUBEPRESS_ROOT . '/src/main/php/plugins/addon/vimeo/resources/templates');
    }

    /**
     * @param string $videoId The video ID to play.
     *
     * @return ehough_curly_Url The URL of the data for this video.
     */
    public final function getDataUrlForVideo($videoId)
    {
        $context = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        $autoPlay = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $showInfo = $context->get(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $loop     = $context->get(tubepress_api_const_options_names_Embedded::LOOP);
        $jsApi    = $context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);

        $color    = $context->get(tubepress_plugins_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);

        /* build the data URL based on these options */
        $link = new ehough_curly_Url("http://player.vimeo.com/video/$videoId");
        $link->setQueryVariable(self::$_URL_PARAM_AUTOPLAY, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));
        $link->setQueryVariable(self::$_URL_PARAM_COLOR, $color);
        $link->setQueryVariable(self::$_URL_PARAM_LOOP, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $link->setQueryVariable(self::$_URL_PARAM_PORTRAIT, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable(self::$_URL_PARAM_BYLINE, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable(self::$_URL_PARAM_TITLE, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));

        if ($jsApi) {

            $link->setQueryVariable(self::$_URL_PARAM_JS_API, 1);
            $link->setQueryVariable(self::$_URL_PARAM_PLAYER_ID, "tubepress-vimeo-player-$videoId");
        }

        return $link;
    }

    /**
     * @return string The name of the video provider whose videos this player handles.
     */
    function getHandledProviderName()
    {
        return 'vimeo';
    }
}
