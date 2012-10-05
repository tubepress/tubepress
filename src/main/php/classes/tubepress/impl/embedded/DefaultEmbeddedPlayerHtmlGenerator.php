<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * An HTML-embeddable video player.
 */
class tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator implements tubepress_spi_embedded_EmbeddedHtmlGenerator
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Default Embedded Player HTML Generator');
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

            $this->_logger->warn('Could not generate the embedded player HTML for ' . $videoId);

            return null;
        }

        $themeHandler           = tubepress_impl_patterns_ioc_KernelServiceLocator::getThemeHandler();
        $eventDispatcherService = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        $template = $embeddedPlayer->getTemplate($themeHandler);

        $dataUrl            = $embeddedPlayer->getDataUrlForVideo($videoId);
        $embeddedPlayerName = $embeddedPlayer->getName();
        $providerName       = $embeddedPlayer->getHandledProviderName();

        /**
         * Build the embedded template event.
         */
        $embeddedTemplateEvent = new tubepress_api_event_TubePressEvent(

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

            tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION,
            $embeddedTemplateEvent
        );

        /**
         * Pull the template out of the event.
         */
        $template = $embeddedTemplateEvent->getSubject();

        /**
         * Build the embedded HTML event.
         */
        $embeddedHtmlEvent = new tubepress_api_event_TubePressEvent(

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

            tubepress_api_const_event_CoreEventNames::EMBEDDED_HTML_CONSTRUCTION,
            $embeddedHtmlEvent
        );

        return $embeddedHtmlEvent->getSubject();
    }

    /**
     * @param $videoId
     *
     * @return tubepress_spi_embedded_PluggableEmbeddedPlayer
     */
    private function _getEmbeddedPlayer($videoId)
    {
        $executionContext           = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        $requestedEmbeddedPlayerName = $executionContext->get(tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $embeddedPlayers             = $serviceCollectionsRegistry->getAllServicesOfType(tubepress_spi_embedded_PluggableEmbeddedPlayer::_);

        if ($requestedEmbeddedPlayerName === tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED) {

            $calculatedProviderName = null;
            $videoProviders         = $serviceCollectionsRegistry->getAllServicesOfType(tubepress_spi_provider_VideoProvider::_);

            foreach ($videoProviders as $videoProvider) {

                if ($videoProvider->recognizesVideoId($videoId)) {

                    $calculatedProviderName = $videoProvider->getName();
                    break;
                }
            }

            /**
             * None of the registered video providers recognize this video ID.
             */
            if ($calculatedProviderName === null) {

                return null;
            }

            foreach ($embeddedPlayers as $embeddedPlayer) {

                if ($embeddedPlayer->getHandledProviderName() === $calculatedProviderName) {

                    return $embeddedPlayer;
                }
            }

            /**
             * None of the registered embedded players support the calculated provider.
             */
            return null;
        }

        foreach ($embeddedPlayers as $embeddedPlayer) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($embeddedPlayer->getName() === $requestedEmbeddedPlayerName) {

                return $embeddedPlayer;
            }
        }

        /**
         * No matching embedded player.
         */
        return null;
    }
}
