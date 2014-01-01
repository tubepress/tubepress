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

class tubepress_addons_wordpress_impl_DefaultContentFilter implements tubepress_addons_wordpress_spi_ContentFilter
{
    /**
     * Filter the content (which may be empty).
     */
    public final function filterContent($content = '')
    {
        /* do as little work as possible here 'cause we might not even run */
        $wpsm    = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $trigger = $wpsm->fetch(tubepress_api_const_options_names_Advanced::KEYWORD);
        $parser  = tubepress_impl_patterns_sl_ServiceLocator::getShortcodeParser();

        /* no shortcode? get out */
        if (!$parser->somethingToParse($content, $trigger)) {

            return $content;
        }

        return $this->_getHtml($content, $trigger, $parser);
    }

    private function _getHtml($content, $trigger, tubepress_spi_shortcode_ShortcodeParser $parser)
    {
        $context = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $gallery = tubepress_impl_patterns_sl_ServiceLocator::getShortcodeHtmlGenerator();

        /* Parse each shortcode one at a time */
        while ($parser->somethingToParse($content, $trigger)) {

            /* Get the HTML for this particular shortcode. Could be a single video or a gallery. */
            try {

                $generatedHtml = $gallery->getHtmlForShortcode($content);

            } catch (Exception $e) {

                $generatedHtml = $this->_dispatchErrorAndGetMessage($e);
            }

            /* remove any leading/trailing <p> tags from the content */
            $pattern = '/(<[P|p]>\s*)(' . preg_quote($context->getActualShortcodeUsed(), '/') . ')(\s*<\/[P|p]>)/';
            $content = preg_replace($pattern, '${2}', $content);

            /* replace the shortcode with our new content */
            $currentShortcode = $context->getActualShortcodeUsed();
            $content          = tubepress_impl_util_StringUtils::replaceFirst($currentShortcode, $generatedHtml, $content);
            $content          = tubepress_impl_util_StringUtils::removeEmptyLines($content);

            /* reset the context for the next shortcode */
            $context->reset();
        }

        return $content;
    }

    private function _dispatchErrorAndGetMessage(Exception $e)
    {
        $event = new tubepress_spi_event_EventBase($e, array(
            'message' => $e->getMessage()
        ));

        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::ERROR_EXCEPTION_CAUGHT, $event);

        return $event->getArgument('message');
    }
}
