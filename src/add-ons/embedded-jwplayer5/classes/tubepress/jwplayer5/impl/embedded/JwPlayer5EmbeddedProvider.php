<?php
/**
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

    /**
     * @var tubepress_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    public function __construct(tubepress_api_options_ContextInterface         $context,
                                tubepress_api_url_UrlFactoryInterface          $urlFactory,
                                tubepress_api_environment_EnvironmentInterface $environment,
                                tubepress_api_boot_BootSettingsInterface       $bootSettings)
    {
        $this->_context      = $context;
        $this->_urlFactory   = $urlFactory;
        $this->_environment  = $environment;
        $this->_bootSettings = $bootSettings;
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
     * @param tubepress_api_media_MediaItem $mediaItem
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateVariables(tubepress_api_media_MediaItem $mediaItem)
    {
        $dataUrl        = $this->_urlFactory->fromString(sprintf('https://www.youtube.com/watch?v=%s', $mediaItem->getId()));
        $userContentDir = $this->_bootSettings->getUserContentDirectory();
        $contentPath    = sprintf('%s/vendor/jwplayer5/player.swf', $userContentDir);

        if (is_file($contentPath)) {

            $userContentUrl = $this->_environment->getUserContentUrl();
            $playerSwfUrl   = sprintf('%s/vendor/jwplayer5/player.swf', $userContentUrl);

        } else {

            $baseUrl      = $this->_environment->getBaseUrl();
            $playerSwfUrl = sprintf('%s/src/add-ons/embedded-jwplayer5/web/player.swf', $baseUrl);
        }

        return array(

            'autostart'                                             => $this->_context->get(tubepress_api_options_Names::EMBEDDED_AUTOPLAY),
            tubepress_api_template_VariableNames::EMBEDDED_DATA_URL => $dataUrl,
            tubepress_jwplayer5_api_OptionNames::COLOR_FRONT        => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_FRONT),
            tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT        => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT),
            tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN       => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN),
            tubepress_jwplayer5_api_OptionNames::COLOR_BACK         => $this->_context->get(tubepress_jwplayer5_api_OptionNames::COLOR_BACK),
            'playerSwfUrl'                                          => $playerSwfUrl
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
            TUBEPRESS_ROOT . '/src/add-ons/embedded-jwplayer5/templates'
        );
    }
}
