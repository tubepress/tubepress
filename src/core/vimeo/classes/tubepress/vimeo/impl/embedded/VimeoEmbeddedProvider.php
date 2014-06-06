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
class tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider implements tubepress_core_embedded_api_EmbeddedProviderInterface
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
    public final function getName()
    {
        return 'vimeo';
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

            'embedded/vimeo.tpl.php',
            TUBEPRESS_ROOT . '/src/core/vimeo/resources/templates'
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
    public function getDataUrlForVideo(tubepress_core_url_api_UrlFactoryInterface         $urlFactory,
                                       tubepress_core_media_provider_api_MediaProviderInterface $provider,
                                       $videoId)
    {
        $autoPlay = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_AUTOPLAY);
        $showInfo = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_SHOW_INFO);
        $loop     = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_LOOP);
        $jsApi    = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_ENABLE_JS_API);
        $color    = $this->_context->get(tubepress_vimeo_api_Constants::OPTION_PLAYER_COLOR);

        /* build the data URL based on these options */
        $link  = $urlFactory->fromString("http://player.vimeo.com/video/$videoId");
        $query = $link->getQuery();

        $query->set(self::$_URL_PARAM_AUTOPLAY, $this->_langUtils->booleanToStringOneOrZero($autoPlay));
        $query->set(self::$_URL_PARAM_COLOR,    $color);
        $query->set(self::$_URL_PARAM_LOOP,     $this->_langUtils->booleanToStringOneOrZero($loop));
        $query->set(self::$_URL_PARAM_PORTRAIT, $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_BYLINE,   $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_TITLE,    $this->_langUtils->booleanToStringOneOrZero($showInfo));

        if ($jsApi) {

            $query->set(self::$_URL_PARAM_JS_API, 1);
            $query->set(self::$_URL_PARAM_PLAYER_ID, 'tubepress-video-object-' . mt_rand());
        }

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
        return 'Vimeo';
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
        return array('vimeo');
    }
}
