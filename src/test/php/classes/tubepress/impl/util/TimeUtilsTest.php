<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_impl_util_TimeUtilsTest extends TubePressUnitTest
{
	function testGetRelativeTimePastDecade()
	{
		$result = tubepress_impl_util_TimeUtils::getRelativeTime(1000000000);
		$this->assertEquals('12 years ago', $result);
	}

	function testGetRelativeTimePast5Years()
	{
	    $result = tubepress_impl_util_TimeUtils::getRelativeTime(1288760400);
	    $this->assertEquals('2 years ago', $result);
	}

	function testSeconds2HumanTime()
	{
		$result = tubepress_impl_util_TimeUtils::secondsToHumanTime(63);
		$this->assertEquals('1:03', $result);
	}

	function testRfc3339toUnixTime()
	{
		$result = tubepress_impl_util_TimeUtils::rfc3339toUnixTime('1980-11-03T09:03:33.000-05:00');
		$this->assertEquals('342108213', $result);
	}
}

