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
class tubepress_impl_html_CssAndJsHtmlGenerator implements tubepress_spi_html_CssAndJsHtmlGeneratorInterface
{
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
