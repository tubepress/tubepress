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
 * Generates HTML for the embedded video player.
 */
class tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator implements tubepress_spi_embedded_EmbeddedHtmlGenerator
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var tubepress_spi_provider_PluggableVideoProviderService[]
     */
    private $_videoProviders = array();

    /**
     * @var tubepress_spi_embedded_PluggableEmbeddedPlayerService[]
     */
    private $_embeddedPlayers = array();

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Embedded Player HTML Generator');
    }

    /**
     * Spits back the text for this embedded player
     *
     * @param string $videoId The video ID to display
     *
     * @return string The text for this embedded player, or null if there was a problem.
     */
    public final function getHtml($videoId)
    {
        $embeddedPlayer = $this->_getEmbeddedPlayer($videoId);

        if ($embeddedPlayer === null) {

            if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

                $this->_logger->warn('Could not generate the embedded player HTML for ' . $videoId);
            }

            return null;
        }

        $themeHandler           = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $eventDispatcherService = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $template = $embeddedPlayer->getTemplate($themeHandler);

        $dataUrl            = $embeddedPlayer->getDataUrlForVideo($videoId);
        $embeddedPlayerName = $embeddedPlayer->getName();
        $providerName       = $embeddedPlayer->getHandledProviderName();

        /**
         * Build the embedded template event.
         */
        $embeddedTemplateEvent = new tubepress_spi_event_EventBase(

            $template,
            array(
                'videoId'                    => $videoId,
                'providerName'               => $providerName,
                'dataUrl'                    => $dataUrl,
                'embeddedImplementationName' => $embeddedPlayerName)
        );

        /**
         * Dispatch the embedded template event.
         */
        $eventDispatcherService->dispatch(

            tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED,
            $embeddedTemplateEvent
        );

        /**
         * Pull the template out of the event.
         */
        $template = $embeddedTemplateEvent->getSubject();

        /**
         * Build the embedded HTML event.
         */
        $embeddedHtmlEvent = new tubepress_spi_event_EventBase(

            $template->toString(),
            array(
                'videoId'                    => $videoId,
                'providerName'               => $providerName,
                'dataUrl'                    => $dataUrl,
                'embeddedImplementationName' => $embeddedPlayerName)
        );

        /**
         * Dispatche the embedded HTML event.
         */
        $eventDispatcherService->dispatch(

            tubepress_api_const_event_EventNames::HTML_EMBEDDED,
            $embeddedHtmlEvent
        );

        return $embeddedHtmlEvent->getSubject();
    }

    public function setPluggableVideoProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    public function setPluggableEmbeddedPlayers(array $players)
    {
        $this->_embeddedPlayers = $players;
    }

    /**
     * @param $videoId
     *
     * @return tubepress_spi_embedded_PluggableEmbeddedPlayerService
     */
    private function _getEmbeddedPlayer($videoId)
    {
        $executionContext            = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $requestedEmbeddedPlayerName = $executionContext->get(tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $recognizingProvider         = $this->_findProviderThatRecognizesVideoId($videoId);

        /**
         * The user has requested a specific embedded player that is registered. Let's see if the provider agrees.
         */
        if ($requestedEmbeddedPlayerName !== tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED) {

            /**
             * @var $embeddedPlayer tubepress_spi_embedded_PluggableEmbeddedPlayerService
             */
            foreach ($this->_embeddedPlayers as $embeddedPlayer) {

                if ($embeddedPlayer->getName() === $requestedEmbeddedPlayerName && $recognizingProvider !== null
                    && $recognizingProvider->getName() === $embeddedPlayer->getHandledProviderName()) {

                    //found it!
                    return $embeddedPlayer;
                }
            }
        }

        /**
         * None of the registered video providers recognize this video ID. Nothing we can do about that. This
         * should basically never happen.
         */
        if ($recognizingProvider === null) {

            return null;
        }

        /**
         * Do we have an embedded provider whose name exactly matches the provider? If so, let's use that. This
         * should be the common case.
         */
        foreach ($this->_embeddedPlayers as $embeddedPlayer) {

            if ($embeddedPlayer->getName() === $recognizingProvider->getName()) {

                 return $embeddedPlayer;
            }
        }

        /**
         * Running out of options. See if we can find *any* player that can handle videos from this provider.
         */
        foreach ($this->_embeddedPlayers as $embeddedPlayer) {

            if ($embeddedPlayer->getHandledProviderName() === $recognizingProvider->getName()) {

                return $embeddedPlayer;
            }
        }

        /**
         * None of the registered embedded players support the calculated provider. I give up.
         */
        return null;
    }

    private function _findProviderThatRecognizesVideoId($videoId)
    {
        /**
         * @var $videoProvider tubepress_spi_provider_PluggableVideoProviderService
         */
        foreach ($this->_videoProviders as $videoProvider) {

            if ($videoProvider->recognizesVideoId($videoId)) {

                return $videoProvider;
            }
        }

        return null;
    }
}
