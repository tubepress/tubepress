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
class tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer
{
    /**
     * @var
     */
    private $_transformer;

    public function __construct($transformer)
    {
        $this->_transformer = $transformer;
    }

    public function transform($incoming)
    {
        return $this->_normalizeListValueOrNull($incoming, array($this->_transformer, 'transform'));
    }

    private function _normalizeListValueOrNull($candidate, $transformer)
    {
        $exploded = preg_split('~\s*,\s*~', $candidate);

        if (count($exploded) === 0) {

            return call_user_func($transformer, $candidate);
        }

        $collection = array();

        foreach ($exploded as $single) {

            $transformed = call_user_func($transformer, $single);

            if ($transformed) {

                $collection[] = $transformed;
            }
        }

        if (count($collection) < 1) {

            return null;
        }

        return implode(',', $collection);
    }
}
