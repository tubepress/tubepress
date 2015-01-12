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
 * PHP language utilities.
 */
class tubepress_platform_impl_util_LangUtils implements tubepress_platform_api_util_LangUtilsInterface
{
    /**
     * @param mixed $candidate
     *
     * @return bool True if the argument is an associative array, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isAssociativeArray($candidate)
    {
        return is_array($candidate)
            && ! empty($candidate)
            && count(array_filter(array_keys($candidate),'is_string')) == count($candidate);
    }

    /**
     * @param mixed $candidate The value to convert to a one or a zero.
     *
     * @return string '1' or '0', depending on the boolean conversion of the incoming value.
     *
     * @api
     * @since 4.0.0
     */
    public function booleanToStringOneOrZero($candidate)
    {
        if ($candidate === '1' || $candidate === '0') {

            return $candidate;
        }

        return $candidate ? '1' : '0';
    }

    /**
     * @param mixed $candidate
     *
     * @return bool True if the given value is a non-associative array whose values are all strings.
     *
     * @api
     * @since 4.0.0
     */
    public function isSimpleArrayOfStrings($candidate)
    {
        if (!is_array($candidate)) {

            return false;
        }

        if ($this->isAssociativeArray($candidate)) {

            return false;
        }

        return array_reduce($candidate, array($this, '__callback_isSimpleArrayOfStrings'));
    }

    public function __callback_isSimpleArrayOfStrings($carry, $item)
    {
        if ($carry === null) {

            $carry = true;
        }

        if ($carry === false) {

            return false;
        }

        return is_string($item);
    }
}