<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_http_impl_puzzle_streams_PuzzleStream<extended>
 */
class tubepress_test_http_impl_puzzle_streams_PuzzleStreamTest extends tubepress_test_http_impl_puzzle_streams_AbstractStreamTest
{
    protected function getExpectedDelegateClass()
    {
        return 'tubepress_api_streams_StreamInterface';
    }

    protected function getSutClass()
    {
        return 'tubepress_http_impl_puzzle_streams_PuzzleStream';
    }
}
