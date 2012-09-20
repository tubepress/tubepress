<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class tubepress_plugins_wordpresscore_lib_impl_DefaultContentFilter implements tubepress_plugins_wordpresscore_lib_spi_ContentFilter
{
    /**
     * Filter the content (which may be empty).
     */
    public final function filterContent($content = '')
    {
        /* do as little work as possible here 'cause we might not even run */
        $wpsm    = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionStorageManager();
        $trigger = $wpsm->get(tubepress_api_const_options_names_Advanced::KEYWORD);
        $parser  = tubepress_impl_patterns_ioc_KernelServiceLocator::getShortcodeParser();

        /* no shortcode? get out */
        if (!$parser->somethingToParse($content, $trigger)) {

            return $content;
        }

        return self::_getHtml($content, $trigger, $parser);
    }

    private static function _getHtml($content, $trigger, tubepress_spi_shortcode_ShortcodeParser $parser)
    {
        $context = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $gallery = tubepress_impl_patterns_ioc_KernelServiceLocator::getShortcodeHtmlGenerator();

        /* Parse each shortcode one at a time */
        while ($parser->somethingToParse($content, $trigger)) {

            /* Get the HTML for this particular shortcode. Could be a single video or a gallery. */
            try {

                $generatedHtml = $gallery->getHtmlForShortcode($content);

            } catch (Exception $e) {

                $generatedHtml = $e->getMessage();
            }

            /* remove any leading/trailing <p> tags from the content */
            $pattern = '/(<[P|p]>\s*)(' . preg_quote($context->getActualShortcodeUsed(), '/') . ')(\s*<\/[P|p]>)/';
            $content = preg_replace($pattern, '${2}', $content);

            /* replace the shortcode with our new content */
            $currentShortcode = $context->getActualShortcodeUsed();
            $content          = tubepress_impl_util_StringUtils::replaceFirst($currentShortcode, $generatedHtml, $content);

            /* reset the context for the next shortcode */
            $context->reset();
        }

        return $content;
    }


}

