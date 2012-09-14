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
 * Embedded player command for YouTube iframe embeds
 */
class tubepress_impl_embedded_commands_YouTubeIframeCommand extends tubepress_impl_embedded_commands_AbstractEmbeddedCommand
{
    protected function _canHandle($providerName, $videoId, tubepress_spi_context_ExecutionContext $context)
    {
        return $providerName === tubepress_spi_provider_Provider::YOUTUBE;
    }

    protected function _getTemplatePath($providerName, $videoId, tubepress_spi_context_ExecutionContext $context)
    {
        return "embedded_flash/youtube.tpl.php";
    }

    protected function _getEmbeddedDataUrl($providerName, $videoId, tubepress_spi_context_ExecutionContext $context)
    {
        $link  = new ehough_curly_Url('http://www.youtube.com/embed/' . $videoId);

        $showRelated     = $context->get(tubepress_api_const_options_names_Embedded::SHOW_RELATED);
        $autoPlay        = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $loop            = $context->get(tubepress_api_const_options_names_Embedded::LOOP);
        $fullscreen      = $context->get(tubepress_api_const_options_names_Embedded::FULLSCREEN);
        $enableJsApi     = $context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $galleryId       = $context->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $playerColor     = tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($context->get(tubepress_api_const_options_names_Embedded::PLAYER_COLOR), '999999');
        $playerHighlight = tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($context->get(tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT), 'FFFFFF');
        $showInfo        = $context->get(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $autoHide        = $context->get(tubepress_api_const_options_names_Embedded::AUTOHIDE);
        $modestBranding  = $context->get(tubepress_api_const_options_names_Embedded::MODEST_BRANDING);

        if (!($playerColor == '999999' && $playerHighlight == 'FFFFFF')) {

            $link->setQueryVariable('color1', $playerHighlight);
            $link->setQueryVariable('color2', $playerColor);
        }

        $link->setQueryVariable('rel', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showRelated));
        $link->setQueryVariable('autoplay', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));
        $link->setQueryVariable('loop', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $link->setQueryVariable('fs', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($fullscreen));
        $link->setQueryVariable('showinfo', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable('wmode', 'transparent');
        $link->setQueryVariable('enablejsapi', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($enableJsApi));
        $link->setQueryVariable('autohide', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoHide));
        $link->setQueryVariable('modestbranding', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($modestBranding));

        if ($context->get(tubepress_api_const_options_names_Embedded::HIGH_QUALITY)) {

            $link->setQueryVariable('hd', '1');
        }

        return $link;
    }

    protected function _getEmbeddedImplName()
    {
        return 'youtube';
    }
}
