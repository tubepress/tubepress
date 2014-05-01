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

class tubepress_addons_wordpress_impl_filters_Content
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_api_html_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_api_shortcode_ParserInterface
     */
    private $_shortcodeParser;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_api_options_ContextInterface $context,
                                tubepress_api_options_PersistenceInterface $persistence,
                                tubepress_api_html_HtmlGeneratorInterface $htmlGenerator,
                                tubepress_api_shortcode_ParserInterface $parser,
                                tubepress_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_context         = $context;
        $this->_persistence     = $persistence;
        $this->_htmlGenerator   = $htmlGenerator;
        $this->_shortcodeParser = $parser;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function filter(tubepress_api_event_EventInterface $event)
    {
        $content = $event->getSubject();

        /* do as little work as possible here 'cause we might not even run */
        $trigger = $this->_persistence->fetch(tubepress_api_const_options_names_Advanced::KEYWORD);

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
            try {

                $generatedHtml = $this->_htmlGenerator->getHtmlForShortcode($content);

            } catch (Exception $e) {

                $generatedHtml = $this->_dispatchErrorAndGetMessage($e);
            }

            /* remove any leading/trailing <p> tags from the content */
            $pattern = '/(<[P|p]>\s*)(' . preg_quote($this->_shortcodeParser->getLastShortcodeUsed(), '/') . ')(\s*<\/[P|p]>)/';
            $content = preg_replace($pattern, '${2}', $content);

            /* replace the shortcode with our new content */
            $currentShortcode = $this->_shortcodeParser->getLastShortcodeUsed();
            $content          = tubepress_impl_util_StringUtils::replaceFirst($currentShortcode, $generatedHtml, $content);
            $content          = tubepress_impl_util_StringUtils::removeEmptyLines($content);

            /* reset the context for the next shortcode */
            $this->_context->setAll(array());
        }

        return $content;
    }

    private function _dispatchErrorAndGetMessage(Exception $e)
    {
        $event = new tubepress_spi_event_EventBase($e, array(
            'message' => $e->getMessage()
        ));

        $this->_eventDispatcher->dispatch(tubepress_api_const_event_EventNames::ERROR_EXCEPTION_CAUGHT, $event);

        return $event->getArgument('message');
    }
}
