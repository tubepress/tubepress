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

class tubepress_impl_util_TimeUtilsTest extends PHPUnit_Framework_TestCase
{
	function testGetRelativeTimePastDecade()
	{
		$result = tubepress_impl_util_TimeUtils::getRelativeTime(1000000000);
		$this->assertEquals('11 years ago', $result);
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

