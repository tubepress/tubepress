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
 * Generates HTML for use in the <head>.
 */
class tubepress_addons_coreapiservices_impl_html_HtmlGenerator implements tubepress_api_html_HtmlGeneratorInterface
{
    /**
     * @var tubepress_spi_shortcode_PluggableShortcodeHandlerService[]
     */
    private $_shortcodeHandlers = array();

    /**
     * @return string The HTML that should be displayed in the HTML <head>.
     */
    public function getCssHtml()
    {
        $cssHtml = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::HTML_STYLESHEETS_PRE, '') . "\n";

        $themeHandler   = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $styles         = $themeHandler->getStyles();
        $filteredStyles = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_STYLESHEETS, $styles);

        foreach ($filteredStyles as $url) {

            $cssHtml .= $this->_toCssTag($url);
        }

        $cssHtml = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::HTML_STYLESHEETS_POST, $cssHtml);

        return $cssHtml;
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

    /**
     * @return string The HTML that should be displayed in the HTML footer (just before </html>)
     */
    public function getJsHtml()
    {
        $themeHandler    = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $scripts         = $themeHandler->getScripts();
        $jsHtml          = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::HTML_SCRIPTS_PRE, '') . "\n";
        $filteredScripts = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_SCRIPTS, $scripts);

        foreach ($filteredScripts as $url) {

            $jsHtml .= $this->_toJsTag($url);
        }

        $jsHtml = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::HTML_SCRIPTS_POST, $jsHtml);

        return $jsHtml;
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

    private function _toJsTag($url)
    {
        return sprintf("<script type=\"text/javascript\" src=\"%s\"></script>\n", $url);

    }

    private function _toCssTag($url)
    {
        return sprintf("<link href=\"%s\" rel=\"stylesheet\" type=\"text/css\">\n", $url);
    }

    private function _fireEventAndReturnSubject($eventName, $raw)
    {
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $event           = new tubepress_spi_event_EventBase($raw);

        $eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }
}
