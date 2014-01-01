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
 * Handles the heavy lifting for YouTube.
 */
class tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService implements tubepress_spi_embedded_PluggableEmbeddedPlayerService
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
        return $themeHandler->getTemplateInstance('embedded/youtube.tpl.php', TUBEPRESS_ROOT . '/src/main/php/add-ons/youtube/resources/templates');
    }

    /**
     * @param string $videoId The video ID to play.
     *
     * @return ehough_curly_Url The URL of the data for this video.
     */
    public final function getDataUrlForVideo($videoId)
    {
        $context = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $link    = new ehough_curly_Url('https://www.youtube.com/embed/' . $videoId);
        $qss     = tubepress_impl_patterns_sl_ServiceLocator::getQueryStringService();
        $url     = new ehough_curly_Url($qss->getFullUrl($_SERVER));
        $origin  = $url->getScheme() . '://' . $url->getHost();


        $autoPlay        = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $loop            = $context->get(tubepress_api_const_options_names_Embedded::LOOP);
        $showInfo        = $context->get(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $autoHide        = $context->get(tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE);
        $enableJsApi     = $context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $fullscreen      = $context->get(tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN);
        $modestBranding  = $context->get(tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING);
        $showRelated     = $context->get(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED);

        $link->setQueryVariable('wmode', 'opaque');
        $link->setQueryVariable('autohide', $this->_getAutoHideValue($autoHide));
        $link->setQueryVariable('autoplay', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));

        $link->setQueryVariable('enablejsapi', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($enableJsApi));
        $link->setQueryVariable('fs', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($fullscreen));
        $link->setQueryVariable('loop', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $link->setQueryVariable('modestbranding', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($modestBranding));
        $link->setQueryVariable('rel', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showRelated));
        $link->setQueryVariable('showinfo', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable('origin', $origin);

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

    private function _getAutoHideValue($autoHide)
    {
        switch ($autoHide) {

            case tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_HIDE_BOTH:

                return 1;

            case tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_SHOW_BOTH:

                return 0;

            default:

                return 2;
        }
    }
}