<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
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
class tubepress_util_impl_StringUtils implements tubepress_api_util_StringUtilsInterface
{
    /**
     * {@inheritdoc}
     */
    public function replaceFirst($search, $replace, $str)
    {
        $l    = strlen($str);
        $a    = strpos($str, $search);
        $b    = $a + strlen($search);
        $temp = substr($str, 0, $a) . $replace . substr($str, $b, ($l - $b));

        return $temp;
    }

    /**
     * {@inheritdoc}
     */
    public function removeNewLines($string)
    {
        return str_replace(array("\r\n", "\r", "\n"), '', $string);
    }

    // http://programming-oneliners.blogspot.com/2006/03/remove-blank-empty-lines-php-29.html
    /**
     * {@inheritdoc}
     */
    public function removeEmptyLines($string)
    {
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);
    }

    // http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
    /**
     * {@inheritdoc}
     */
    public function startsWith($haystack, $needle)
    {
        if (!is_string($haystack) || !is_string($needle)) {

            return false;
        }

        $length = strlen($needle);

        return substr($haystack, 0, $length) === $needle;
    }

    // http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
    /**
     * {@inheritdoc}
     */
    public function endsWith($haystack, $needle)
    {
        if (!is_string($haystack) || !is_string($needle)) {

            return false;
        }

        $length = strlen($needle);
        $start  = $length * -1; //negative

        return substr($haystack, $start) === $needle;
    }

    // http://us2.php.net/manual/en/function.stripslashes.php#92524
    /**
     * {@inheritdoc}
     */
    public function stripslashes_deep($text, $times = 2) {

        $i = 0;

        // loop will execute $times times.
        while (strstr($text, '\\') && $i != $times) {

            $text = stripslashes($text);
            ++$i;
        }

        return $text;
    }

    /**
     * {@inheritdoc}
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
