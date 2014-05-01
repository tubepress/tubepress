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
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(
        tubepress_api_options_ContextInterface $context,
        tubepress_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_urlFactory = $urlFactory;
        $this->_context    = $context;
    }

    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'vimeo';
    }

    /**
     * @param tubepress_spi_theme_ThemeHandlerInterface $themeHandler The theme handler.
     *
     * @return ehough_contemplate_api_Template The template for this embedded player.
     */
    public final function getTemplate(tubepress_spi_theme_ThemeHandlerInterface $themeHandler)
    {
        return $themeHandler->getTemplateInstance('embedded/vimeo.tpl.php', TUBEPRESS_ROOT . '/src/main/php/add-ons/vimeo/resources/templates');
    }

    /**
     * @param string $videoId The video ID to play.
     *
     * @return tubepress_api_url_UrlInterface The URL of the data for this video.
     */
    public final function getDataUrlForVideo($videoId)
    {
        $autoPlay = $this->_context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $showInfo = $this->_context->get(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $loop     = $this->_context->get(tubepress_api_const_options_names_Embedded::LOOP);
        $jsApi    = $this->_context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $color    = $this->_context->get(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);

        /* build the data URL based on these options */
        $link  = $this->_urlFactory->fromString("http://player.vimeo.com/video/$videoId");
        $query = $link->getQuery();

        $query->set(self::$_URL_PARAM_AUTOPLAY, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));
        $query->set(self::$_URL_PARAM_COLOR,    $color);
        $query->set(self::$_URL_PARAM_LOOP,     tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $query->set(self::$_URL_PARAM_PORTRAIT, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_BYLINE,   tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_TITLE,    tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));

        if ($jsApi) {

            $query->set(self::$_URL_PARAM_JS_API, 1);
            $query->set(self::$_URL_PARAM_PLAYER_ID, 'tubepress-video-object-' . mt_rand());
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
