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
class tubepress_impl_embedded_EmbeddedPlayerChain implements tubepress_spi_embedded_EmbeddedHtmlGenerator
{
    /**
     * Provider name key for the chain.
     */
    const CHAIN_KEY_PROVIDER_NAME = 'providerName';

    /**
     * Video ID key for the chain.
     */
    const CHAIN_KEY_VIDEO_ID = 'videoId';

    /**
     * Template key for the chain.
     */
    const CHAIN_KEY_TEMPLATE = 'template';

    /**
     * Data URL key for the chain.
     */
    const CHAIN_KEY_DATA_URL = 'dataUrl';

    /**
     * Implementation name key for the chain.
     */
    const CHAIN_KEY_IMPLEMENTATION_NAME = 'implementationName';

    /** @var array An array of commands. */
    private $_commands;

    /** @var ehough_chaingang_api_Chain */
    private $_chain;

    public function __construct(ehough_chaingang_api_Chain $chain)
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Embedded HTML Chain');
        $this->_chain  = $chain;
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
        $providerCalculatorService = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProviderCalculator();

        $providerName = $providerCalculatorService->calculateProviderOfVideoId($videoId);
        $context      = new ehough_chaingang_impl_StandardContext();

        $context->put(self::CHAIN_KEY_PROVIDER_NAME, $providerName);
        $context->put(self::CHAIN_KEY_VIDEO_ID, $videoId);

        /**
         * Let the commands do the heavy lifting.
         */
        $status = $this->_chain->execute($context);

        /**
         * If nobody can handle it, there's really nothing else to do but bail.
         */
        if ($status === false) {

            $this->_logger->warn('No commands could handle the embedded HTML generation for ' . $videoId);

            return null;
        }

        /**
         * Pull out the relevant stuff from the context.
         */
        $template = $context->get(self::CHAIN_KEY_TEMPLATE);
        $dataUrl  = $context->get(self::CHAIN_KEY_DATA_URL);
        $implName = $context->get(self::CHAIN_KEY_IMPLEMENTATION_NAME);

        $eventDispatcherService = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        /**
         * Build the embedded template event.
         */
        $embeddedTemplateEvent = new tubepress_api_event_EmbeddedTemplateConstruction(

            $template,
            array(
                tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_VIDEO_ID                     => $videoId,
                tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_PROVIDER_NAME                => $providerName,
                tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_DATA_URL                     => $dataUrl,
                tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_EMBEDDED_IMPLEMENTATION_NAME => $implName)
        );

        /**
         * Dispatch the embedded template event.
         */
        $eventDispatcherService->dispatch(

            tubepress_api_event_EmbeddedTemplateConstruction::EVENT_NAME,
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
                'embeddedImplementationName' => $implName)
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
}
