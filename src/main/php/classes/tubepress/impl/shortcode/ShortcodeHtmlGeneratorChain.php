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
 * HTML handler implementation.
 */
class tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain implements tubepress_spi_shortcode_ShortcodeHtmlGenerator
{
    /**
     * Generated HTML chain key.
     */
    const CHAIN_KEY_GENERATED_HTML = 'generatedHtml';

    private $_chain;

    private $_logger;

    public function __construct(ehough_chaingang_api_Chain $chain)
    {
        $this->_chain  = $chain;
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Shortcode HTML Generator Chain');
    }

    /**
     * Generates the HTML for TubePress. Could be a gallery or single video.
     *
     * @param string $shortCodeContent The shortcode content.
     *
     * @return string The HTML for the given shortcode, or the error message if there was a problem.
     */
    public function getHtmlForShortcode($shortCodeContent)
    {
        /* parse the shortcode if we need to */
        if ($shortCodeContent != '') {

            $shortcodeParser = tubepress_impl_patterns_ioc_KernelServiceLocator::getShortcodeParser();
            $shortcodeParser->parse($shortCodeContent);
        }

        /* use the chain to get the HTML */
        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug('Running the shortcode HTML chain');
        }

        $rawHtml = $this->_runChain();

        $eventDispatcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        $event = new tubepress_api_event_TubePressEvent($rawHtml);

        /* send it through the filters */
        if ($eventDispatcher->hasListeners(tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION)) {

            $eventDispatcher->dispatch(

                tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION,
                $event
            );
        }

        return $event->getSubject();
    }

    private function _runChain()
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $status  = $this->_chain->execute($context);

        if ($status === false) {

            throw new RuntimeException('No commands could generate the shortcode HTML.');
        }

        return $context->get(self::CHAIN_KEY_GENERATED_HTML);
    }
}
