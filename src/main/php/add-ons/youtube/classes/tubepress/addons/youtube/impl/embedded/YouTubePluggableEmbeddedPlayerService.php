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
     * @var tubepress_api_url_CurrentUrlServiceInterface
     */
    private $_currentUrlService;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    public function __construct(tubepress_api_url_CurrentUrlServiceInterface $currentUrlService,
        tubepress_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_currentUrlService = $currentUrlService;
        $this->_urlFactory        = $urlFactory;
    }

    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'youtube';
    }

    /**
     * @param tubepress_spi_theme_ThemeHandlerInterface $themeHandler The theme handler.
     *
     * @return ehough_contemplate_api_Template The template for this embedded player.
     */
    public final function getTemplate(tubepress_spi_theme_ThemeHandlerInterface $themeHandler)
    {
        return $themeHandler->getTemplateInstance('embedded/youtube.tpl.php', TUBEPRESS_ROOT . '/src/main/php/add-ons/youtube/resources/templates');
    }

    /**
     * @param string $videoId The video ID to play.
     *
     * @return tubepress_api_url_UrlInterface The URL of the data for this video.
     */
    public final function getDataUrlForVideo($videoId)
    {
        $context    = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $link       = $this->_urlFactory->fromString('https://www.youtube.com/embed/' . $videoId);
        $embedQuery = $link->getQuery();
        $url        = $this->_currentUrlService->getUrl();
        $origin     = $url->getScheme() . '://' . $url->getHost();


        $autoPlay        = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $loop            = $context->get(tubepress_api_const_options_names_Embedded::LOOP);
        $showInfo        = $context->get(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $autoHide        = $context->get(tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE);
        $enableJsApi     = $context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $fullscreen      = $context->get(tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN);
        $modestBranding  = $context->get(tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING);
        $showRelated     = $context->get(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED);

        $embedQuery->set('autohide',       $this->_getAutoHideValue($autoHide));
        $embedQuery->set('autoplay',       tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));
        $embedQuery->set('enablejsapi',    tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($enableJsApi));
        $embedQuery->set('fs',             tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($fullscreen));
        $embedQuery->set('loop',           tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $embedQuery->set('modestbranding', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($modestBranding));
        $embedQuery->set('origin',         $origin);
        $embedQuery->set('rel',            tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showRelated));
        $embedQuery->set('showinfo',       tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $embedQuery->set('wmode',          'opaque');

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