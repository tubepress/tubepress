<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/util/TimeUtils.class.php';
require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_util_TimeUtilsTest extends TubePressUnitTest
{
	function testGetRelativeTimePast()
	{
		$result = org_tubepress_util_TimeUtils::getRelativeTime(1000000000);
		$this->assertEquals('9 years ago', $result);
	}

	function testSeconds2HumanTime()
	{
		$result = org_tubepress_util_TimeUtils::secondsToHumanTime(63);
		$this->assertEquals('1:03', $result);
	}

	function testRfc3339toUnixTime()
	{
		$result = org_tubepress_util_TimeUtils::rfc3339toUnixTime('1980-11-03T09:03:33.000-05:00');
		$this->assertEquals('342108213', $result);
	}
}
?>
