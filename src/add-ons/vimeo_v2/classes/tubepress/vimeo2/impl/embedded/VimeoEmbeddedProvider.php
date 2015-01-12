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
 * Embedded player command for native Vimeo
 */
class tubepress_vimeo2_impl_embedded_VimeoEmbeddedProvider implements tubepress_app_api_embedded_EmbeddedProviderInterface, tubepress_lib_api_template_PathProviderInterface
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
            'vimeo',
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
        return 'vimeo';
    }

    /**
     * @return string The template name for this provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateName()
    {
        return 'single/embedded/vimeo_v2';
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
        return 'Vimeo';
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

            TUBEPRESS_ROOT . '/src/add-ons/vimeo_v2/templates'
        );
    }

    private function _getDataUrl(tubepress_app_api_media_MediaItem $mediaItem)
    {
        $autoPlay = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY);
        $showInfo = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO);
        $loop     = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_LOOP);
        $color    = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR);

        /* build the data URL based on these options */
        $link  = $this->_urlFactory->fromString('https://player.vimeo.com/video/' . $mediaItem->getId());
        $query = $link->getQuery();

        $query->set(self::$_URL_PARAM_AUTOPLAY, $this->_langUtils->booleanToStringOneOrZero($autoPlay));
        $query->set(self::$_URL_PARAM_COLOR,    $color);
        $query->set(self::$_URL_PARAM_LOOP,     $this->_langUtils->booleanToStringOneOrZero($loop));
        $query->set(self::$_URL_PARAM_PORTRAIT, $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_BYLINE,   $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_TITLE,    $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_JS_API, 1);

        return $link;
    }
}
