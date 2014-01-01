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

        $cssAndJsRegistry = tubepress_impl_patterns_sl_ServiceLocator::getCssAndJsRegistry();
        $styleHandles     = $cssAndJsRegistry->getStyleHandlesForDisplay();
        $styles           = array();

        foreach ($styleHandles as $handle) {

            $style = $cssAndJsRegistry->getStyle($handle);

            if ($style !== null) {

                $styles[$handle] = $style;
            }
        }

        $filteredStyles = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_STYLESHEETS, $styles);

        foreach ($filteredStyles as $handle => $info) {

            $cssHtml .= $this->_toCssTag($info);
        }

        $cssHtml = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::HTML_STYLESHEETS_POST, $cssHtml);

        return $cssHtml;
    }

    /**
     * @return string The HTML that should be displayed in the HTML footer (just before </html>)
     */
    public function getJsHtml()
    {
        $cssAndJsRegistry = tubepress_impl_patterns_sl_ServiceLocator::getCssAndJsRegistry();
        $scriptHandles     = $cssAndJsRegistry->getScriptHandlesForDisplay();
        $scripts           = array();

        foreach ($scriptHandles as $handle) {

            $script = $cssAndJsRegistry->getScript($handle);

            if ($script !== null) {

                $scripts[$handle] = $script;
            }
        }

        $jsHtml          = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::HTML_SCRIPTS_PRE, '') . "\n";
        $filteredScripts = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_SCRIPTS, $scripts);

        foreach ($filteredScripts as $handle => $info) {

            $jsHtml .= $this->_toJsTag($info);
        }

        $jsHtml = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::HTML_SCRIPTS_POST, $jsHtml);

        return $jsHtml;
    }

    private function _toJsTag(array $script)
    {
        return sprintf("<script type=\"text/javascript\" src=\"%s\"></script>\n", $script['url']);

    }

    private function _toCssTag(array $style)
    {
        return sprintf("<link href=\"%s\" rel=\"stylesheet\" type=\"text/css\" media=\"%s\">\n", $style['url'], $style['media']);
    }

    private function _fireEventAndReturnSubject($eventName, $raw)
    {
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $event           = new tubepress_spi_event_EventBase($raw);

        $eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }
}
