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
 */
class tubepress_platform_impl_util_StringUtils implements tubepress_platform_api_util_StringUtilsInterface
{
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
    public function replaceFirst($search, $replace, $str)
    {
        $l    = strlen($str);
        $a    = strpos($str, $search);
        $b    = $a + strlen($search);
        $temp = substr($str, 0, $a) . $replace . substr($str, $b, ($l-$b));
        return $temp;
    }

    /**
     * @param string $string The incoming string.
     *
     * @return string The string without new lines.
     *
     * @api
     * @since 4.0.0
     */
    public function removeNewLines($string)
    {
        return str_replace(array("\r\n", "\r", "\n"), '', $string);
    }

    /**
     * Grabbed from http://programming-oneliners.blogspot.com/2006/03/remove-blank-empty-lines-php-29.html
     *
     * @param string $string The string to modify
     *
     * @return string The string with most empty lines removed.
     *
     * @api
     * @since 4.0.0
     */
    public function removeEmptyLines($string)
    {
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);
    }

    /**
     * http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
     *
     * @param string $haystack Haystack.
     * @param string $needle   Needle.
     *
     * @return bool True if the haystack starts with the needle. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function startsWith($haystack, $needle)
    {
        if (! is_string($haystack) || ! is_string($needle)) {

            return false;
        }

        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
     *
     * @param string $haystack Haystack.
     * @param string $needle   Needle.
     *
     * @return bool True if the haystack ends with the needle. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function endsWith($haystack, $needle)
    {
        if (! is_string($haystack) || ! is_string($needle)) {

            return false;
        }

        $length = strlen($needle);
        $start  = $length * -1; //negative

        return (substr($haystack, $start) === $needle);
    }

    /**
     * Strips slashes recursively.
     *
     * http://us2.php.net/manual/en/function.stripslashes.php#92524
     *
     * @param string $text  The incoming string.
     * @param int    $times The recursion depth.
     *
     * @return string The modified text.
     *
     * @api
     * @since 4.0.0
     */
    public function stripslashes_deep($text, $times = 2) {

        $i = 0;

        // loop will execute $times times.
        while (strstr($text, '\\') && $i != $times) {

            $text = stripslashes($text);
            $i++;
        }

        return $text;
    }

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
    public function redactSecrets($string)
    {
        if (is_scalar($string)) {

            $string = "$string";

        } else {

            if (is_array($string)) {

                $string = var_export($string, true);

            } else {

                $string = 'resource/object';
            }
        }

        return preg_replace('/[0-9a-fA-F]{12,}/', 'XXXXXX', $string);
    }
}