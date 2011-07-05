<?php

require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/util/TimeUtils.class.php';

class org_tubepress_impl_util_TimeUtilsTest extends TubePressUnitTest
{
	function testGetRelativeTimePast()
	{
		$result = org_tubepress_impl_util_TimeUtils::getRelativeTime(1000000000);
		$this->assertEquals('10 years ago', $result);
	}

	function testSeconds2HumanTime()
	{
		$result = org_tubepress_impl_util_TimeUtils::secondsToHumanTime(63);
		$this->assertEquals('1:03', $result);
	}

	function testRfc3339toUnixTime()
	{
		$result = org_tubepress_impl_util_TimeUtils::rfc3339toUnixTime('1980-11-03T09:03:33.000-05:00');
		$this->assertEquals('342108213', $result);
	}
}

