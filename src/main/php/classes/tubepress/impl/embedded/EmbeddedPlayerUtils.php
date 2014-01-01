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
 * Embedded player utilities
 *
 */
class tubepress_impl_embedded_EmbeddedPlayerUtils
{
    /**
     * Returns a valid HTML color.
     *
     * @param string $candidate The first-choice HTML color. May be invalid.
     * @param string $default   The fallback HTML color. Must be be invalid.
     *
     * @return string $candidate if it's a valid HTML color. $default otherwise.
     */
    public static function getSafeColorValue($candidate, $default)
    {
        $pattern = '/^[0-9a-fA-F]{6}$/';

        if (preg_match($pattern, $candidate) === 1) {

            return $candidate;
        }

        return $default;
    }

    /**
     * Converts a boolean value to a string 1 or 0.
     *
     * @param boolean $bool The boolean value to convert.
     *
     * @return string '1' or '0'
     */
    public static function booleanToOneOrZero($bool)
    {
        if ($bool === '1' || $bool === '0') {

            return $bool;
        }

        return $bool ? '1' : '0';
    }

    /**
     * Converts a boolean value to string.
     *
     * @param boolean $bool The boolean value to convert.
     *
     * @return string 'true' or 'false'
     */
    public static function booleanToString($bool)
    {
        return $bool ? 'true' : 'false';
    }
}