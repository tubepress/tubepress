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
class tubepress_core_impl_embedded_EmbeddedHtml implements tubepress_core_api_embedded_EmbeddedHtmlInterface
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_api_provider_VideoProviderInterface[]
     */
    private $_videoProviders = array();

    /**
     * @var tubepress_core_api_embedded_EmbeddedProviderInterface[]
     */
    private $_embeddedPlayers = array();

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_core_api_template_TemplateFactoryInterface
     */
    private $_templateFactory;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(tubepress_api_log_LoggerInterface                    $logger,
                                tubepress_core_api_options_ContextInterface          $context,
                                tubepress_core_api_event_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_api_url_UrlFactoryInterface           $urlFactory,
                                tubepress_core_api_template_TemplateFactoryInterface $templateFactory)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_logger          = $logger;
        $this->_urlFactory      = $urlFactory;
        $this->_templateFactory = $templateFactory;
        $this->_shouldLog       = $logger->isEnabled();
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
        $videoProvider = $this->_findProviderThatRecognizesVideoId($videoId);

        /**
         * None of the registered video providers recognize this video ID. Nothing we can do about that. This
         * should basically never happen.
         */
        if ($videoProvider === null) {

            if ($this->_shouldLog) {

                $this->_logger->error('No video providers recognize video ' . $videoId);
            }

            return null;
        }

        $embeddedProvider = $this->_findEmbeddedProvider($videoProvider);

        if ($embeddedProvider === null) {

            if ($this->_shouldLog) {

                $this->_logger->error('Could not generate the embedded player HTML for ' . $videoId);
            }

            return null;
        }

        $templatePaths      = $embeddedProvider->getPathsForTemplateFactory();
        $template           = $this->_templateFactory->fromFilesystem($templatePaths);
        $dataUrl            = $embeddedProvider->getDataUrlForVideo($this->_urlFactory, $videoProvider, $videoId);
        $embeddedPlayerName = $embeddedProvider->getName();
        $providerName       = $videoProvider->getName();

        /**
         * Build the embedded template event.
         */
        $embeddedTemplateEvent = $this->_eventDispatcher->newEventInstance(

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
        $this->_eventDispatcher->dispatch(

            tubepress_core_api_const_event_EventNames::TEMPLATE_EMBEDDED,
            $embeddedTemplateEvent
        );

        /**
         * Pull the template out of the event.
         */
        $template = $embeddedTemplateEvent->getSubject();

        /**
         * Build the embedded HTML event.
         */
        $embeddedHtmlEvent = $this->_eventDispatcher->newEventInstance(

            $template->toString(),
            array(
                'videoId'                    => $videoId,
                'providerName'               => $providerName,
                'dataUrl'                    => $dataUrl,
                'embeddedImplementationName' => $embeddedPlayerName)
        );

        /**
         * Dispatch the embedded HTML event.
         */
        $this->_eventDispatcher->dispatch(

            tubepress_core_api_const_event_EventNames::HTML_EMBEDDED,
            $embeddedHtmlEvent
        );

        return $embeddedHtmlEvent->getSubject();
    }

    public function setVideoProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    public function setEmbeddedProviders(array $players)
    {
        $this->_embeddedPlayers = $players;
    }

    private function _findEmbeddedProvider(tubepress_core_api_provider_VideoProviderInterface $videoProvider)
    {
        $requestedEmbeddedPlayerName = $this->_context->get(tubepress_core_api_const_options_Names::PLAYER_IMPL);

        /**
         * The user has requested a specific embedded player that is registered. Let's see if the provider agrees.
         */
        if ($requestedEmbeddedPlayerName !== tubepress_core_api_const_options_ValidValues::EMBEDDED_IMPL_PROVIDER_BASED) {

            /**
             * @var $embeddedPlayer tubepress_core_api_embedded_EmbeddedProviderInterface
             */
            foreach ($this->_embeddedPlayers as $embeddedPlayer) {

                if ($embeddedPlayer->getName() !== $requestedEmbeddedPlayerName) {

                    continue;
                }

                if ($this->_compatible($embeddedPlayer, $videoProvider)) {

                    return $embeddedPlayer;
                }
            }
        }

        /**
         * Do we have an embedded provider whose name exactly matches the provider? If so, let's use that.
         */
        foreach ($this->_embeddedPlayers as $embeddedPlayer) {

            if ($embeddedPlayer->getName() === $videoProvider->getName()) {

                if ($this->_compatible($embeddedPlayer, $videoProvider)) {

                    return $embeddedPlayer;
                }
            }
        }

        /**
         * Running out of options. See if we can find *any* player that can handle videos from this provider.
         */
        foreach ($this->_embeddedPlayers as $embeddedPlayer) {

            if ($this->_compatible($embeddedPlayer, $videoProvider)) {

                return $embeddedPlayer;
            }
        }

        /**
         * None of the registered embedded players support the calculated provider. I give up.
         */
        return null;
    }

    private function _compatible(tubepress_core_api_embedded_EmbeddedProviderInterface $embeddedProvider,
                                 tubepress_core_api_provider_VideoProviderInterface    $videoProvider)
    {
        $compatibleProviderNames = $embeddedProvider->getCompatibleProviderNames();

        return in_array($videoProvider->getName(), $compatibleProviderNames);
    }

    private function _findProviderThatRecognizesVideoId($videoId)
    {
        /**
         * @var $videoProvider tubepress_core_api_provider_VideoProviderInterface
         */
        foreach ($this->_videoProviders as $videoProvider) {

            if ($videoProvider->recognizesVideoId($videoId)) {

                return $videoProvider;
            }
        }

        return null;
    }
}
