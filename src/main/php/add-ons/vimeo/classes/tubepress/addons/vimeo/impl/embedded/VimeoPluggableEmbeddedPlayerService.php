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
 * Embedded player command for native Vimeo
 */
class tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService implements tubepress_spi_embedded_PluggableEmbeddedPlayerService
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
        return $themeHandler->getTemplateInstance('embedded/vimeo.tpl.php', TUBEPRESS_ROOT . '/src/main/php/add-ons/vimeo/resources/templates');
    }

    /**
     * @param string $videoId The video ID to play.
     *
     * @return ehough_curly_Url The URL of the data for this video.
     */
    public final function getDataUrlForVideo($videoId)
    {
        $context = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();

        $autoPlay = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $showInfo = $context->get(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $loop     = $context->get(tubepress_api_const_options_names_Embedded::LOOP);
        $jsApi    = $context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);

        $color    = $context->get(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);

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
            $link->setQueryVariable(self::$_URL_PARAM_PLAYER_ID, 'tubepress-video-object-' . mt_rand());
        }

        return $link;
    }

    /**
     * @return string The name of the video provider whose videos this player handles.
     */
    public final function getHandledProviderName()
    {
        return 'vimeo';
    }

    /**
     * @return string The friendly name of this embedded player service.
     */
    public final function getFriendlyName()
    {
        return 'Vimeo';
    }
}
