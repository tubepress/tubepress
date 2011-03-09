<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
 * Some string utilities
 *
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

    //http://programming-oneliners.blogspot.com/2006/03/remove-blank-empty-lines-php-29.html
    public static function removeEmptyLines($string)
    {
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);
    }
    
    public static function cleanForSearch($string)
    {
        /* chop it off at 100 chars */
        $result = substr($string, 0, 100);
        
        /* only allow alphanumerics, pipe, plus, quotes, and minus */
        return preg_replace('/[^a-zA-Z0-9"\'\+\|\- ]/', '', $result);
    }
}
