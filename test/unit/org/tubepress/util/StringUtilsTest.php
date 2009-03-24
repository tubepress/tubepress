<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/util/StringUtils.class.php';

class org_tubepress_util_StringUtilsTest extends PHPUnit_Framework_TestCase
{
	function testCanReplaceFirstOnlyFirstOccurence()
	{
		$this->assertEquals("zxx", org_tubepress_util_StringUtils::replaceFirst("x", "z", "xxx"));
	}
}
?>