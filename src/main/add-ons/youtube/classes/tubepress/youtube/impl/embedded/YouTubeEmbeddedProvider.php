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
 *
 */
class tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider implements tubepress_core_embedded_api_EmbeddedProviderInterface
{
    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_util_LangUtilsInterface
     */
    private $_langUtils;

    public function __construct(tubepress_core_options_api_ContextInterface $context,
                                tubepress_api_util_LangUtilsInterface       $langUtils)
    {
        $this->_context   = $context;
        $this->_langUtils = $langUtils;
    }

    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'youtube';
    }

    /**
     * @return string[] The paths, to pass to the template factory, for this embedded provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getPathsForTemplateFactory()
    {
        return array(

            'embedded/youtube.tpl.php',
            TUBEPRESS_ROOT . '/src/main/add-ons/youtube/resources/templates/embedded/youtube.tpl.php'
        );
    }

    /**
     * @param tubepress_core_url_api_UrlFactoryInterface         $urlFactory URL factory
     * @param tubepress_core_media_provider_api_MediaProviderInterface $provider   The video provider
     * @param string                                             $videoId    The video ID to play
     *
     * @return tubepress_core_url_api_UrlInterface The URL of the data for this video.
     *
     * @api
     * @since 4.0.0
     */
    public function getDataUrlForVideo(tubepress_core_url_api_UrlFactoryInterface $urlFactory,
                                tubepress_core_media_provider_api_MediaProviderInterface $provider,
                                $videoId)
    {
        $link       = $urlFactory->fromString('https://www.youtube.com/embed/' . $videoId);
        $embedQuery = $link->getQuery();
        $url        = $urlFactory->fromCurrent();
        $origin     = $url->getScheme() . '://' . $url->getHost();

        $autoPlay        = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_AUTOPLAY);
        $loop            = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_LOOP);
        $showInfo        = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_SHOW_INFO);
        $autoHide        = $this->_context->get(tubepress_youtube_api_Constants::OPTION_AUTOHIDE);
        $enableJsApi     = $this->_context->get(tubepress_core_html_api_Constants::OPTION_ENABLE_JS_API);
        $fullscreen      = $this->_context->get(tubepress_youtube_api_Constants::OPTION_FULLSCREEN);
        $modestBranding  = $this->_context->get(tubepress_youtube_api_Constants::OPTION_MODEST_BRANDING);
        $showRelated     = $this->_context->get(tubepress_youtube_api_Constants::OPTION_SHOW_RELATED);

        $embedQuery->set('autohide',       $this->_getAutoHideValue($autoHide));
        $embedQuery->set('autoplay',       $this->_langUtils->booleanToStringOneOrZero($autoPlay));
        $embedQuery->set('enablejsapi',    $this->_langUtils->booleanToStringOneOrZero($enableJsApi));
        $embedQuery->set('fs',             $this->_langUtils->booleanToStringOneOrZero($fullscreen));
        $embedQuery->set('loop',           $this->_langUtils->booleanToStringOneOrZero($loop));
        $embedQuery->set('modestbranding', $this->_langUtils->booleanToStringOneOrZero($modestBranding));
        $embedQuery->set('origin',         $origin);
        $embedQuery->set('rel',            $this->_langUtils->booleanToStringOneOrZero($showRelated));
        $embedQuery->set('showinfo',       $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $embedQuery->set('wmode',          'opaque');

        return $link;
    }

    /**
     * @return string The friendly name of this embedded player service.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'YouTube';
    }

    /**
     * @param tubepress_core_media_provider_api_MediaProviderInterface
     *
     * @return string[] An array of provider names that this embedded provider can handle.
     *
     * @api
     * @since 4.0.0
     */
    public function getCompatibleProviderNames()
    {
        return array('youtube');
    }

    private function _getAutoHideValue($autoHide)
    {
        switch ($autoHide) {

            case tubepress_youtube_api_Constants::AUTOHIDE_HIDE_BOTH:

                return 1;

            case tubepress_youtube_api_Constants::AUTOHIDE_SHOW_BOTH:

                return 0;

            default:

                return 2;
        }
    }
}