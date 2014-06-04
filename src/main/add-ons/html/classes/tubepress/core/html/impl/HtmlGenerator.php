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
class tubepress_core_html_impl_HtmlGenerator implements tubepress_core_html_api_HtmlGeneratorInterface
{
    /**
     * @var tubepress_core_shortcode_api_ParserInterface
     */
    private $_shortcodeParser;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_theme_api_ThemeLibraryInterface
     */
    private $_themeLibrary;

    public function __construct(tubepress_core_event_api_EventDispatcherInterface $eventDispatcher,
                                tubepress_core_shortcode_api_ParserInterface      $parser,
                                tubepress_core_theme_api_ThemeLibraryInterface    $themeLibrary)
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
        $cssHtml        = $this->_fireEventAndReturnSubject(tubepress_core_html_api_Constants::EVENT_STYLESHEETS_PRE, '') . "\n";
        $styles         = $this->_themeLibrary->getStylesUrls();
        $filteredStyles = $this->_fireEventAndReturnSubject(tubepress_core_html_api_Constants::EVENT_STYLESHEETS, $styles);

        foreach ($filteredStyles as $url) {

            $cssHtml .= $this->_toCssTag($url);
        }

        $cssHtml = $this->_fireEventAndReturnSubject(tubepress_core_html_api_Constants::EVENT_STYLESHEETS_POST, $cssHtml);

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
        try {

            return $this->_wrappedGetHtmlForShortcode($shortCodeContent);

        } catch (Exception $e) {

            return $this->_getHtmlForException($e);
        }
    }

    private function _wrappedGetHtmlForShortcode($shortCodeContent)
    {
        /* parse the shortcode if we need to */
        if ($shortCodeContent != '') {

            $this->_shortcodeParser->parse($shortCodeContent);
        }

        $htmlProviderSelectionEvent = $this->_eventDispatcher->newEventInstance('');

        $this->_eventDispatcher->dispatch(tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML, $htmlProviderSelectionEvent);

        /**
         * @var $selected string
         */
        $html = $htmlProviderSelectionEvent->getSubject();

        if ($html === null) {

            throw new RuntimeException('Unable to generate HTML.');
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
        $jsHtml          = $this->_fireEventAndReturnSubject(tubepress_core_html_api_Constants::EVENT_SCRIPTS_PRE, '') . "\n";
        $filteredScripts = $this->_fireEventAndReturnSubject(tubepress_core_html_api_Constants::EVENT_SCRIPTS, $scripts);

        foreach ($filteredScripts as $url) {

            $jsHtml .= $this->_toJsTag($url);
        }

        $jsHtml = $this->_fireEventAndReturnSubject(tubepress_core_html_api_Constants::EVENT_SCRIPTS_POST, $jsHtml);

        return $jsHtml;
    }

    private function _toJsTag(tubepress_core_url_api_UrlInterface $url)
    {
        return sprintf("<script type=\"text/javascript\" src=\"%s\"></script>\n", $url);

    }

    private function _toCssTag(tubepress_core_url_api_UrlInterface $url)
    {
        return sprintf("<link href=\"%s\" rel=\"stylesheet\" type=\"text/css\">\n", $url);
    }

    private function _fireEventAndReturnSubject($eventName, $raw)
    {
        $event = $this->_eventDispatcher->newEventInstance($raw);

        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }

    private function _getHtmlForException(Exception $e)
    {
        $event = $this->_eventDispatcher->newEventInstance($e, array(

            'htmlForUser' => $e->getMessage()
        ));

        $this->_eventDispatcher->dispatch(tubepress_core_html_api_Constants::EVENT_EXCEPTION_CAUGHT, $event);

        return $event->getArgument('htmlForUser');
    }
}