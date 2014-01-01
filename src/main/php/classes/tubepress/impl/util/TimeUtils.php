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
 * Time conversion utilities.
 *
 */
class tubepress_impl_util_TimeUtils
{
    //Grabbed from http://www.weberdev.com/get_example-4769.html
    /**
     * Converts a unix timestamp to relative time.
     *
     * @param integer $timestamp The Unix timestamp.
     *
     * @return string The relative time of this timestamp.
     *
     * @throws LogicException
     */
    public static function getRelativeTime($timestamp)
    {
        $difference = time() - $timestamp;
        $a = array(
            31104000 =>  'year',
            2592000  =>  'month',
            86400    =>  'day',
            3600     =>  'hour',
            60       =>  'minute',
            1        =>  'second'
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
     * Converts a count of seconds to a minutes:seconds format.
     *
     * @param $integer $seconds The count of seconds.
     *
     * @return string The time in minutes:seconds format
     */
    public static function secondsToHumanTime($seconds)
    {
        $length          = intval($seconds / 60);
        $leftOverSeconds = $seconds % 60;
        if ($leftOverSeconds < 10) {
            $leftOverSeconds = '0' . $leftOverSeconds;
        }
        $length .= ':' . $leftOverSeconds;
        return $length;
    }

    /**
     * Converts gdata timestamps to unix time
     * 
     * @param string $rfcTime The RFC 3339 format of time
     * 
     * @return int Unix time for the given RFC 3339 time
     */
    public static function rfc3339toUnixTime($rfcTime)
    {
        $tmp      = str_replace('T', ' ', $rfcTime);
        $tmp      = preg_replace('/(\.[0-9]{1,})?/', '', $tmp);
        $datetime = substr($tmp, 0, 19);

        if (tubepress_impl_util_StringUtils::endsWith($tmp, 'Z')) {

            $reset = date_default_timezone_get();

            date_default_timezone_set('UTC');

            $toReturn = strtotime($datetime);

            date_default_timezone_set($reset);

            return $toReturn;
        }

        $timezone = str_replace(':', '', substr($tmp, 19, 6));

        return strtotime($datetime . ' ' . $timezone);
    }
}

