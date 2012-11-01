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
 * Generates HTML from some shortcode.
 */
class tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator implements tubepress_spi_shortcode_ShortcodeHtmlGenerator
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Default Shortcode HTML Generator');
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

        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();
        $handlers                   = $serviceCollectionsRegistry->getAllServicesOfType(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);
        $html                       = null;

        foreach ($handlers as $handler) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($handler->shouldExecute()) {

                $html = $handler->getHtml();
                break;
            }
        }

        if ($html === null) {

            throw new RuntimeException('No shortcode handlers could generate HTML');
        }

        $eventDispatcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        $event = new tubepress_api_event_TubePressEvent($html);

        /* send it through the filters */
        if ($eventDispatcher->hasListeners(tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION)) {

            $eventDispatcher->dispatch(

                tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION,
                $event
            );
        }

        return $event->getSubject();
    }
}
