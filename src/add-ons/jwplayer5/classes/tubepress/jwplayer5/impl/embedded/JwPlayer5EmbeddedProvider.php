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
 * Plays videos with JW Player.
 */
class tubepress_jwplayer5_impl_embedded_JwPlayer5EmbeddedProvider implements tubepress_app_api_embedded_EmbeddedProviderInterface, tubepress_lib_api_template_PathProviderInterface
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_app_api_environment_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_app_api_options_ContextInterface         $context,
                                tubepress_platform_api_url_UrlFactoryInterface     $urlFactory,
                                tubepress_app_api_environment_EnvironmentInterface $environment)
    {
        $this->_context     = $context;
        $this->_urlFactory  = $urlFactory;
        $this->_environment = $environment;
    }

    /**
     * @return string The display name of this embedded player service.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'JW Player (by Longtail Video)';  //>(translatable)<
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
        return 'longtail';
    }

    /**
     * @return string The template name for this provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateName()
    {
        return 'single/embedded/jwplayer5';
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
        $dataUrl = $this->_urlFactory->fromString(sprintf('http://www.youtube.com/watch?v=%s', $mediaItem->getId()));

        return array(

            'tubePressBaseUrl'                                          => $this->_environment->getBaseUrl(),
            'autostart'                                                 => $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY),
            tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL => $dataUrl,
            tubepress_jwplayer5_api_OptionNames::COLOR_FRONT  => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_FRONT),
            tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT  => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT),
            tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN),
            tubepress_jwplayer5_api_OptionNames::COLOR_BACK   => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_BACK),
        );
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
            TUBEPRESS_ROOT . '/src/add-ons/jwplayer5/templates'
        );
    }
}
