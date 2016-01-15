<?php
/**
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
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_util_TimeUtilsInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_util_TimeUtilsInterface';

    /**
     * Converts a unix timestamp to relative time.
     *
     * @param integer $timestamp The Unix timestamp.
     *
     * @return string The relative time of this timestamp.
     *
     * @throws LogicException
     *
     * @api
     * @since 4.0.0
     */
    function getRelativeTime($timestamp);

    /**
     * Converts a count of seconds to a minutes:seconds format.
     *
     * @param int $seconds The count of seconds.
     *
     * @return string The time in minutes:seconds format
     *
     * @api
     * @since 4.0.0
     */
    function secondsToHumanTime($seconds);

    /**
     * Converts gdata timestamps to unix time
     *
     * @param string $rfcTime The RFC 3339 format of time
     *
     * @return int Unix time for the given RFC 3339 time
     *
     * @api
     * @since 4.0.0
     */
    function rfc3339toUnixTime($rfcTime);

    /**
     * Given a unix time, return a human-readable version.
     *
     * @param int|string $unixTime The given unix time.
     * @param string     $format   The time format.
     * @param bool       $relative Convert to relative time, instead.
     *
     * @return string A human readable time.
     *
     * @api
     * @since 4.0.0
     */
    function unixTimeToHumanReadable($unixTime, $format, $relative);
}