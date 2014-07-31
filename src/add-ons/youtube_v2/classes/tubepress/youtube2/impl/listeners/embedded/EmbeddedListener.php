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
class tubepress_youtube2_impl_listeners_embedded_EmbeddedListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_platform_api_util_LangUtilsInterface
     */
    private $_langUtils;

    public function __construct(tubepress_app_api_options_ContextInterface     $context,
                                tubepress_platform_api_util_LangUtilsInterface $langUtils)
    {
        $this->_context   = $context;
        $this->_langUtils = $langUtils;
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

            'embedded/youtube',
            TUBEPRESS_ROOT . '/src/add-ons/youtube/resources/templates/embedded/youtube.tpl.php'
        );
    }

    /**
     * @param tubepress_platform_api_url_UrlFactoryInterface $urlFactory URL factory
     * @param string                                         $mediaId    The video ID to play
     *
     * @return tubepress_platform_api_url_UrlInterface The URL of the data for this video.
     *
     * @api
     * @since 4.0.0
     */
    public function getDataUrlForMediaItem(tubepress_platform_api_url_UrlFactoryInterface $urlFactory, $mediaId)
    {
        $link       = $urlFactory->fromString('https://www.youtube.com/embed/' . $mediaId);
        $embedQuery = $link->getQuery();
        $url        = $urlFactory->fromCurrent();
        $origin     = $url->getScheme() . '://' . $url->getHost();

        $autoPlay        = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY);
        $loop            = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_LOOP);
        $showInfo        = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO);
        $autoHide        = $this->_context->get(tubepress_youtube2_api_Constants::OPTION_AUTOHIDE);
        $fullscreen      = $this->_context->get(tubepress_youtube2_api_Constants::OPTION_FULLSCREEN);
        $modestBranding  = $this->_context->get(tubepress_youtube2_api_Constants::OPTION_MODEST_BRANDING);
        $showRelated     = $this->_context->get(tubepress_youtube2_api_Constants::OPTION_SHOW_RELATED);

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
            $embedQuery->set('playlist', $mediaId);
        }

        return $link;
    }

    /**
     * @return string The display name of this embedded player service.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'YouTube';
    }

    /**
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

            case tubepress_youtube2_api_Constants::AUTOHIDE_HIDE_BOTH:

                return 1;

            case tubepress_youtube2_api_Constants::AUTOHIDE_SHOW_BOTH:

                return 0;

            default:

                return 2;
        }
    }
}