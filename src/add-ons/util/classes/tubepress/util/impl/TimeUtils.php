<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Time conversion utilities.
 */
class tubepress_util_impl_TimeUtils implements tubepress_api_util_TimeUtilsInterface
{
    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_stringUtils = $stringUtils;
    }

    //Grabbed from http://www.weberdev.com/get_example-4769.html
    /**
     * {@inheritdoc}
     */
    public function getRelativeTime($timestamp)
    {
        $difference = time() - $timestamp;
        $a          = array(
            31104000 => 'year',
            2592000  => 'month',
            86400    => 'day',
            3600     => 'hour',
            60       => 'minute',
            1        => 'second',
        );

        foreach ($a as $secs => $str) {

            $d = $difference / $secs;

            if ($d >= 1) {

                $r = round($d);

                return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
            }
        }

        throw new LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function secondsToHumanTime($seconds)
    {
        $format = $seconds > 3600 ? 'H:i:s' : 'i:s';
        $raw    = gmdate($format, $seconds);

        if (strpos($raw, '0') === 0) {

            $raw = substr($raw, 1);
        }

        return $raw;
    }

    /**
     * {@inheritdoc}
     */
    public function rfc3339toUnixTime($rfcTime)
    {
        $tmp      = str_replace('T', ' ', $rfcTime);
        $tmp      = preg_replace('/(\.[0-9]{1,})?/', '', $tmp);
        $datetime = substr($tmp, 0, 19);

        if ($this->_stringUtils->endsWith($tmp, 'Z')) {

            $reset = date_default_timezone_get();

            date_default_timezone_set('UTC');

            $toReturn = strtotime($datetime);

            date_default_timezone_set($reset);

            return $toReturn;
        }

        $timezone = str_replace(':', '', substr($tmp, 19, 6));

        return strtotime($datetime . ' ' . $timezone);
    }

    /**
     * {@inheritdoc}
     */
    public function unixTimeToHumanReadable($unixTime, $format, $relative)
    {
        if ($unixTime == '') {

            return '';
        }

        if ($relative) {

            return self::getRelativeTime($unixTime);
        }

        if (strpos($format, '%') === false) {

            return @date($format, $unixTime);
        }

        return strftime($format, $unixTime);
    }
}
