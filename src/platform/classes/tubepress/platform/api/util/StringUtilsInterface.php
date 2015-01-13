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
 * Handy string utilities.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_platform_api_util_StringUtilsInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_platform_api_util_StringUtilsInterface';

    /**
     * Replaces the first occurence of a string by another string
     *
     * @param string $search  The needle
     * @param string $replace The replacement string
     * @param string $str     The haystack
     *
     * @return string The haystack with the first needle replaced
     *                by the replacement string
     *
     * @api
     * @since 4.0.0
     */
    function replaceFirst($search, $replace, $str);

    /**
     * @param string $string The incoming string.
     *
     * @return string The string without new lines.
     *
     * @api
     * @since 4.0.0
     */
    function removeNewLines($string);

    /**
     * @param string $string The string to modify
     *
     * @return string The string with most empty lines removed.
     *
     * @api
     * @since 4.0.0
     */
    function removeEmptyLines($string);

    /**
     * @param string $haystack Haystack.
     * @param string $needle   Needle.
     *
     * @return bool True if the haystack starts with the needle. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function startsWith($haystack, $needle);

    /**
     * @param string $haystack Haystack.
     * @param string $needle   Needle.
     *
     * @return bool True if the haystack ends with the needle. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function endsWith($haystack, $needle);

    /**
     * Strips slashes recursively.
     *
     * @param string $text  The incoming string.
     * @param int    $times The recursion depth.
     *
     * @return string The modified text.
     *
     * @api
     * @since 4.0.0
     */
    function stripslashes_deep($text, $times = 2);

    /**
     * Masks hex strings.
     *
     * @param string $string The incoming string.
     *
     * @return string The same string with hex sequences replaced by asterisks.
     *
     * @api
     * @since 4.0.0
     */
    function redactSecrets($string);
}