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
 * @api
 * @since 4.1.0
 */
interface tubepress_api_array_ArrayReaderInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_array_ArrayReaderInterface';

    /**
     * @api
     * @since 4.1.0
     *
     * @param array $array
     * @param $path
     * @param int $default
     *
     * @return int
     */
    function getAsInteger(array $array, $path, $default = 0);

    /**
     * @api
     * @since 4.1.0
     *
     * @param array $array
     * @param $path
     * @param float $default
     *
     * @return float
     */
    function getAsFloat(array $array, $path, $default = 0.0);

    /**
     * @api
     * @since 4.1.0
     *
     * @param array $array
     * @param $path
     * @param bool $default
     *
     * @return bool
     */
    function getAsBoolean(array $array, $path, $default = false);

    /**
     * @api
     * @since 4.1.0
     *
     * @param array $array
     * @param $path
     * @param string $default
     *
     * @return string
     */
    function getAsString(array $array, $path, $default = '');

    /**
     * @api
     * @since 4.1.0
     *
     * @param array $array
     * @param $path
     * @param array $default
     *
     * @return array
     */
    function getAsArray(array $array, $path, $default = array());
}