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
 * Language utilities.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_util_LangUtilsInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_util_LangUtilsInterface';

    /**
     * @param mixed $candidate
     *
     * @return bool True if the argument is an associative array, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function isAssociativeArray($candidate);

    /**
     * @param mixed $candidate The value to convert to a one or a zero.
     *
     * @return string '1' or '0', depending on the boolean conversion of the incoming value.
     *
     * @api
     * @since 4.0.0
     */
    function booleanToStringOneOrZero($candidate);

    /**
     * @param mixed $candidate
     *
     * @return bool True if the given value is a non-associative array whose values are all strings.
     *
     * @api
     * @since 4.0.0
     */
    function isSimpleArrayOfStrings($candidate);
}