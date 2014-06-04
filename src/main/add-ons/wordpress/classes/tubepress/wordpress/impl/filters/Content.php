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

class tubepress_wordpress_impl_filters_Content
{
    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_options_api_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_core_html_api_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_core_shortcode_api_ParserInterface
     */
    private $_shortcodeParser;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_core_options_api_ContextInterface       $context,
                                tubepress_core_options_api_PersistenceInterface   $persistence,
                                tubepress_core_html_api_HtmlGeneratorInterface    $htmlGenerator,
                                tubepress_core_shortcode_api_ParserInterface      $parser,
                                tubepress_api_util_StringUtilsInterface           $stringUtils)
    {
        $this->_context         = $context;
        $this->_persistence     = $persistence;
        $this->_htmlGenerator   = $htmlGenerator;
        $this->_shortcodeParser = $parser;
        $this->_stringUtils     = $stringUtils;
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function filter(tubepress_core_event_api_EventInterface $event)
    {
        $content = $event->getSubject();

        /* do as little work as possible here 'cause we might not even run */
        $trigger = $this->_persistence->fetch(tubepress_core_shortcode_api_Constants::OPTION_KEYWORD);

        /* no shortcode? get out */
        if (!$this->_shortcodeParser->somethingToParse($content, $trigger)) {

            return;
        }

        $event->setSubject($this->_getHtml($content, $trigger));
    }

    private function _getHtml($content, $trigger)
    {
        /* Parse each shortcode one at a time */
        while ($this->_shortcodeParser->somethingToParse($content, $trigger)) {

            /* Get the HTML for this particular shortcode. Could be a single video or a gallery. */
            $generatedHtml = $this->_htmlGenerator->getHtmlForShortcode($content);

            /* remove any leading/trailing <p> tags from the content */
            $pattern = '/(<[P|p]>\s*)(' . preg_quote($this->_shortcodeParser->getLastShortcodeUsed(), '/') . ')(\s*<\/[P|p]>)/';
            $content = preg_replace($pattern, '${2}', $content);

            /* replace the shortcode with our new content */
            $currentShortcode = $this->_shortcodeParser->getLastShortcodeUsed();
            $content          = $this->_stringUtils->replaceFirst($currentShortcode, $generatedHtml, $content);
            $content          = $this->_stringUtils->removeEmptyLines($content);

            /* reset the context for the next shortcode */
            $this->_context->setEphemeralOptions(array());
        }

        return $content;
    }
}