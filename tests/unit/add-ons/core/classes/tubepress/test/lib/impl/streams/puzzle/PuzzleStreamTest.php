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
 * @covers tubepress_lib_impl_streams_puzzle_PuzzleStream<extended>
 */
class tubepress_test_lib_impl_streams_puzzle_PuzzleStreamTest extends tubepress_test_lib_impl_streams_puzzle_AbstractStreamTest
{
    protected function getExpectedDelegateClass()
    {
        return 'tubepress_lib_api_streams_StreamInterface';
    }

    protected  function getSutClass()
    {
        return 'tubepress_lib_impl_streams_puzzle_PuzzleStream';
    }
}