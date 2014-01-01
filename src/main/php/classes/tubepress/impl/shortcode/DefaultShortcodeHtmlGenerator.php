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
 * Generates HTML from some shortcode.
 */
class tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator implements tubepress_spi_shortcode_ShortcodeHtmlGenerator
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var tubepress_spi_shortcode_PluggableShortcodeHandlerService[]
     */
    private $_shortcodeHandlers = array();

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Shortcode HTML Generator');
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

        usort($this->_shortcodeHandlers, array($this, 'sortShortcodeHandlers'));

        $html = null;

        /**
         * @var $handler tubepress_spi_shortcode_PluggableShortcodeHandlerService
         */
        foreach ($this->_shortcodeHandlers as $handler) {

            if ($handler->shouldExecute()) {

                $html = $handler->getHtml();

                break;
            }
        }

        if ($html === null) {

            throw new RuntimeException('No shortcode handlers could generate HTML');
        }

        return $html;
    }

    public final function sortShortcodeHandlers($first, $second)
    {
        if ($first instanceof tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService) {

            return 1;
        }

        if ($second instanceof tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService) {

            return -1;
        }

        return 0;
    }

    public function setPluggableShortcodeHandlers(array $handlers)
    {
        $this->_shortcodeHandlers = $handlers;
    }
}
