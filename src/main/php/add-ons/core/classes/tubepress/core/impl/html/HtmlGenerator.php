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
class tubepress_core_impl_html_HtmlGenerator implements tubepress_core_api_html_HtmlGeneratorInterface
{
    /**
     * @var tubepress_core_api_shortcode_ParserInterface
     */
    private $_shortcodeParser;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_api_theme_ThemeLibraryInterface
     */
    private $_themeLibrary;

    public function __construct(tubepress_core_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_core_api_shortcode_ParserInterface      $parser,
                                tubepress_core_api_theme_ThemeLibraryInterface    $themeLibrary)
    {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_shortcodeParser = $parser;
        $this->_themeLibrary    = $themeLibrary;
    }

    /**
     * @return string The HTML that should be displayed in the HTML <head> for CSS.
     *
     * @api
     * @since 4.0.0
     */
    public function getCssHtml()
    {
        $cssHtml = $this->_fireEventAndReturnSubject(tubepress_core_api_const_event_EventNames::HTML_STYLESHEETS_PRE, '') . "\n";

        $styles         = $this->_themeLibrary->getStylesUrls();
        $filteredStyles = $this->_fireEventAndReturnSubject(tubepress_core_api_const_event_EventNames::CSS_JS_STYLESHEETS, $styles);

        foreach ($filteredStyles as $url) {

            $cssHtml .= $this->_toCssTag($url);
        }

        $cssHtml = $this->_fireEventAndReturnSubject(tubepress_core_api_const_event_EventNames::HTML_STYLESHEETS_POST, $cssHtml);

        return $cssHtml;
    }

    /**
     * Generates the HTML for the given shortcode.
     *
     * @param string $shortCodeContent The shortcode content.
     *
     * @return string The HTML for the given shortcode, or the error message if there was a problem.
     *
     * @api
     * @since 4.0.0
     */
    public function getHtmlForShortcode($shortCodeContent)
    {
        /* parse the shortcode if we need to */
        if ($shortCodeContent != '') {

            $this->_shortcodeParser->parse($shortCodeContent);
        }

        $htmlProviderSelectionEvent = $this->_eventDispatcher->newEventInstance();

        $this->_eventDispatcher->dispatch(tubepress_core_api_const_event_EventNames::HTML_GENERATION, $htmlProviderSelectionEvent);

        /**
         * @var $selected string
         */
        $html = $htmlProviderSelectionEvent->getSubject();

        if ($html === null) {

            throw new RuntimeException('No providers could generate HTML');
        }

        return $html;
    }

    /**
     * @return string The HTML that should be displayed for JS to be loaded onto the page. May occure in head
     *                or near footer.
     *
     * @api
     * @since 4.0.0
     */
    public function getJsHtml()
    {
        $scripts         = $this->_themeLibrary->getScriptsUrls();
        $jsHtml          = $this->_fireEventAndReturnSubject(tubepress_core_api_const_event_EventNames::HTML_SCRIPTS_PRE, '') . "\n";
        $filteredScripts = $this->_fireEventAndReturnSubject(tubepress_core_api_const_event_EventNames::CSS_JS_SCRIPTS, $scripts);

        foreach ($filteredScripts as $url) {

            $jsHtml .= $this->_toJsTag($url);
        }

        $jsHtml = $this->_fireEventAndReturnSubject(tubepress_core_api_const_event_EventNames::HTML_SCRIPTS_POST, $jsHtml);

        return $jsHtml;
    }

    private function _toJsTag(tubepress_core_api_url_UrlInterface $url)
    {
        return sprintf("<script type=\"text/javascript\" src=\"%s\"></script>\n", $url);

    }

    private function _toCssTag(tubepress_core_api_url_UrlInterface $url)
    {
        return sprintf("<link href=\"%s\" rel=\"stylesheet\" type=\"text/css\">\n", $url);
    }

    private function _fireEventAndReturnSubject($eventName, $raw)
    {
        $event = $this->_eventDispatcher->newEventInstance($raw);

        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }
}
