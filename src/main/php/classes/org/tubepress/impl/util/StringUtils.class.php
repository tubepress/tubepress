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

/**
 * Handy string utilities.
 */
class org_tubepress_impl_util_StringUtils
{
    /**
     * Replaces the first occurence of a string by another string
     *
     * @param string $search  The needle
     * @param string $replace The replacement string
     * @param string $str     The haystack
     *
     * @return string The haystack with the first needle replaced
     *     by the replacement string
     */
    public static function replaceFirst($search, $replace, $str)
    {
        $l    = strlen($str);
        $a    = strpos($str, $search);
        $b    = $a + strlen($search);
        $temp = substr($str, 0, $a) . $replace . substr($str, $b, ($l-$b));
        return $temp;
    }

    public static function removeNewLines($string)
    {
    	return str_replace(array("\r\n", "\r", "\n"), '', $string);
    }

    /**
     * Grabbed from http://programming-oneliners.blogspot.com/2006/03/remove-blank-empty-lines-php-29.html
     *
     * @param string $string The string to modify
     *
     * @return string The string with most empty lines removed.
     */
    public static function removeEmptyLines($string)
    {
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);
    }

    //http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
    public static function startsWith($haystack, $needle)
    {
        if (! is_string($haystack) || ! is_string($needle)) {

            return false;
        }

        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    //http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
    public static function endsWith($haystack, $needle)
    {
        if (! is_string($haystack) || ! is_string($needle)) {

            return false;
        }

        $length = strlen($needle);
        $start  = $length * -1; //negative

        return (substr($haystack, $start) === $needle);
    }

    //http://us2.php.net/manual/en/function.stripslashes.php#92524
    public static function stripslashes_deep($text, $times = 2) {

        $i = 0;

        // loop will execute $times times.
        while (strstr($text, '\\') && $i != $times) {

            $text = stripslashes($text);
            $i++;
        }

        return $text;
    }
}
