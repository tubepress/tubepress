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
 * PHP language utilities.
 */
class tubepress_util_impl_LangUtils implements tubepress_api_util_LangUtilsInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAssociativeArray($candidate)
    {
        return is_array($candidate)
            && !empty($candidate)
            && count(array_filter(array_keys($candidate), 'is_string')) == count($candidate);
    }

    /**
     * {@inheritdoc}
     */
    public function booleanToStringOneOrZero($candidate)
    {
        if ($candidate === '1' || $candidate === '0') {

            return $candidate;
        }

        return $candidate ? '1' : '0';
    }

    /**
     * {@inheritdoc}
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
