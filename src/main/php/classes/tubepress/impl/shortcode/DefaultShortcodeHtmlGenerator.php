<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
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
     * @throws RuntimeException If no handlers could generate the proper HTML.
     *
     * @return string The HTML for the given shortcode, or the error message if there was a problem.
     */
    public function getHtmlForShortcode($shortCodeContent)
    {
        /* parse the shortcode if we need to */
        if ($shortCodeContent != '') {

            $shortcodeParser = tubepress_impl_patterns_sl_ServiceLocator::getShortcodeParser();
            $shortcodeParser->parse($shortCodeContent);
        }

        $handlers = tubepress_impl_patterns_sl_ServiceLocator::getShortcodeHandlers();

        usort($handlers, array($this, 'sortShortcodeHandlers'));

        $html = null;

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

        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

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

    public final function sortShortcodeHandlers($first, $second)
    {
        if ($first instanceof tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService) {

            return 1;
        }

        if ($second instanceof tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService) {

            return -1;
        }

        return 0;
    }
}
