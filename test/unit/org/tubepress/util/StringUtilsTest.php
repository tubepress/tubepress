<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/util/StringUtils.class.php';
require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_util_StringUtilsTest extends TubePressUnitTest
{
	function testCanReplaceFirstOnlyFirstOccurence()
	{
		$this->assertEquals("zxx", org_tubepress_util_StringUtils::replaceFirst("x", "z", "xxx"));
	}
}
?>
