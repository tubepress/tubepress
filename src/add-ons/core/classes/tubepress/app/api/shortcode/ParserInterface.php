<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Parses shortcodes.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_api_shortcode_ParserInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_api_shortcode_ParserInterface';

    /**
     * This function is used to parse a shortcode into options that TubePress can use.
     *
     * @param string $content The haystack in which to search
     *
     * @return array The associative array of parsed options.
     *
     * @api
     * @since 4.0.0
     *
     * @deprecated
     */
    function parse($content);

    /**
     * @return string|null The last shortcode used, or null if never parsed.
     *
     * @api
     * @since 4.0.0
     *
     * @deprecated
     */
    function getLastShortcodeUsed();

    /**
     * Determines if the given content contains a shortcode.
     *
     * @param string $content The content to search through
     * @param string $trigger The shortcode trigger word
     *
     * @return boolean True if there's a shortcode in the content, false otherwise.
     *
     * @api
     * @since 4.0.0
     *
     * @deprecated
     */
    function somethingToParse($content, $trigger = "tubepress");
}