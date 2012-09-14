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
class tubepress_impl_embedded_commands_VimeoCommand extends tubepress_impl_embedded_commands_AbstractEmbeddedCommand
{
    const VIMEO_EMBEDDED_PLAYER_URL = 'http://player.vimeo.com/';
    const VIMEO_QUERYPARAM_AUTOPLAY = 'autoplay';
    const VIMEO_QUERYPARAM_TITLE    = 'title';
    const VIMEO_QUERYPARAM_BYLINE   = 'byline';
    const VIMEO_QUERYPARAM_COLOR    = 'color';
    const VIMEO_QUERYPARAM_LOOP     = 'loop';
    const VIMEO_QUERYPARAM_PORTRAIT = 'portrait';
    private static $_paramJsApi     = 'api';
    private static $_paramPlayerId  = 'player_id';

    protected function _canHandle($providerName, $videoId, tubepress_spi_context_ExecutionContext $context)
    {
        return $providerName === tubepress_spi_provider_Provider::VIMEO;
    }

    protected function _getTemplatePath($providerName, $videoId, tubepress_spi_context_ExecutionContext $context)
    {
        return 'embedded_flash/vimeo.tpl.php';
    }

    protected function _getEmbeddedDataUrl($providerName, $videoId, tubepress_spi_context_ExecutionContext $context)
    {
        $autoPlay = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $color    = $context->get(tubepress_api_const_options_names_Embedded::PLAYER_COLOR);
        $showInfo = $context->get(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $loop     = $context->get(tubepress_api_const_options_names_Embedded::LOOP);
        $jsApi    = $context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);

        /* build the data URL based on these options */
        $link = new ehough_curly_Url(self::VIMEO_EMBEDDED_PLAYER_URL . "video/$videoId");
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_AUTOPLAY, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_COLOR, $color);
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_LOOP, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_TITLE, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_BYLINE, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_PORTRAIT, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));

        if ($jsApi) {

            $link->setQueryVariable(self::$_paramJsApi, 1);
            $link->setQueryVariable(self::$_paramPlayerId, "tubepress-vimeo-player-$videoId");
        }

        return $link;
    }

    protected function _getEmbeddedImplName()
    {
        return 'vimeo';
    }
}
