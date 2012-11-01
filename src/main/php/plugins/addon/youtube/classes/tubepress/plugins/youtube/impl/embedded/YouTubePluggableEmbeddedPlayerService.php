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
 * Handles the heavy lifting for YouTube.
 */
class tubepress_plugins_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService implements tubepress_spi_embedded_PluggableEmbeddedPlayerService
{
    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'youtube';
    }

    /**
     * @param tubepress_spi_theme_ThemeHandler $themeHandler The theme handler.
     *
     * @return ehough_contemplate_api_Template The template for this embedded player.
     */
    public final function getTemplate(tubepress_spi_theme_ThemeHandler $themeHandler)
    {
        return $themeHandler->getTemplateInstance('embedded/youtube.tpl.php', TUBEPRESS_ROOT . '/src/main/php/plugins/addon/youtube/resources/templates');
    }

    /**
     * @param string $videoId The video ID to play.
     *
     * @return ehough_curly_Url The URL of the data for this video.
     */
    public final function getDataUrlForVideo($videoId)
    {
        $link  = new ehough_curly_Url('http://www.youtube.com/embed/' . $videoId);

        $context = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        $autoPlay        = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $loop            = $context->get(tubepress_api_const_options_names_Embedded::LOOP);
        $showInfo        = $context->get(tubepress_api_const_options_names_Embedded::SHOW_INFO);

        $autoHide        = $context->get(tubepress_plugins_youtube_api_const_options_names_Embedded::AUTOHIDE);
        $annotations     = $context->get(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS);
        $cc              = $context->get(tubepress_plugins_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS);
        $controls        = $context->get(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_CONTROLS);
        $disableKeys     = $context->get(tubepress_plugins_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD);
        $enableJsApi     = $context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $fullscreen      = $context->get(tubepress_plugins_youtube_api_const_options_names_Embedded::FULLSCREEN);
        $modestBranding  = $context->get(tubepress_plugins_youtube_api_const_options_names_Embedded::MODEST_BRANDING);
        $showRelated     = $context->get(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_RELATED);
        $theme           = $context->get(tubepress_plugins_youtube_api_const_options_names_Embedded::THEME);

        $link->setQueryVariable('autohide', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoHide));
        $link->setQueryVariable('autoplay', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));

        if ($cc) {

            $link->setQueryVariable('cc_load_policy', '1');
        }

        $link->setQueryVariable('controls', self::_getControlsValue($controls));
        $link->setQueryVariable('disablekb', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($disableKeys));
        $link->setQueryVariable('enablejsapi', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($enableJsApi));
        $link->setQueryVariable('fs', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($fullscreen));
        $link->setQueryVariable('iv_load_policy', self::_getAnnotationsValue($annotations));
        $link->setQueryVariable('loop', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $link->setQueryVariable('modestbranding', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($modestBranding));
        $link->setQueryVariable('rel', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showRelated));
        $link->setQueryVariable('showinfo', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable('theme', $theme);

        return $link;
    }

    /**
     * @return string The name of the video provider whose videos this player handles.
     */
    public final function getHandledProviderName()
    {
        return 'youtube';
    }

    /**
     * @return string The friendly name of this embedded player service.
     */
    public final function getFriendlyName()
    {
        return 'YouTube';
    }

    private static function _getAnnotationsValue($raw)
    {
        return $raw ? 1 : 3;
    }

    private static function _getControlsValue($raw)
    {
        return $raw ? 2 : 0;
    }
}