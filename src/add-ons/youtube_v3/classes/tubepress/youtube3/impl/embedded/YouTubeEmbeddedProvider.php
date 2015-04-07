<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_youtube3_impl_embedded_YouTubeEmbeddedProvider  implements tubepress_app_api_embedded_EmbeddedProviderInterface, tubepress_lib_api_template_PathProviderInterface
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_platform_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    public function __construct(tubepress_app_api_options_ContextInterface     $context,
                                tubepress_platform_api_util_LangUtilsInterface $langUtils,
                                tubepress_platform_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_context    = $context;
        $this->_langUtils  = $langUtils;
        $this->_urlFactory = $urlFactory;
    }

    /**
     * @return string[] The names of the media providers that this provider can handle.
     *
     * @api
     * @since 4.0.0
     */
    public function getCompatibleMediaProviderNames()
    {
        return array(
            'youtube',
        );
    }

    /**
     * @return string The name of this embedded provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'youtube';
    }

    /**
     * @return string The template name for this provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateName()
    {
        return 'single/embedded/youtube_iframe';
    }

    /**
     * @param tubepress_app_api_media_MediaItem $mediaItem
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateVariables(tubepress_app_api_media_MediaItem $mediaItem)
    {
        return array(

            tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL => $this->_getDataUrl($mediaItem),
        );
    }

    /**
     * @return string The display name of this embedded provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'YouTube';
    }

    /**
     * @return string[] A set of absolute filesystem directory paths
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateDirectories()
    {
        return array(

            TUBEPRESS_ROOT . '/src/add-ons/youtube_v3/templates'
        );
    }

    private function _getDataUrl(tubepress_app_api_media_MediaItem $mediaItem)
    {
        $link       = $this->_urlFactory->fromString('https://www.youtube.com/embed/' . $mediaItem->getId());
        $embedQuery = $link->getQuery();
        $url        = $this->_urlFactory->fromCurrent();
        $origin     = $url->getScheme() . '://' . $url->getHost();

        $autoPlay        = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY);
        $loop            = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_LOOP);
        $showInfo        = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO);
        $autoHide        = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_AUTOHIDE);
        $fullscreen      = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_FULLSCREEN);
        $modestBranding  = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_MODEST_BRANDING);
        $showRelated     = $this->_context->get(tubepress_youtube3_api_Constants::OPTION_SHOW_RELATED);

        $embedQuery->set('autohide',       $this->_getAutoHideValue($autoHide));
        $embedQuery->set('autoplay',       $this->_langUtils->booleanToStringOneOrZero($autoPlay));
        $embedQuery->set('enablejsapi',    '1');
        $embedQuery->set('fs',             $this->_langUtils->booleanToStringOneOrZero($fullscreen));
        $embedQuery->set('modestbranding', $this->_langUtils->booleanToStringOneOrZero($modestBranding));
        $embedQuery->set('origin',         $origin);
        $embedQuery->set('rel',            $this->_langUtils->booleanToStringOneOrZero($showRelated));
        $embedQuery->set('showinfo',       $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $embedQuery->set('wmode',          'opaque');

        if ($loop) {

            $embedQuery->set('loop', $this->_langUtils->booleanToStringOneOrZero($loop));
            $embedQuery->set('playlist', $mediaItem->getId());
        }

        return $link;
    }

    private function _getAutoHideValue($autoHide)
    {
        switch ($autoHide) {

            case tubepress_youtube3_api_Constants::AUTOHIDE_HIDE_BOTH:

                return 1;

            case tubepress_youtube3_api_Constants::AUTOHIDE_SHOW_BOTH:

                return 0;

            default:

                return 2;
        }
    }
}