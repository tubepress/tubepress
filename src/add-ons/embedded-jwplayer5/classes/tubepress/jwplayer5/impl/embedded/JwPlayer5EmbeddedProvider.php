<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_jwplayer5_impl_embedded_JwPlayer5EmbeddedProvider implements tubepress_spi_embedded_EmbeddedProviderInterface, tubepress_spi_template_PathProviderInterface
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_api_options_ContextInterface         $context,
                                tubepress_api_url_UrlFactoryInterface          $urlFactory,
                                tubepress_api_environment_EnvironmentInterface $environment)
    {
        $this->_context     = $context;
        $this->_urlFactory  = $urlFactory;
        $this->_environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDisplayName()
    {
        return 'JW Player (by Longtail Video)';  //>(translatable)<
    }

    /**
     * {@inheritdoc}
     */
    public function getCompatibleMediaProviderNames()
    {
        return array(
            'youtube',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'longtail';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'single/embedded/jwplayer5';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateVariables(tubepress_api_media_MediaItem $mediaItem)
    {
        $dataUrl = $this->_urlFactory->fromString(sprintf('https://www.youtube.com/watch?v=%s', $mediaItem->getId()));

        return array(

            'userContentUrl'                                        => $this->_environment->getUserContentUrl(),
            'autostart'                                             => $this->_context->get(tubepress_api_options_Names::EMBEDDED_AUTOPLAY),
            tubepress_api_template_VariableNames::EMBEDDED_DATA_URL => $dataUrl,
            tubepress_jwplayer5_api_OptionNames::COLOR_FRONT        => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_FRONT),
            tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT        => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT),
            tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN       => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN),
            tubepress_jwplayer5_api_OptionNames::COLOR_BACK         => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_BACK),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateDirectories()
    {
        return array(
            TUBEPRESS_ROOT . '/src/add-ons/embedded-jwplayer5/templates',
        );
    }
}
