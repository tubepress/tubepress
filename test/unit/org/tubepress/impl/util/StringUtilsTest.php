<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/util/StringUtils.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_util_StringUtilsTest extends TubePressUnitTest
{
	function testCanReplaceFirstOnlyFirstOccurence()
	{
		$this->assertEquals("zxx", org_tubepress_impl_util_StringUtils::replaceFirst("x", "z", "xxx"));
	}
}
?>
