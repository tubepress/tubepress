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
 * Generates HTML for the embedded media player.
 */
class tubepress_core_embedded_impl_EmbeddedHtml implements tubepress_core_embedded_api_EmbeddedHtmlInterface
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders = array();

    /**
     * @var tubepress_core_embedded_api_EmbeddedProviderInterface[]
     */
    private $_embeddedProviders = array();

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_url_api_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_core_template_api_TemplateFactoryInterface
     */
    private $_templateFactory;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(tubepress_api_log_LoggerInterface                    $logger,
                                tubepress_core_options_api_ContextInterface          $context,
                                tubepress_core_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_url_api_UrlFactoryInterface           $urlFactory,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory)
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
     * @param string $mediaId The item ID to display
     *
     * @return string The text for this embedded player, or null if there was a problem.
     */
    public function getHtml($mediaId)
    {
        $mediaProvider = $this->_findMediaProviderForItemId($mediaId);

        /**
         * None of the registered media providers recognize this item ID. Nothing we can do about that. This
         * should basically never happen.
         */
        if ($mediaProvider === null) {

            if ($this->_shouldLog) {

                $this->_logger->error('No media providers recognize item with ID ' . $mediaId);
            }

            return null;
        }

        $embeddedProvider = $this->_findEmbeddedProvider($mediaProvider);

        if ($embeddedProvider === null) {

            if ($this->_shouldLog) {

                $this->_logger->error('Could not generate the embedded player HTML for ' . $mediaId);
            }

            return null;
        }

        $templatePaths = $embeddedProvider->getPathsForTemplateFactory();
        $template      = $this->_templateFactory->fromFilesystem($templatePaths);
        $dataUrl       = $embeddedProvider->getDataUrlForVideo($this->_urlFactory, $mediaProvider, $mediaId);

        $template = $this->_fireEventAndReturnSubject($template,
            $mediaId, $mediaProvider, $dataUrl, $embeddedProvider, tubepress_core_embedded_api_Constants::EVENT_TEMPLATE_EMBEDDED);

        return $this->_fireEventAndReturnSubject($template->toString(),
            $mediaId, $mediaProvider, $dataUrl, $embeddedProvider, tubepress_core_embedded_api_Constants::EVENT_HTML_EMBEDDED);
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    public function setEmbeddedProviders(array $players)
    {
        $this->_embeddedProviders = $players;
    }

    private function _findEmbeddedProvider(tubepress_core_media_provider_api_MediaProviderInterface $mediaProvider)
    {
        $requestedEmbeddedPlayerName = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_PLAYER_IMPL);

        /**
         * The user has requested a specific embedded player that is registered. Let's see if the provider agrees.
         */
        if ($requestedEmbeddedPlayerName !== tubepress_core_embedded_api_Constants::EMBEDDED_IMPL_PROVIDER_BASED) {

            /**
             * @var $embeddedPlayer tubepress_core_embedded_api_EmbeddedProviderInterface
             */
            foreach ($this->_embeddedProviders as $embeddedPlayer) {

                if ($embeddedPlayer->getName() !== $requestedEmbeddedPlayerName) {

                    continue;
                }

                if ($this->_compatible($embeddedPlayer, $mediaProvider)) {

                    return $embeddedPlayer;
                }
            }
        }

        /**
         * Do we have an embedded provider whose name exactly matches the provider? If so, let's use that.
         */
        foreach ($this->_embeddedProviders as $embeddedPlayer) {

            if ($embeddedPlayer->getName() === $mediaProvider->getName()) {

                if ($this->_compatible($embeddedPlayer, $mediaProvider)) {

                    return $embeddedPlayer;
                }
            }
        }

        /**
         * Running out of options. See if we can find *any* player that can handle items from this provider.
         */
        foreach ($this->_embeddedProviders as $embeddedPlayer) {

            if ($this->_compatible($embeddedPlayer, $mediaProvider)) {

                return $embeddedPlayer;
            }
        }

        /**
         * None of the registered embedded players support the calculated provider. I give up.
         */
        return null;
    }

    private function _compatible(tubepress_core_embedded_api_EmbeddedProviderInterface $embeddedProvider,
                                 tubepress_core_media_provider_api_MediaProviderInterface    $mediaProvider)
    {
        $compatibleProviderNames = $embeddedProvider->getCompatibleProviderNames();

        return in_array($mediaProvider->getName(), $compatibleProviderNames);
    }

    private function _findMediaProviderForItemId($itemId)
    {
        /**
         * @var $mediaProvider tubepress_core_media_provider_api_MediaProviderInterface
         */
        foreach ($this->_mediaProviders as $mediaProvider) {

            if ($mediaProvider->recognizesItemId($itemId)) {

                return $mediaProvider;
            }
        }

        return null;
    }

    private function _fireEventAndReturnSubject($subject,
                                                $mediaId,
                                                tubepress_core_media_provider_api_MediaProviderInterface $mediaProvider,
                                                tubepress_core_url_api_UrlInterface $url,
                                                tubepress_core_embedded_api_EmbeddedProviderInterface $embeddedProvider,
                                                $eventName)
    {
        $event = $this->_eventDispatcher->newEventInstance(
            $subject,
            array(
                'itemId'           => $mediaId,
                'itemProvider'     => $mediaProvider,
                'dataUrl'          => $url,
                'embeddedProvider' => $embeddedProvider
            )
        );

        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }
}