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
 * Time conversion utilities.
 *
 */
class org_tubepress_impl_util_TimeUtils
{
    //Grabbed from http://www.weberdev.com/get_example-4769.html
    /**
     * Converts a unix timestamp to relative time.
     *
     * @param integer $timestamp The Unix timestamp.
     *
     * @return string The relative time of this timestamp.
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
        $timezone = str_replace(':', '', substr($tmp, 19, 6));

        return @strtotime($datetime . ' ' . $timezone);
    }
}

